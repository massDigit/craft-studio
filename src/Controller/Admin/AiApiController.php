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
        $materialsOrNotes = null;
        $imagePath = null;

        if (is_array($payload)) {
            $nameVal = $payload['name'] ?? '';
            $name = is_string($nameVal) ? $nameVal : '';
            $catVal = $payload['category'] ?? '';
            $category = is_string($catVal) ? $catVal : '';
            $matVal = $payload['materialsOrNotes'] ?? null;
            $materialsOrNotes = is_string($matVal) && '' !== trim($matVal) ? $matVal : null;
            $imgVal = $payload['imagePath'] ?? null;
            $imagePath = is_string($imgVal) && '' !== trim($imgVal) ? $imgVal : null;
        }

        if ('' === $name) {
            return new JsonResponse(['error' => 'Le nom de la création est obligatoire.'], 400);
        }

        $generatedHtml = $this->aiService->generateProductDescription($name, $category, $materialsOrNotes, $imagePath);

        return new JsonResponse([
            'success' => true,
            'content' => $generatedHtml,
        ]);
    }

    #[Route('/generate-taxon-description', name: 'generate_taxon_description', methods: ['POST'])]
    public function generateTaxonDescription(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        $taxonName = '';
        $parentName = null;
        $currentDescription = null;
        $imagePath = null;

        if (is_array($payload)) {
            $nameVal = $payload['taxonName'] ?? '';
            $taxonName = is_string($nameVal) ? $nameVal : '';
            $parentVal = $payload['parentName'] ?? null;
            $parentName = is_string($parentVal) && '' !== trim($parentVal) ? $parentVal : null;
            $descVal = $payload['currentDescription'] ?? null;
            $currentDescription = is_string($descVal) && '' !== trim($descVal) ? $descVal : null;
            $imgVal = $payload['imagePath'] ?? null;
            $imagePath = is_string($imgVal) && '' !== trim($imgVal) ? $imgVal : null;
        }

        if ('' === $taxonName) {
            return new JsonResponse(['error' => 'Le nom du taxon est obligatoire.'], 400);
        }

        $description = $this->aiService->generateTaxonDescription($taxonName, $parentName, $currentDescription, $imagePath);

        return new JsonResponse([
            'success' => true,
            'description' => $description,
        ]);
    }

    #[Route('/generate-technical-sheet', name: 'generate_technical_sheet', methods: ['POST'])]
    public function generateTechnicalSheet(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        $name = '';
        $category = '';
        $expectedTitle = null;

        if (is_array($payload)) {
            $nameVal = $payload['name'] ?? '';
            $name = is_string($nameVal) ? $nameVal : '';
            $catVal = $payload['category'] ?? '';
            $category = is_string($catVal) ? $catVal : '';
            $expectedVal = $payload['expectedTitle'] ?? '';
            $expectedTitle = is_string($expectedVal) && $expectedVal !== '' ? $expectedVal : null;
        }

        if ('' === $name) {
            return new JsonResponse(['error' => 'Le nom de la création est obligatoire.'], 400);
        }

        $items = $this->aiService->generateTechnicalSheet($name, $category, $expectedTitle);

        return new JsonResponse([
            'success' => true,
            'items' => $items,
        ]);
    }

    #[Route('/improve-text', name: 'improve_text', methods: ['POST'])]
    public function improveText(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        $text = '';
        $instructions = '';

        if (is_array($payload)) {
            $textVal = $payload['text'] ?? '';
            $text = is_string($textVal) ? $textVal : '';
            $instVal = $payload['instructions'] ?? '';
            $instructions = is_string($instVal) ? $instVal : '';
        }

        if ('' === $text || '' === $instructions) {
            return new JsonResponse(['error' => 'Texte ou instructions manquants.'], 400);
        }

        $improved = $this->aiService->improveText($text, $instructions);

        return new JsonResponse([
            'success' => true,
            'improvedText' => $improved,
        ]);
    }

    #[Route('/generate-faq', name: 'generate_faq', methods: ['POST'])]
    public function generateFaq(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        $taxonName = '';
        $taxonDescription = null;
        $imagePath = null;

        if (is_array($payload)) {
            $nameVal = $payload['taxonName'] ?? '';
            $taxonName = is_string($nameVal) ? $nameVal : '';
            $descVal = $payload['taxonDescription'] ?? null;
            $taxonDescription = is_string($descVal) && '' !== trim($descVal) ? $descVal : null;
            $imgVal = $payload['imagePath'] ?? null;
            $imagePath = is_string($imgVal) && '' !== trim($imgVal) ? $imgVal : null;
        }

        if ('' === $taxonName) {
            return new JsonResponse(['error' => 'Le nom du taxon est obligatoire.'], 400);
        }

        $generatedFaq = $this->aiService->generateFaqItem($taxonName, $taxonDescription, $imagePath);

        return new JsonResponse([
            'success' => true,
            'question' => $generatedFaq['question'],
            'answer' => $generatedFaq['answer'],
        ]);
    }

    #[Route('/generate-blog-article', name: 'generate_blog_article', methods: ['POST'])]
    public function generateBlogArticle(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        $title = '';
        $topic = null;
        $keywords = null;

        if (is_array($payload)) {
            $titleVal = $payload['title'] ?? '';
            $title = is_string($titleVal) ? $titleVal : '';
            $topicVal = $payload['topic'] ?? null;
            $topic = is_string($topicVal) && '' !== trim($topicVal) ? $topicVal : null;
            $kwVal = $payload['keywords'] ?? null;
            $keywords = is_string($kwVal) && '' !== trim($kwVal) ? $kwVal : null;
        }

        if ('' === $title) {
            return new JsonResponse(['error' => 'Le titre de l\'article est obligatoire.'], 400);
        }

        $article = $this->aiService->generateBlogArticle($title, $topic, $keywords);

        return new JsonResponse([
            'success' => true,
            'article' => $article,
        ]);
    }

    #[Route('/generate-blog-post', name: 'generate_blog_post', methods: ['POST'])]
    public function generateBlogPost(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        $topic = '';
        $angle = null;
        $tone = null;
        $keypoints = null;
        $targetTopic = null;
        $length = 'medium';

        if (is_array($payload)) {
            $topicVal = $payload['topic'] ?? '';
            $topic = is_string($topicVal) ? $topicVal : '';
            $angleVal = $payload['angle'] ?? null;
            $angle = is_string($angleVal) && '' !== trim($angleVal) ? $angleVal : null;
            $toneVal = $payload['tone'] ?? null;
            $tone = is_string($toneVal) && '' !== trim($toneVal) ? $toneVal : null;
            $kpVal = $payload['keypoints'] ?? null;
            $keypoints = is_string($kpVal) && '' !== trim($kpVal) ? $kpVal : null;
            $ttVal = $payload['targetTopic'] ?? null;
            $targetTopic = is_string($ttVal) && '' !== trim($ttVal) ? $ttVal : null;
            $lenVal = $payload['length'] ?? 'medium';
            $length = is_string($lenVal) ? $lenVal : 'medium';
        }

        if ('' === trim($topic)) {
            return new JsonResponse(['error' => 'Le sujet ou titre de l\'article est obligatoire.'], 400);
        }

        $result = $this->aiService->generateBlogPostFull($topic, $angle, $tone, $keypoints, $targetTopic, $length);

        return new JsonResponse([
            'success' => true,
            'data' => $result,
        ]);
    }

    #[Route('/refine-blog-post', name: 'refine_blog_post', methods: ['POST'])]
    public function refineBlogPost(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        $content = '';
        $excerpt = null;
        $instruction = '';

        if (is_array($payload)) {
            $contentVal = $payload['content'] ?? '';
            $content = is_string($contentVal) ? $contentVal : '';
            $excerptVal = $payload['excerpt'] ?? null;
            $excerpt = is_string($excerptVal) && '' !== trim($excerptVal) ? $excerptVal : null;
            $instVal = $payload['instruction'] ?? '';
            $instruction = is_string($instVal) ? $instVal : '';
        }

        if ('' === trim($content)) {
            return new JsonResponse(['error' => 'Le contenu actuel est obligatoire pour appliquer une retouche.'], 400);
        }

        if ('' === trim($instruction)) {
            return new JsonResponse(['error' => 'Veuillez préciser une consigne de retouche.'], 400);
        }

        $result = $this->aiService->refineBlogPostContent($content, $excerpt, $instruction);

        return new JsonResponse([
            'success' => true,
            'data' => $result,
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
