<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Product\ProductTechnicalSheetItemTranslation;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductTechnicalSheetItemTranslationType extends AbstractResourceType
{
    public function __construct()
    {
        parent::__construct(ProductTechnicalSheetItemTranslation::class, ['sylius']);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'app.form.technical_sheet.title',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'app.form.technical_sheet.description',
                'required' => false,
                'attr' => ['rows' => 4],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_product_technical_sheet_item_translation';
    }
}
