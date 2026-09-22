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

        // Désactiver (masquer) la section "Ventes" (Sales) car on n'utilise plus le pipeline e-commerce classique
        if (null !== $menu->getChild('sales')) {
            $menu->removeChild('sales');
        }
    }
}
