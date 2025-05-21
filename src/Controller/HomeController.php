<?php

namespace App\Controller;

use App\Entity\Cvs;
use App\Service\PdfParserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController extends AbstractController{
    private $emi;
    
    public function __construct(EntityManagerInterface $em) {
        $this->emi = $em;
    }

    #[Route('/', name: 'app_home')]
    public function index(Request $req): Response{
        if($req->getSession()->get('user_id')){
            return $this->redirectToRoute('app_dashboard');
        }
        return $this->render('home/index.html.twig');
    }
    
    #[Route('/upload', name: 'app_upload_pdf', methods: ['POST'])]
    public function upload(
        Request $request, 
        SluggerInterface $slugger, 
        PdfParserService $pdfParserService
    ): Response {
        try {
            $file = $request->files->get('cv_file');
            
            if (!$file) {
                return $this->json(['success' => false, 'error' => 'No file uploaded']);
            }
            
            if ($file->getMimeType() !== 'application/pdf') {
                return $this->json(['success' => false, 'error' => 'Please upload a PDF file']);
            }
            
            $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            try {
                $extractedText = $pdfParserService->parseUploadedPdf($file);
            } catch (\Exception $e) {
                return $this->json([
                    'success' => false, 
                    'error' => 'Failed to extract text from PDF: ' . $e->getMessage()
                ]);
            }
            
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
            
            try {
                $file->move(
                    $uploadDir,
                    $newFilename
                );
            } catch (\Exception $e) {
                return $this->json([
                    'success' => false, 
                    'error' => 'Failed to save PDF file: ' . $e->getMessage()
                ]);
            }

            $client = HttpClient::create();
            $response = $client->request('POST', "https://openrouter.ai/api/v1/chat/completions", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $_ENV['APP_OPEN_ROUTER_KEY'],
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => 'wessinsight',
                    'X-Title' => 'WessInsight CV Analysis',
                ],
                'json' => [
                    'model' => "nousresearch/deephermes-3-mistral-24b-preview:free",
                    // 'model' => "google/gemma-3-27b-it:free",
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a professional CV analyzer. Given a CV text, analyze it and return a structured JSON response with candidate information, sections analysis, and overall score.'
                        ],
                        [
                            'role' => 'user',
                            'content' => "Analyze this CV and return a structured JSON with the following structure: \n\n" . json_encode([
                                'candidate' => [
                                    'name' => 'John Doe',
                                    'email' => 'johndoe@example.com',
                                    'phone' => '+1234567890',
                                    'location' => 'New York, USA'
                                ],
                                'sections' => [
                                    'education' => [
                                        'score' => 85,
                                        'improvements' => ['Consider pursuing advanced certifications'],
                                        'quality' => [
                                            'degreeLevel' => 'Bachelor',
                                            'fieldMatch' => 'High',
                                            'relevance' => 'Relevant',
                                            'status' => "dd"
                                        ]
                                    ],
                                    'experience' => [
                                        'score' => 70,
                                        'improvements' => ['Highlight leadership roles'],
                                        'level' => [
                                            'years' => 5,
                                            'requiredYears' => 3,
                                            'domainMatch' => 'Strong',
                                            'status' => "zeaz"
                                        ]
                                    ],
                                    'skills' => [
                                        'score' => 80,
                                        'improvements' => ['Add more technical skills'],
                                        'match' => [
                                            'percentage' => 75,
                                            'matched' => ['Python', 'SQL', 'Project Management'],
                                            'missing' => ['Cloud Computing', 'Machine Learning'],
                                            'status' => 'medium',
                                        ],
                                        'comparison' => [
                                            'categories' => ['Technical', 'Soft Skills', 'Tools', 'Domain Knowledge'],
                                            'candidate' => [70, 80, 60, 75],
                                            'required' => [80, 70, 75, 85],
                                        ]
                                    ],
                                    'languages' => [
                                        'score' => 90,
                                        'improvements' => [],
                                        'proficiency' => [
                                            'languages' => ['English', 'Spanish'],
                                            'levels' => ['Native', 'Intermediate'],
                                            'status' => "esfsdf"
                                        ]
                                    ],
                                    'softSkills' => [
                                        'score' => 75,
                                        'detected' => ['Teamwork', 'Communication'],
                                        'improvements' => ['Focus on problem-solving skills'],
                                        'status' => 'medium',
                                    ]
                                ],
                                'aiSuggestions' => [
                                    ['section' => 'Format', 'status' => 'medium', 'message' => 'Ensure consistent formatting throughout the CV'],
                                    ['section' => 'Content', 'status' => 'medium', 'message' => 'Include quantifiable achievements'],
                                    ['section' => 'Keywords', 'status' => 'medium', 'message' => 'Incorporate more industry-specific keywords'],
                                ],
                                'keywordCoverage' => [
                                    'found' => ['Python', 'SQL', 'Leadership'],
                                    'missing' => ['Cloud Computing', 'Machine Learning']
                                ],
                                'overallScore' => 78,
                                'overallTips' => 'Focus on improving technical skills and adding measurable achievements.'
                            ], JSON_PRETTY_PRINT) . "\n\nCV Text:\n\n" . $extractedText
                        ]
                    ],
                    'response_format' => [
                        'type' => 'json_object'
                    ]
                ],
            ]);

            $aiResult = $response->toArray(false);
            $analysisContent = $aiResult['choices'][0]['message']['content'] ?? null;
            $analysis = json_decode($analysisContent, true);

            if($analysisContent==null){
                return $this->json([
                    'success' => false, 
                    'error' => 'Failed to analyze the CV',
                    "message" => "Something went wrong, please try again later."
                ]);
            }

            // return $this->json([
            //     'success' => true,
            //     'analysis' => $analysis
            // ]);

            // $user = new User();
            // $user->setUsername($username);
            // $user->setEmail($email);
            // $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            
            // $this->emi->persist($user);
            // $this->emi->flush();

            $cv = new Cvs();
            $cv->setUserId($request->getSession()->get('user_id'));
            $cv->setFileName($file->getClientOriginalName());
            $cv->setCvContent($extractedText);
            $cv->setAiResult([$analysis]);
            $this->emi->persist($cv);
            $this->emi->flush();

            return $this->redirectToRoute('app_analysis', [
                'id' => $cv->getId(),
            ]);


            return $this->json([
                'success' => true,
                'text' => $extractedText,
                'filename' => $file->getClientOriginalName(),
                'stored_filename' => $newFilename,
                'user_id' => $request->getSession()->get('user_id'),
                'analysis' => $analysis,
                // "ai_result" => $aiResult,
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false, 
                'error' => 'An error occurred while processing your PDF: ' . $e->getMessage(),
                "message" => "Something went wrong, please try again later.",
            ]);
        }
    }
}