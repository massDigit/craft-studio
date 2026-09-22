<?php

declare(strict_types=1);

namespace App\Form\Extension;

use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\Type\ProductTechnicalSheetItemType;

class ProductTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('technicalSheetItems', CollectionType::class, [
            'entry_type' => ProductTechnicalSheetItemType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'label' => 'app.form.product.technical_sheet_items',
            'button_add_label' => 'app.form.product.add_technical_sheet_item',
        ]);

        $builder->add('audios', CollectionType::class, [
            'entry_type' => \App\Form\Type\ProductAudioType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'label' => 'app.form.product.audios',
            'button_add_label' => 'Ajouter un fichier audio',
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [ProductType::class];
    }
}
