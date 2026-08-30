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

        // 1. Personnalisation du menu Gestion des Contenus & Blog
        $cmsMenu = $menu->getChild('sylius_cms');
        if (null !== $cmsMenu) {
            $cmsMenu->setLabel('Gestion des Contenus & Blog');

            // Accès direct aux Articles du Journal de l'Artisan
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

            // Masquage des sous-sections complexes et obsolètes
            $cmsMenu->removeChild('blocks');
            $cmsMenu->removeChild('templates');
            $cmsMenu->removeChild('collections');
            $cmsMenu->removeChild('media');
        }

        // 2. Masquage des sections e-commerce inutiles en Mode Vitrine Sur-Mesure
        $menu->removeChild('sales');
        $menu->removeChild('marketing');

        // 3. Personnalisation du menu Catalogue
        $catalogMenu = $menu->getChild('catalog');
        if (null !== $catalogMenu) {
            $catalogMenu->setLabel('Catalogue & Créations');

            $products = $catalogMenu->getChild('products');
            if (null !== $products) {
                $products->setLabel('Créations & Instruments');
            }

            $taxons = $catalogMenu->getChild('taxons');
            if (null !== $taxons) {
                $taxons->setLabel('Catégories & Univers');
            }

            // Masquage des sous-sections d'options et attributs complexes
            $catalogMenu->removeChild('attributes');
            $catalogMenu->removeChild('options');
            $catalogMenu->removeChild('associations');
        }
    }
}
