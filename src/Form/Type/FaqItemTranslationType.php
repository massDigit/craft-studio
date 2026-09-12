<?php

declare(strict_types=1);

namespace App\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Entity\Taxonomy\FaqItemTranslation;

class FaqItemTranslationType extends AbstractResourceType
{
    public function __construct()
    {
        parent::__construct(FaqItemTranslation::class, ['sylius']);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question', TextType::class, [
                'label' => 'Question',
                'required' => false,
                'attr' => ['class' => 'faq-input-question']
            ])
            ->add('answer', TextareaType::class, [
                'label' => 'Réponse',
                'required' => false,
                'attr' => ['class' => 'faq-input-answer', 'rows' => 3]
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_faq_item_translation';
    }
}
