<?php
namespace App\Twig;

use App\Repository\FileCategoryRepository;
use App\Repository\PostCategoryRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private PostCategoryRepository $postCategoryRepository;
    private FileCategoryRepository $fileCategoryRepository;

    public function __construct(PostCategoryRepository $postCategoryRepository, FileCategoryRepository $fileCategoryRepository)
    {
        $this->postCategoryRepository = $postCategoryRepository;
        $this->fileCategoryRepository = $fileCategoryRepository;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getPostCategories', [$this, 'getPostCategories']),
            new TwigFunction('getFileCategories', [$this, 'getFileCategories'])

        ];
    }

    public function getPostCategories()
    {
        return $this->postCategoryRepository->findAll();
    }

    public function getFileCategories()
    {
        return $this->fileCategoryRepository->findAll();
    }
}
