<?php

namespace App\Controller;

use App\Service\PdfParserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class HomeController extends AbstractController
{
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
        $file = $request->files->get('cv_file');
        
        if (!$file) {
            return $this->json(['success' => false, 'error' => 'No file uploaded']);
        }
        
        if ($file->getMimeType() !== 'application/pdf') {
            return $this->json(['success' => false, 'error' => 'Please upload a PDF file']);
        }
        
        try {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
            
            $extractedText = $pdfParserService->parseUploadedPdf($file);
            
            return $this->json([
                'success' => true,
                'text' => $extractedText,
                'filename' => $file->getClientOriginalName(),
                'stored_filename' => $newFilename
            ]);
            
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => 'An error occurred while processing your PDF: ' . $e->getMessage()]);
        }
    }
}