<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{
    #[Route('/{_locale}/blog', name: 'app_blog_index', requirements: ['_locale' => '^[a-z]{2}(?:_[A-Z]{2})?$'])]
    public function index(): Response
    {
        return $this->render('shop/blog/index.html.twig');
    }
}
