<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Ai\AiPrompt;
use App\Form\Type\Ai\AiPromptTypeForm;
use App\Repository\Ai\AiPromptRepository;
use App\Service\Ai\AiPromptManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/ai-prompts', name: 'app_admin_ai_prompt_')]
#[IsGranted('ROLE_ADMINISTRATION_ACCESS')]
class AiPromptController extends AbstractController
{
    public function __construct(
        private readonly AiPromptRepository $promptRepository,
        private readonly AiPromptManager $promptManager,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $prompts = $this->promptRepository->findAllOrdered();

        return $this->render('@SyliusAdmin/ai_prompt/index.html.twig', [
            'prompts' => $prompts,
        ]);
    }

    #[Route('/new', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $prompt = new AiPrompt();
        $form = $this->createForm(AiPromptTypeForm::class, $prompt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->promptManager->save($prompt);

            $this->addFlash('success', sprintf('Le prompt "%s" a été créé avec succès.', $prompt->getLabel()));

            return $this->redirectToRoute('app_admin_ai_prompt_index');
        }

        return $this->render('@SyliusAdmin/ai_prompt/create.html.twig', [
            'form' => $form->createView(),
            'prompt' => $prompt,
        ]);
    }

    #[Route('/{id}/edit', name: 'update', methods: ['GET', 'POST'])]
    public function edit(AiPrompt $prompt, Request $request): Response
    {
        $form = $this->createForm(AiPromptTypeForm::class, $prompt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->promptManager->save($prompt);

            $this->addFlash('success', sprintf('Le prompt "%s" a été mis à jour avec succès.', $prompt->getLabel()));

            return $this->redirectToRoute('app_admin_ai_prompt_index');
        }

        return $this->render('@SyliusAdmin/ai_prompt/edit.html.twig', [
            'form' => $form->createView(),
            'prompt' => $prompt,
        ]);
    }

    #[Route('/{id}/toggle-active', name: 'toggle_active', methods: ['POST'])]
    public function toggleActive(AiPrompt $prompt, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('toggle_active_' . $prompt->getId(), (string) $request->request->get('_token'))) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'message' => 'Token CSRF invalide.'], 403);
            }
            $this->addFlash('error', 'Token CSRF invalide.');

            return $this->redirectToRoute('app_admin_ai_prompt_index');
        }

        $result = $this->promptManager->toggleActive($prompt);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($result);
        }

        $this->addFlash($result['success'] ? 'success' : 'error', $result['message']);

        return $this->redirectToRoute('app_admin_ai_prompt_index');
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(AiPrompt $prompt, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('delete_' . $prompt->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');

            return $this->redirectToRoute('app_admin_ai_prompt_index');
        }

        $label = $prompt->getLabel();
        $this->promptManager->delete($prompt);

        $this->addFlash('success', sprintf('Le prompt "%s" a été supprimé.', $label));

        return $this->redirectToRoute('app_admin_ai_prompt_index');
    }
}
