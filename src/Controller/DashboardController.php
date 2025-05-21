<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(Request $req): Response {

        if(!$req->getSession()->get('user_id')){
            return $this->redirectToRoute('app_home');
        }

        $cvHistory = [
            [
                'id' => '682d13df0f52b',
                'filename' => 'resume-may-2025.pdf',
                'uploaded_at' => new \DateTime('2025-05-18'),
                'score' => 82,
                'status' => 'good',
            ],
            [
                'id' => '682d140257d60',
                'filename' => 'developer-cv.pdf',
                'uploaded_at' => new \DateTime('2025-05-15'),
                'score' => 75,
                'status' => 'medium',
            ],
            [
                'id' => '682d1437e534f',
                'filename' => 'john-doe-resume.pdf',
                'uploaded_at' => new \DateTime('2025-05-10'),
                'score' => 65,
                'status' => 'low',
            ],
        ];
        
        return $this->render('dashboard/index.html.twig', [
            'cv_history' => $cvHistory,
        ]);
    }
}