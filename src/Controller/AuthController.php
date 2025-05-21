<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthController extends AbstractController
{
    private $emi;
    
    public function __construct(EntityManagerInterface $em) {
        $this->emi = $em;
    }

    #[Route('/login', name: 'login_route')]
    public function login(Request $req): Response{
        if($req->getSession()->get('user_id')){
            return new JsonResponse(["success"=>true]);
        }
        
        $error = null;
        
        if($req->isMethod('POST')){
            $email = $req->request->get('email');
            $password = $req->request->get('password');
            
            if (!$email || !$password) {
                $error = 'Please provide both email and password';
            } else {
                $user = $this->emi->getRepository(User::class)->findOneBy(['email' => $email]);
                
                if (!$user || !password_verify($password, $user->getPassword())) {
                    $error = 'Invalid credentials. Please check your email and password.';
                } else {
                    $req->getSession()->set('user_id', $user->getId());
                    $req->getSession()->set('user_name', $user->getUsername());
                    
                    if ($req->request->get('remember_me')) {
                        $req->getSession()->set('session_lifetime', 'extended');
                    }
                    
                    if ($req->headers->get('X-Requested-With') === 'XMLHttpRequest') {
                        return new JsonResponse(["success" => true]);
                    }
                    
                    return $this->redirectToRoute('app_dashboard');
                }
            }
            
            if ($req->headers->get('X-Requested-With') === 'XMLHttpRequest') {
                return new JsonResponse([
                    "success" => false,
                    "error" => $error
                ]);
            }
        }
        
        return $this->render('home/index.html.twig', [
            'error' => $error
        ]);
    }
    
    #[Route('/signup', name: 'signup_route')]
    public function signup(Request $req): Response{
        if($req->getSession()->get('user_id')){
            return new JsonResponse(["success"=>true]);
        }
        
        $error = null;
        
        if($req->isMethod('POST')){
            $username = $req->request->get('username');
            $email = $req->request->get('email');
            $password = $req->request->get('password');
            $confirmPassword = $req->request->get('confirm_password');
            
            if (!$username || !$email || !$password || !$confirmPassword) {
                $error = 'All fields are required';
            } elseif (strlen($username) < 3) {
                $error = 'Username should be at least 3 characters';
            } elseif (strlen($password) < 6) {
                $error = 'Password should be at least 6 characters';
            } elseif ($password !== $confirmPassword) {
                $error = 'Passwords do not match';
            } elseif ($this->emi->getRepository(User::class)->findOneBy(['email' => $email])) {
                $error = 'Email already in use';
            } elseif ($this->emi->getRepository(User::class)->findOneBy(['username' => $username])) {
                $error = 'Username already in use';
            } else {
                $user = new User();
                $user->setUsername($username);
                $user->setEmail($email);
                $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
                
                $this->emi->persist($user);
                $this->emi->flush();

                $this->addFlash('success', 'Registration successful! You can now login.');

                if ($req->headers->get('X-Requested-With') === 'XMLHttpRequest') {
                    $req->getSession()->set('user_id', $user->getId());
                    $req->getSession()->set('user_name', $user->getUsername());
                    return new JsonResponse(["success"=>true]);
                }
                
            }
            
            if ($req->headers->get('X-Requested-With') === 'XMLHttpRequest') {
                return new JsonResponse([
                    "success" => false,
                    "error" => $error
                ]);
            }
        }
        
        return $this->render('home/index.html.twig', [
            'error' => $error
        ]);
    }

    #[Route('/logout', name: 'logout_route')]
    public function logout(Request $req): Response{
        $req->getSession()->remove('user_id');
        $req->getSession()->remove('user_name');
        return $this->redirectToRoute('app_home');
    }
}