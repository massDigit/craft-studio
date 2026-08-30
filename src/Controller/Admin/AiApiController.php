<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Service\Ai\OllamaAiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/ajax/ai', name: 'app_admin_ajax_ai_')]
class AiApiController extends AbstractController
{
    public function __construct(
        private readonly OllamaAiService $aiService,
    ) {
    }

    #[Route('/generate-description', name: 'generate_description', methods: ['POST'])]
    public function generateDescription(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        $name = '';
        $category = '';

        if (is_array($payload)) {
            $nameVal = $payload['name'] ?? '';
            $name = is_string($nameVal) ? $nameVal : '';
            $catVal = $payload['category'] ?? '';
            $category = is_string($catVal) ? $catVal : '';
        }

        if ('' === $name) {
            return new JsonResponse(['error' => 'Le nom de la création est obligatoire.'], 400);
        }

        $generatedHtml = $this->aiService->generateProductDescription($name, $category);

        return new JsonResponse([
            'success' => true,
            'content' => $generatedHtml,
        ]);
    }

    #[Route('/translate-field', name: 'translate_field', methods: ['POST'])]
    public function translateField(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        $text = '';
        $targetLocale = 'en';

        if (is_array($payload)) {
            $textVal = $payload['text'] ?? '';
            $text = is_string($textVal) ? $textVal : '';
            $locVal = $payload['targetLocale'] ?? 'en';
            $targetLocale = is_string($locVal) ? $locVal : 'en';
        }

        if ('' === $text) {
            return new JsonResponse(['success' => true, 'translatedText' => '']);
        }

        $translated = $this->aiService->translateText($text, $targetLocale);

        return new JsonResponse([
            'success' => true,
            'translatedText' => $translated,
        ]);
    }

    #[Route('/translate-document', name: 'translate_document', methods: ['POST'])]
    public function translateDocument(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        $fields = [];
        $targetLocale = 'en';

        if (is_array($payload)) {
            if (isset($payload['fields']) && is_array($payload['fields'])) {
                $fields = $payload['fields'];
            }
            $locVal = $payload['targetLocale'] ?? 'en';
            $targetLocale = is_string($locVal) ? $locVal : 'en';
        }

        $translatedFields = [];
        foreach ($fields as $key => $value) {
            if (is_string($value) && '' !== trim($value)) {
                $translatedFields[$key] = $this->aiService->translateText($value, $targetLocale);
            } else {
                $translatedFields[$key] = $value;
            }
        }

        return new JsonResponse([
            'success' => true,
            'fields' => $translatedFields,
        ]);
    }
}
