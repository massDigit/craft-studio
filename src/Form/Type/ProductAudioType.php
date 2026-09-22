<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Product\ProductAudio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductAudioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'ztc.form.product_audio.label',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'ztc.product_audio.label.not_blank']),
                ],
                'attr' => [
                    'placeholder' => 'Ex: Démonstration en Ré Majeur',
                ],
            ])
            ->add('file', FileType::class, [
                'label' => 'ztc.form.product_audio.file',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '15M',
                    ]),
                ],
            ])
            ->add('isPrimary', CheckboxType::class, [
                'label' => 'ztc.form.product_audio.is_primary',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductAudio::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'ztc_product_audio';
    }
}
