<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use Sylius\CmsPlugin\Repository\PageRepositoryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PageExtension extends AbstractExtension
{
    public function __construct(
        private readonly PageRepositoryInterface $pageRepository
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ztc_get_enabled_pages', $this->getEnabledPages(...)),
        ];
    }

    public function getEnabledPages(): array
    {
        return $this->pageRepository->findBy(['enabled' => true]);
    }
}
