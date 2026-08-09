<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Blog\BlogPost;
use App\Repository\BlogPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{
    public function __construct(
        private BlogPostRepository $blogPostRepository,
    ) {
    }

    #[Route('/{_locale}/blog', name: 'app_blog_index', requirements: ['_locale' => '^[a-z]{2}(?:_[A-Z]{2})?$'])]
    public function index(): Response
    {
        $posts = $this->blogPostRepository->findPublished();

        return $this->render('shop/blog/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/{_locale}/blog/{slug}', name: 'app_blog_show', requirements: ['_locale' => '^[a-z]{2}(?:_[A-Z]{2})?$'])]
    public function show(string $slug): Response
    {
        /** @var BlogPost|null $post */
        $post = $this->blogPostRepository->findOneBy(['slug' => $slug, 'published' => true]);
        if (!$post) {
            throw $this->createNotFoundException('Article introuvable.');
        }

        return $this->render('shop/blog/show.html.twig', [
            'post' => $post,
        ]);
    }
}
