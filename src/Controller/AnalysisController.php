<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class AnalysisController extends AbstractController
{
    #[Route('/analysis/{id}', name: 'app_analysis')]
    public function show(string $id): Response {
            $cvData = [
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
            ];

        return $this->render('analysis/show.html.twig', [
            'id' => $id,
            'cvData' => $cvData
        ]);
    }
}