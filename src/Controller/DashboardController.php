<?php

namespace App\Controller;

use App\Entity\Cvs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController{
    private $emi;
    
    public function __construct(EntityManagerInterface $em) {
        $this->emi = $em;
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(Request $req): Response {

        if(!$req->getSession()->get('user_id')){
            return $this->redirectToRoute('app_home');
        }

        $userId = $req->getSession()->get('user_id');
        $cvs = $this->emi->getRepository(Cvs::class)->findBy(['userId' => $userId]);

        // return $this->json([
        //     "cvs" => $cvs,
        // ]);

        // $cvHistory = [
        //     [
        //         'id' => '682d13df0f52b',
        //         'filename' => 'resume-may-2025.pdf',
        //         'uploaded_at' => new \DateTime('2025-05-18'),
        //         'score' => 82,
        //         'status' => 'good',
        //     ],
        //     [
        //         'id' => '682d140257d60',
        //         'filename' => 'developer-cv.pdf',
        //         'uploaded_at' => new \DateTime('2025-05-15'),
        //         'score' => 75,
        //         'status' => 'medium',
        //     ],
        //     [
        //         'id' => '682d1437e534f',
        //         'filename' => 'john-doe-resume.pdf',
        //         'uploaded_at' => new \DateTime('2025-05-10'),
        //         'score' => 65,
        //         'status' => 'low',
        //     ],
        // ];
        
        return $this->render('dashboard/index.html.twig', [
            'cv_history' => $cvs,
        ]);
    }
}