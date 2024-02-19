<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Post;
use App\Form\PostType;
use App\Repository\FileCategoryRepository;
use App\Repository\PostCategoryRepository;
use App\Repository\PostRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, PostCategoryRepository $categoryRepository, SluggerInterface $slugger, FileUploader $fileUploader, FileCategoryRepository $fileCategoryRepository): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $post->setAuthor($this->getUser());
            }
            $postCategory = $categoryRepository->find(1);
            $post->setCreatedAt(new \DateTimeImmutable('now'));
            $post->setPostCategory($postCategory);

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
                $post->setFeaturedImg($file);
                if ($this->getUser()) {
                    $file->setUploadedBy($this->getUser());
                }
                $entityManager->persist($file);



            }

            /** @var UploadedFile $attachment */
            $attachment = $form->get('attachment')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($attachment) {
                $originalFilename = $attachment->getClientOriginalName();
                $fileCategory = $fileCategoryRepository->find(1);
                $file = new File();
                $file->setCategory($fileCategory);

                $targetDirectory = $this->getParameter('files_directory').'/'.$file->getCategory()->getPath();


                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $extension = $attachment->guessExtension();
                $newFilename = $safeFilename.'.'.$extension;
                $file->setName($newFilename);
                $fileUrl = $fileUploader->upload($attachment, $targetDirectory);

                $file->setUrl($fileUrl);
                $file->setExtension($extension);
                $file->setPost($post);
                if ($this->getUser()) {
                    $file->setUploadedBy($this->getUser());
                }
                $entityManager->persist($file);



            }
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager, SluggerInterface $slugger, FileUploader $fileUploader, FileCategoryRepository $fileCategoryRepository): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form->get('attachment')->getData();

            // this condition is needed because the 'uploadedFile' field is not required
            // so the file must be processed only when a file is uploaded
            if ($uploadedFile)
            {
                $file = new File();
                $fileCategory = $fileCategoryRepository->find(1);
                $file->setCategory($fileCategory);
                $targetDirectory = $this->getParameter('files_directory').'/'.$file->getCategory()->getPath();
                $extension = $uploadedFile->attachment->guessExtension();
                $originalFilename = pathinfo($uploadedFile->attachment->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'.'.$extension;
                $uploadedFile->setName($newFilename);
                $fileUrl = $fileUploader->upload($uploadedFile, $targetDirectory);

                $file->setUrl($fileUrl);
                $file->setExtension($extension);
                $file->setPost($post);

            }

            $entityManager->persist($file);

            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
