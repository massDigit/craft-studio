<?php

declare(strict_types=1);

namespace App\Form\Extension;

use App\Entity\Taxonomy\Taxon;
use App\Form\Type\FaqItemType;
use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
            $taxon = $event->getData();
            if ($taxon instanceof Taxon) {
                $taxon->normalizeFaqPositions();
            }
        });
    }

    public static function getExtendedTypes(): iterable
    {
        return [TaxonType::class];
    }
}
