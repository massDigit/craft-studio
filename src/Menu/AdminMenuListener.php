<?php

declare(strict_types=1);

namespace App\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $customerSubmenu = $menu->getChild('customers');
        if (null !== $customerSubmenu) {
            $customerSubmenu
                ->addChild('project_requests', ['route' => 'app_admin_project_request_index'])
                ->setLabel('Demandes sur-mesure')
                ->setLabelAttribute('icon', 'envelope outline');
        }

        $cmsSubmenu = $menu->getChild('sylius_cms');
        if (null !== $cmsSubmenu) {
            // Masquer les sections CMS inutilisées pour l'artisanat
            $cmsSubmenu->removeChild('templates');
            $cmsSubmenu->removeChild('blocks');
            $cmsSubmenu->removeChild('media');

            $cmsSubmenu
                ->addChild('blog_posts', [
                    'route' => 'app_admin_blog_post_index',
                    'extras' => ['routes' => [
                        ['route' => 'app_admin_blog_post_create'],
                        ['route' => 'app_admin_blog_post_update'],
                    ]],
                ])
                ->setLabel('Journal de l\'Artisan (Blog)')
                ->setLabelAttribute('icon', 'tabler:article');
        }

        $catalogSubmenu = $menu->getChild('catalog');
        if (null !== $catalogSubmenu) {
            // Masquer les sections catalogue inutilisées pour des créations artisanales / pièces uniques
            $catalogSubmenu->removeChild('inventory');
            $catalogSubmenu->removeChild('attributes');
            $catalogSubmenu->removeChild('options');
            $catalogSubmenu->removeChild('association_types');

            $taxonsItem = $catalogSubmenu->getChild('taxons');
            if (null !== $taxonsItem) {
                $taxonsItem->setLabel('Catalogues');
            }
        }

        // Désactiver (masquer) la section "Marketing" (promotions, réductions, coupons, avis)
        if (null !== $menu->getChild('marketing')) {
            $menu->removeChild('marketing');
        }

        // Désactiver (masquer) la section "Ventes" (Sales) car on n'utilise plus le pipeline e-commerce classique
        if (null !== $menu->getChild('sales')) {
            $menu->removeChild('sales');
        }
    }
}
