<?php

declare(strict_types=1);

namespace App\Form\Extension;

use Sylius\Bundle\ProductBundle\Form\Type\ProductTranslationType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ProductTranslationTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $form = $event->getForm();
            $localeCode = (string) $form->getName();

            if ($form->has('description')) {
                $config = $form->get('description')->getConfig();
                /** @var array<string, mixed> $existingOptions */
                $existingOptions = $config->getOptions();
                /** @var array<string, mixed> $existingAttr */
                $existingAttr = is_array($existingOptions['attr'] ?? null) ? $existingOptions['attr'] : [];

                $currentController = isset($existingAttr['data-controller']) && is_string($existingAttr['data-controller'])
                    ? $existingAttr['data-controller']
                    : '';

                if ('fr_FR' === $localeCode || 'fr' === $localeCode || str_contains($localeCode, 'fr')) {
                    $existingAttr['data-controller'] = trim($currentController . ' ai-description');
                } else {
                    $existingAttr['data-controller'] = trim($currentController . ' ai-translator');
                    $existingAttr['data-ai-translator-locale-value'] = str_starts_with($localeCode, 'en') ? 'en' : $localeCode;
                }

                $existingOptions['attr'] = $existingAttr;
                $form->add('description', TextareaType::class, $existingOptions);
            }
        });
    }

    public static function getExtendedTypes(): iterable
    {
        return [ProductTranslationType::class];
    }
}
