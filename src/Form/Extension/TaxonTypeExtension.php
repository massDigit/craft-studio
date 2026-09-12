<?php

declare(strict_types=1);

namespace App\Form\Extension;

use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\Type\FaqItemType;

class TaxonTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('faqItems', CollectionType::class, [
            'entry_type' => FaqItemType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'label' => 'Foire Aux Questions',
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [TaxonType::class];
    }
}
