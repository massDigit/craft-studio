<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Taxonomy\FaqItem;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class FaqItemType extends AbstractResourceType
{
    public function __construct()
    {
        parent::__construct(FaqItem::class, ['sylius']);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('position', IntegerType::class, [
                'label' => 'Position',
                'required' => false,
                'empty_data' => '0',
                'attr' => [
                    'class' => 'faq-input-position form-control',
                    'min' => 0,
                ],
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => FaqItemTranslationType::class,
                'label' => 'Traductions',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_faq_item';
    }
}
