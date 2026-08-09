<?php

declare(strict_types=1);

namespace App\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'sylius.menu.admin.main', priority: -10)]
class AdminMenuListener
{
    public function __invoke(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $cmsMenu = $menu->getChild('sylius_cms');
        if (null !== $cmsMenu) {
            $cmsMenu->setLabel('📰 CMS & Journal de l\'Artisan');

            $collections = $cmsMenu->getChild('collections');
            if (null !== $collections) {
                $collections->setLabel('📁 Emplacements (Footer / Blog)');
            }

            $pages = $cmsMenu->getChild('pages');
            if (null !== $pages) {
                $pages->setLabel('📄 Pages Éditoriales & Blog');
            }

            $blocks = $cmsMenu->getChild('blocks');
            if (null !== $blocks) {
                $blocks->setLabel('🧱 Blocs de Contenu');
            }

            $media = $cmsMenu->getChild('media');
            if (null !== $media) {
                $media->setLabel('🖼️ Médiathèque & Visuels');
            }
        }
    }
}
