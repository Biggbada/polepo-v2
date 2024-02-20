<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\PostCategory;
use App\Form\PostCategoryType;
use App\Repository\FileCategoryRepository;
use App\Repository\PostCategoryRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function new(Request $request, EntityManagerInterface $entityManager, FileCategoryRepository $fileCategoryRepository, SluggerInterface $slugger, FileUploader $fileUploader): Response
    {
        $postCategory = new PostCategory();
        $form = $this->createForm(PostCategoryType::class, $postCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $featuredImg */
            $featuredImg = $form->get('featuredImg')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($featuredImg) {
                $originalFilename = $featuredImg->getClientOriginalName();
                $fileCategory = $fileCategoryRepository->find(1);
                $file = new File();
                $file->setCategory($fileCategory);

                $targetDirectory = $this->getParameter('files_directory').'/'.$file->getCategory()->getPath();


                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $extension = $featuredImg->guessExtension();
                $newFilename = $safeFilename.'.'.$extension;
                $file->setName($newFilename);
                $fileUrl = $fileUploader->upload($featuredImg, $targetDirectory);

                $file->setUrl($fileUrl);
                $file->setExtension($extension);
                $postCategory->setFeaturedImg($file);
                if ($this->getUser()) {
                    $file->setUploadedBy($this->getUser());
                }
                $entityManager->persist($file);



            }

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

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_post_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PostCategory $postCategory, EntityManagerInterface $entityManager, FileCategoryRepository $fileCategoryRepository, SluggerInterface $slugger, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(PostCategoryType::class, $postCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $featuredImg */
            $featuredImg = $form->get('featuredImg')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($featuredImg) {
                $originalFilename = $featuredImg->getClientOriginalName();
                $fileCategory = $fileCategoryRepository->find(1);
                $file = new File();
                $file->setCategory($fileCategory);

                $targetDirectory = $this->getParameter('files_directory').'/'.$file->getCategory()->getPath();


                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $extension = $featuredImg->guessExtension();
                $newFilename = $safeFilename.'.'.$extension;
                $file->setName($newFilename);
                $fileUrl = $fileUploader->upload($featuredImg, $targetDirectory);

                $file->setUrl($fileUrl);
                $file->setExtension($extension);
                $postCategory->setFeaturedImg($file);
                if ($this->getUser()) {
                    $file->setUploadedBy($this->getUser());
                }
                $entityManager->persist($file);



            }

            $entityManager->flush();

            return $this->redirectToRoute('app_post_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post_category/edit.html.twig', [
            'post_category' => $postCategory,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
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
