<?php

declare(strict_types=1);

namespace App\Controller;

use Sylius\CmsPlugin\Entity\CollectionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{
    public function __construct(
        #[Target('sylius_cms.repository.collection')]
        private RepositoryInterface $collectionRepository,
        #[Target('sylius_cms.repository.page')]
        private RepositoryInterface $pageRepository,
    ) {
    }

    #[Route('/{_locale}/blog', name: 'app_blog_index', requirements: ['_locale' => '^[a-z]{2}(?:_[A-Z]{2})?$'])]
    public function index(): Response
    {
        /** @var CollectionInterface|null $collection */
        $collection = $this->collectionRepository->findOneBy(['code' => 'blog']);
        $blogPages = $collection ? $collection->getPages() : $this->pageRepository->findBy(['enabled' => true]);

        return $this->render('shop/blog/index.html.twig', [
            'blogPages' => $blogPages,
        ]);
    }
}
