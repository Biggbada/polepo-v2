<?php

namespace App\Controller;

use App\Entity\PostCategory;
use App\Form\PostCategoryType;
use App\Repository\PostCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post/category')]
class PostCategoryController extends AbstractController
{
    #[Route('/', name: 'app_post_category_index', methods: ['GET'])]
    public function index(PostCategoryRepository $postCategoryRepository): Response
    {
        return $this->render('post_category/index.html.twig', [
            'post_categories' => $postCategoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_post_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $postCategory = new PostCategory();
        $form = $this->createForm(PostCategoryType::class, $postCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($postCategory);
            $entityManager->flush();

            return $this->redirectToRoute('app_post_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post_category/new.html.twig', [
            'post_category' => $postCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_category_show', methods: ['GET'])]
    public function show(PostCategory $postCategory): Response
    {
        return $this->render('post_category/show.html.twig', [
            'post_category' => $postCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PostCategory $postCategory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostCategoryType::class, $postCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_post_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post_category/edit.html.twig', [
            'post_category' => $postCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_category_delete', methods: ['POST'])]
    public function delete(Request $request, PostCategory $postCategory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$postCategory->getId(), $request->request->get('_token'))) {
            $entityManager->remove($postCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
