<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class HeadController extends AbstractController
{
    /**
     * Route('/get', name: 'get', methods: ['GET'])]
     *return JsonResponse
     * */
    public function getffff(): string
    {
        return 'null';
    }
}