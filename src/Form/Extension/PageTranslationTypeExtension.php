<?php

declare(strict_types=1);

namespace App\Form\Extension;

use Sylius\CmsPlugin\Form\Type\Translation\PageTranslationType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class PageTranslationTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [PageTranslationType::class];
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('metaDescription', null, [
            'label' => 'Contenu Juridique / Texte de la Page Légale',
            'attr' => [
                'data-controller' => 'wysiwyg',
                'rows' => 12,
            ],
            'required' => false,
        ]);
    }
}
