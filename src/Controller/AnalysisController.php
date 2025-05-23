<?php

namespace App\Controller;

use App\Entity\Cvs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class AnalysisController extends AbstractController{
    private $emi;
    
    public function __construct(EntityManagerInterface $em) {
        $this->emi = $em;
    }

    #[Route('/analysis/{id}', name: 'app_analysis')]
    public function show(string $id, Request $req): Response {

        if(!$req->getSession()->get('user_id')){
            return $this->redirectToRoute('app_home');
        }
        
        $cv = $this->emi->getRepository(Cvs::class)->findOneBy(['id' => $id]);

        if (!$cv) {
            return $this->redirectToRoute('app_dashboard');
        }

        if($cv->getUserId() != $req->getSession()->get('user_id')){
            return $this->redirectToRoute('app_dashboard');
        }

        // return $this->json([
        //     // 'cv' => $cv,
        //     'id' => $cv->getId(),
        //     'cvData' => $cv->getAiResult(),
        //     'fileName' => $cv->getFileName(),
        // ]);

        return $this->render('analysis/show.html.twig', [
            // 'id' => $cv[0]->id,
            // 'cvData' => $cv[0]->aiResult
            'id' => $cv->getId(),
            'cvData' => $cv->getAiResult()[0],
            'fileName' => $cv->getFileName(),
        ]);
    }

    #[Route('/deleteCv/{id}', name: 'app_delete_cv', methods: ['POST'])]
    public function delete(string $id, Request $req): Response {
        if (!$req->getSession()->get('user_id')) {
            return $this->redirectToRoute('app_home');
        }

        $cv = $this->emi->getRepository(Cvs::class)->findOneBy(['id' => $id]);

        if (!$cv || $cv->getUserId() != $req->getSession()->get('user_id')) {
            return $this->redirectToRoute('app_dashboard');
        }

        $this->emi->remove($cv);
        $this->emi->flush();

        return $this->redirectToRoute('app_dashboard');
    }
}