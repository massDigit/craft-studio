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
            $cmsMenu->setLabel('Gestion des Contenus & Blog');

            // Ajout de l'accès direct aux Articles du Journal de l'Artisan
            $cmsMenu
                ->addChild('blog_posts', [
                    'route' => 'app_admin_blog_post_index',
                ])
                ->setLabel('Articles du Journal (Blog)')
                ->setLabelAttribute('icon', 'tabler:article');

            $pages = $cmsMenu->getChild('pages');
            if (null !== $pages) {
                $pages->setLabel('Pages Legales & Footer');
            }

            // Masquage des sections complexes et obsoletes (Blocs, Modeles, Collections et Medias)
            $cmsMenu->removeChild('blocks');
            $cmsMenu->removeChild('templates');
            $cmsMenu->removeChild('collections');
            $cmsMenu->removeChild('media');
        }
    }
}
