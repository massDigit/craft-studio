<?php

declare(strict_types=1);

namespace App\Form\Extension;

use Sylius\CmsPlugin\Form\Type\PageType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [PageType::class];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'help' => '💡 Guide : Pour un Article de Blog, associez un Média et cochez la collection "blog". Pour une Page Légale, cochez la collection "footer_menu".',
        ]);
    }
}
