<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Annotation\Route;

class OpenRouterController extends AbstractController
{
    #[Route('/chat', name: 'chat_endpoint')]
    public function chat(): JsonResponse{
        $client = HttpClient::create();
        $response = $client->request('POST', "https://openrouter.ai/api/v1/chat/completions", [
            'headers' => [
                'Authorization' => 'Bearer ',
                'Content-Type' => 'application/json',
                'HTTP-Referer' => 'wessinsight',
                'X-Title' => 'Symfony Chatbot',
            ],
            'json' => [
                // 'model' => "nousresearch/deephermes-3-mistral-24b-preview:free",
                'model' => "google/gemma-3-27b-it:free",
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => ''
                    ],
                    [
                        'role' => 'user',
                        'content' => 'Hello, who are you?'
                    ],
                ],
            ],
        ]);
        $data = $response->toArray(false);
        return $this->json([
            'response' => $data['choices'][0]['message']['content'] ?? 'No reply',
        ]);
    }
}
