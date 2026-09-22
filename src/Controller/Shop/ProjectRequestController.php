<?php

declare(strict_types=1);

namespace App\Controller\Shop;

use App\Entity\ProjectRequest;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class ProjectRequestController extends AbstractController
{
    #[Route('/project-request/submit', name: 'app_shop_project_request_submit', methods: ['POST'])]
    public function submit(
        Request $request,
        ProductRepositoryInterface $productRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['email'], $data['firstName'], $data['lastName'], $data['specificDetails'], $data['productId'])) {
            return new JsonResponse(['error' => 'Données invalides'], 400);
        }

        $product = $productRepository->find($data['productId']);
        
        $projectRequest = new ProjectRequest();
        $projectRequest->setProduct($product);
        $projectRequest->setFirstName($data['firstName']);
        $projectRequest->setLastName($data['lastName']);
        $projectRequest->setEmail($data['email']);
        $projectRequest->setSpecificDetails($data['specificDetails']);
        // Status and createdAt are set in entity constructor/defaults

        $entityManager->persist($projectRequest);
        $entityManager->flush();

        // (L'envoi d'email sera géré via un EventListener ultérieurement)

        return new JsonResponse(['success' => true]);
    }
}
