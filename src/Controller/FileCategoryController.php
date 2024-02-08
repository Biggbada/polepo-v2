<?php

namespace App\Controller;

use App\Entity\FileCategory;
use App\Form\FileCategoryType;
use App\Repository\FileCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/file/category')]
class FileCategoryController extends AbstractController
{
    #[Route('/', name: 'app_file_category_index', methods: ['GET'])]
    public function index(FileCategoryRepository $fileCategoryRepository): Response
    {
        return $this->render('file_category/index.html.twig', [
            'file_categories' => $fileCategoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_file_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $fileCategory = new FileCategory();
        $form = $this->createForm(FileCategoryType::class, $fileCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($fileCategory);
            $entityManager->flush();

            return $this->redirectToRoute('app_file_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('file_category/new.html.twig', [
            'file_category' => $fileCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_file_category_show', methods: ['GET'])]
    public function show(FileCategory $fileCategory): Response
    {
        return $this->render('file_category/show.html.twig', [
            'file_category' => $fileCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_file_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FileCategory $fileCategory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FileCategoryType::class, $fileCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_file_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('file_category/edit.html.twig', [
            'file_category' => $fileCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_file_category_delete', methods: ['POST'])]
    public function delete(Request $request, FileCategory $fileCategory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fileCategory->getId(), $request->request->get('_token'))) {
            $entityManager->remove($fileCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_file_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
