<?php

declare(strict_types=1);

namespace App\Form\Type\Ai;

use App\Entity\Ai\AiPrompt;
use App\Enum\Ai\AiPromptType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AiPromptTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'Libellé du Prompt',
                'attr' => [
                    'placeholder' => 'ex: Génération FAQ standard (Bambou)',
                ],
            ])
            ->add('type', EnumType::class, [
                'class' => AiPromptType::class,
                'choice_label' => fn (AiPromptType $choice) => $choice->getLabel(),
                'label' => 'Type de Prompt',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu du Système Prompt',
                'attr' => [
                    'rows' => 14,
                    'placeholder' => 'Tu es le rédacteur officiel de...',
                    'class' => 'font-monospace',
                ],
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Définir comme prompt actif pour ce type',
                'required' => false,
                'help' => 'Si activé, tout autre prompt du même type sera automatiquement désactivé.',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AiPrompt::class,
        ]);
    }
}
