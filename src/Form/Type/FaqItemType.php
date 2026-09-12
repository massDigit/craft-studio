<?php

declare(strict_types=1);

namespace App\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use App\Entity\Taxonomy\FaqItem;

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
