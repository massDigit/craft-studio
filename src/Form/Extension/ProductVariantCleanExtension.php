<?php

declare(strict_types=1);

namespace App\Form\Extension;

use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductVariantCleanExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // On retire les champs du formulaire pour que Symfony n'essaie pas de les soumettre à null
        // et qu'ils gardent leur valeur d'origine en base de données.
        $builder->remove('tracked');
        $builder->remove('onHand');
        $builder->remove('taxCategory');
        $builder->remove('shippingCategory');
        $builder->remove('weight');
        $builder->remove('width');
        $builder->remove('height');
        $builder->remove('depth');
        
        // Mollie fields
        $builder->remove('recurring');
        $builder->remove('times');
        $builder->remove('interval');
    }

    public static function getExtendedTypes(): array
    {
        return [ProductVariantType::class];
    }
}
