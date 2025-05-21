<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class OpenRouterController extends AbstractController
{
    #[Route('/AiAnalyze', name: 'ai_analyze', methods: ['POST'])]
    public function analyzeCV(Request $request): JsonResponse
    {
        // Get CV text from request
        $data = json_decode($request->getContent(), true);
        $cvText = $data['cv_text'] ?? null;
        
        if (!$cvText) {
            return $this->json(['success' => false, 'error' => 'No CV text provided'], 400);
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
                        ], JSON_PRETTY_PRINT) . "\n\nCV Text:\n\n" . $cvText
                    ]
                ],
                'response_format' => [
                    'type' => 'json_object'
                ]
            ]
        ]);

        $aiResult = $response->toArray(false);
        $analysisContent = $aiResult['choices'][0]['message']['content'] ?? null;
        // $analysis = json_decode($analysisContent, true);
        return $this->json([
            'content' => $cvText,
            'success' => true,
            'analysis' => $analysisContent
        ]);
        
        // try {
        //     $aiResult = $response->toArray(false);
        //     $analysisContent = $aiResult['choices'][0]['message']['content'] ?? null;
            
        //     // Parse the JSON content from AI response (it should be already in JSON format)
        //     $analysis = json_decode($analysisContent, true);
            
        //     if (!$analysis) {
        //         // If JSON parsing failed, return a fallback structure
        //         $analysis = [
        //             'candidate' => [
        //                 'name' => 'Unknown',
        //                 'email' => 'Unknown',
        //                 'phone' => 'Unknown',
        //                 'location' => 'Unknown'
        //             ],
        //             'sections' => [
        //                 'education' => [
        //                     'score' => 0,
        //                     'improvements' => ['Could not analyze education section'],
        //                     'quality' => [
        //                         'degreeLevel' => 'Unknown',
        //                         'fieldMatch' => 'Unknown',
        //                         'relevance' => 'Unknown',
        //                         'status' => "Unknown"
        //                     ]
        //                 ],
        //                 'experience' => [
        //                     'score' => 0,
        //                     'improvements' => ['Could not analyze experience section'],
        //                     'level' => [
        //                         'years' => 0,
        //                         'requiredYears' => 0,
        //                         'domainMatch' => 'Unknown',
        //                         'status' => "Unknown"
        //                     ]
        //                 ],
        //                 'skills' => [
        //                     'score' => 0,
        //                     'improvements' => ['Could not analyze skills section'],
        //                     'match' => [
        //                         'percentage' => 0,
        //                         'matched' => [],
        //                         'missing' => [],
        //                         'status' => 'unknown',
        //                     ],
        //                     'comparison' => [
        //                         'categories' => [],
        //                         'candidate' => [],
        //                         'required' => [],
        //                     ]
        //                 ],
        //                 'languages' => [
        //                     'score' => 0,
        //                     'improvements' => [],
        //                     'proficiency' => [
        //                         'languages' => [],
        //                         'levels' => [],
        //                         'status' => "unknown"
        //                     ]
        //                 ],
        //                 'softSkills' => [
        //                     'score' => 0,
        //                     'detected' => [],
        //                     'improvements' => ['Could not analyze soft skills'],
        //                     'status' => 'unknown',
        //                 ]
        //             ],
        //             'aiSuggestions' => [],
        //             'keywordCoverage' => [
        //                 'found' => [],
        //                 'missing' => []
        //             ],
        //             'overallScore' => 0,
        //             'overallTips' => 'Unable to properly analyze the CV'
        //         ];
        //     }
            
        //     return $this->json([
        //         'success' => true,
        //         'analysis' => $analysis
        //     ]);
            
        // } catch (\Exception $e) {
        //     return $this->json([
        //         'success' => false,
        //         'error' => 'Failed to analyze CV: ' . $e->getMessage()
        //     ], 500);
        // }
    }
}
