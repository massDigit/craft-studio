<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FaqController extends AbstractController
{
    #[Route('/{_locale}/faq', name: 'app_faq_index', requirements: ['_locale' => '^[a-z]{2}(?:_[A-Z]{2})?$'])]
    public function index(): Response
    {
        return $this->render('shop/faq/index.html.twig');
    }
}
