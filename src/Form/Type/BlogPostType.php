<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Blog\BlogPost;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class BlogPostType extends AbstractResourceType
{
    /**
     * @param array<string> $validationGroups
     */
    public function __construct(
        string $dataClass = BlogPost::class,
        array $validationGroups = [],
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

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
            ->add('coverImageFile', FileType::class, [
                'label' => 'Téléverser l\'image de couverture (Fichier JPG, PNG, WebP)',
                'required' => false,
            ])
            ->add('excerpt', TextareaType::class, [
                'label' => 'Extrait / Chapeau court (apparaît sur la carte du blog)',
                'required' => false,
                'attr' => ['rows' => 3],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Corps de l\'article (Éditeur de texte riche)',
                'attr' => [
                    'data-controller' => 'wysiwyg',
                    'rows' => 12,
                ],
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
