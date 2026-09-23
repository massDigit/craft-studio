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
            ->add('topic', TextType::class, [
                'label' => 'Thématique / Rubrique (ex: L\'Art du Son, Savoir-Faire, L\'Atelier)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: L\'Art du Son',
                ],
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

        $builder->addEventListener(\Symfony\Component\Form\FormEvents::POST_SET_DATA, function (\Symfony\Component\Form\FormEvent $event): void {
            /** @var BlogPost|null $blogPost */
            $blogPost = $event->getData();
            $form = $event->getForm();
            if (!$blogPost instanceof BlogPost) {
                return;
            }

            $helpHtml = null;
            if ($blogPost->getCoverImage()) {
                $helpHtml = sprintf(
                    '<div class="mt-2 p-2 bg-light rounded border text-center" style="max-width: 320px;">' .
                    '<span class="small text-muted d-block mb-1">Image actuelle enregistrée :</span>' .
                    '<img src="/media/image/%s" class="img-fluid rounded" style="max-height: 140px; object-fit: cover;" alt="Aperçu">' .
                    '</div>',
                    htmlspecialchars($blogPost->getCoverImage(), ENT_QUOTES, 'UTF-8')
                );
            }

            $form->add('coverImageFile', FileType::class, [
                'label' => 'Téléverser l\'image de couverture (Fichier JPG, PNG, WebP)',
                'required' => false,
                'help' => $helpHtml,
                'help_html' => true,
            ]);
        });
    }

    public function getBlockPrefix(): string
    {
        return 'app_blog_post';
    }
}
