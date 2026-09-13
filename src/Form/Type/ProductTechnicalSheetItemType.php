<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Product\ProductTechnicalSheetItem;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductTechnicalSheetItemType extends AbstractResourceType
{
    public function __construct()
    {
        parent::__construct(ProductTechnicalSheetItem::class, ['sylius']);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('position', IntegerType::class, [
                'required' => false,
                'label' => 'app.form.technical_sheet.position',
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => ProductTechnicalSheetItemTranslationType::class,
                'label' => 'app.form.technical_sheet.translations',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_product_technical_sheet_item';
    }
}
