<?php

namespace App\Form;

use App\Entity\File;
use App\Entity\FileCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('attachment', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'label' => 'FIchier'
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'class' => FileCategory::class,
                'choice_label' => 'title',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => File::class,
        ]);
    }
}
