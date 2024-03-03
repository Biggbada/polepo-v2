<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'posts' => $postRepository->findAll()
        ]);
    }
    #[Route('/ambi', name: 'app_ambilight')]
    public function indexAmbi(PostRepository $postRepository): Response
    {
        return $this->render('tests-pages/index.html.twig', [
            'controller_name' => 'HomeController',
            'posts' => $postRepository->findAll()
        ]);
    }
}
