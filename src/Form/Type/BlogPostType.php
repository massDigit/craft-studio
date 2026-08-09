<?php

declare(strict_types=1);

namespace App\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class BlogPostType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'Code unique de l\'article',
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'article',
            ])
            ->add('slug', TextType::class, [
                'label' => 'Slug (URL)',
            ])
            ->add('coverImage', TextType::class, [
                'label' => 'Image de couverture (Nom du fichier dans public/media/image/)',
                'required' => false,
            ])
            ->add('excerpt', TextareaType::class, [
                'label' => 'Extrait / Chapeau court (apparaît sur la carte du blog)',
                'required' => false,
                'attr' => ['rows' => 3],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Corps de l\'article (Contenu riche)',
                'attr' => ['rows' => 10],
            ])
            ->add('published', CheckboxType::class, [
                'label' => 'Publier immédiatement cet article sur le site',
                'required' => false,
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'app_blog_post';
    }
}
