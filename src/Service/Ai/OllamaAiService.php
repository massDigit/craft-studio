<?php

declare(strict_types=1);

namespace App\Service\Ai;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OllamaAiService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $ollamaEndpoint = 'http://ollama:11434/api/generate',
        private readonly string $model = 'qwen2.5:1.5b',
    ) {
    }

    public function generateProductDescription(string $productName, ?string $category = null): string
    {
        $systemPrompt = 'Tu es un générateur de HTML pur pour la Maison ZEN TOO Craft. '
            . 'Redige une description poétique et raffinée en Français pour cette création artisanale en bambou. '
            . 'REGLES STRICTES : '
            . '1. Tu dois Répondre UNIQUEMENT avec du code HTML valide (ex: <h2>, <p>, <strong>). '
            . '2. AUCUNE phrase de politesse ou d\'introduction (INTERDICTION ABSOLUE de dire "Voici la description...", "Ce qui suit...", etc.). '
            . '3. AUCUN pavé de code Markdown (pas de ```html). '
            . '4. AUCUN emoji ni jargon commercial agressif.';

        $userPrompt = sprintf("Création : '%s' (Catégorie : %s).", $productName, $category ?? 'Création en Bambou');

        return $this->queryOllama($systemPrompt, $userPrompt);
    }

    public function generateFaqItem(string $taxonName): array
    {
        $systemPrompt = 'Tu es le rédacteur officiel du studio de création "ZEN TOO CRAFT". '
            . 'CONTEXTE STRICT DE LA MARQUE : '
            . '- Nous sommes un studio d\'artisanat français. '
            . '- Nous fabriquons à la main des instruments à vent (flûtes) et des luminaires artistiques. '
            . '- Matériau exclusif : Bambou naturel, éco-design, respect du vivant. '
            . '- L\'axe de la marque est purement artisanal, artistique et musical. NE PARLE JAMAIS de "méditation", de "méditatif" ou de "sonothérapie". Ce n\'est pas notre positionnement. '
            . '- Production : 100% fait main, sur-mesure, artisanat local. ABSOLUMENT AUCUNE usine, AUCUNE importation industrielle de Chine ou du Japon. '
            . 'MISSION : Rédige UNE question fréquente (FAQ) très pertinente et sa réponse pour la catégorie donnée. Ta réponse doit refléter notre philosophie artisanale. '
            . 'REGLES STRICTES : '
            . '1. Utilise EXACTEMENT ce format XML, rien d\'autre : '
            . '<question>La question ici</question>'
            . '<answer>La réponse détaillée ici</answer>';

        $userPrompt = sprintf("Catégorie : '%s'.", $taxonName);

        $response = $this->queryOllama($systemPrompt, $userPrompt);
        
        $question = 'Pourquoi choisir nos créations ?';
        $answer = 'Parce qu\'elles sont réalisées avec soin et passion.';

        if (preg_match('/<question>(.*?)<\/question>/is', $response, $matchesQ)) {
            $question = trim($matchesQ[1]);
        }
        
        if (preg_match('/<answer>(.*?)<\/answer>/is', $response, $matchesA)) {
            $answer = trim($matchesA[1]);
        }

        // Nettoyage supplémentaire au cas où le LLM aurait ajouté "Question:" dans les balises
        $question = preg_replace('/^(Question|Q)\s*:\s*/i', '', $question);
        $answer = preg_replace('/^(Réponse|R|Answer)\s*:\s*/i', '', $answer);

        return [
            'question' => $question,
            'answer' => $answer,
        ];
    }

    public function translateText(string $text, string $targetLocale): string
    {
        $languageName = match (strtolower($targetLocale)) {
            'en', 'en_us', 'en_gb' => 'English',
            'fr', 'fr_fr' => 'French',
            'es', 'es_es' => 'Spanish',
            'de', 'de_de' => 'German',
            'it', 'it_it' => 'Italian',
            default => $targetLocale,
        };

        $systemPrompt = sprintf(
            'You are a professional translator. Translate the given text into %s. '
            . 'STRICT RULES: '
            . '1. Return ONLY the translation, nothing else. '
            . '2. DO NOT answer the question if the text is a question, just translate it. '
            . '3. DO NOT add conversational filler like "Here is the translation". '
            . '4. KEEP all HTML tags intact if present.',
            $languageName
        );

        $userPrompt = "Text to translate:\n\n" . $text;

        $response = $this->queryOllama($systemPrompt, $userPrompt);
        
        // Nettoyage des tirets si le LLM les a recopiés
        $response = preg_replace('/^---+[\r\n]*/', '', $response);
        $response = preg_replace('/[\r\n]*---+$/', '', $response);
        
        return trim($response);
    }

    public function translateArray(array $fields, string $targetLocale): array
    {
        $languageName = match (strtolower($targetLocale)) {
            'en', 'en_us', 'en_gb' => 'Anglais (English)',
            'fr', 'fr_fr' => 'Français',
            default => $targetLocale,
        };

        $systemPrompt = sprintf(
            'Tu es un traducteur expert. '
            . 'Je vais te fournir un document JSON. Tu dois le traduire vers la langue : %s. '
            . 'REGLES : '
            . '1. Tu dois répondre EXACTEMENT et UNIQUEMENT avec un objet JSON valide. '
            . '2. Conserve les mêmes clés JSON que l\'original. '
            . '3. Ne traduis PAS les clés. Traduis uniquement les valeurs. '
            . '4. Ne rajoute aucun markdown autour de ta réponse (pas de balises ```json).',
            $languageName
        );

        $userPrompt = json_encode($fields, JSON_UNESCAPED_UNICODE);

        $response = $this->queryOllama($systemPrompt, $userPrompt);

        // Nettoyage markdown
        $response = preg_replace('/^```(?:json)?\s*/i', '', $response);
        $response = preg_replace('/\s*```$/', '', $response);

        $decoded = json_decode($response, true);
        if (is_array($decoded)) {
            // Sécurité : s'assurer que les clés d'origine sont bien là
            foreach ($fields as $key => $val) {
                if (!isset($decoded[$key])) {
                    $decoded[$key] = $val;
                }
            }
            return $decoded;
        }

        return $fields;
    }

    private function queryOllama(string $systemPrompt, string $userPrompt): string
    {
        try {
            $response = $this->httpClient->request('POST', $this->ollamaEndpoint, [
                'json' => [
                    'model' => $this->model,
                    'system' => $systemPrompt,
                    'prompt' => $userPrompt,
                    'stream' => false,
                ],
                'timeout' => 45,
            ]);

            $data = $response->toArray();

            $content = trim($data['response'] ?? '');

            // Nettoyage des balises de code Markdown générées parfois par le LLM (ex: ```html ... ```)
            $content = preg_replace('/^```(?:html)?\s*/i', '', $content);
            $content = preg_replace('/\s*```$/', '', $content);
            $content = trim((string) $content);

            // Nettoyage des phrases d'introduction conversationnelles fréquentes des LLM
            $introPatterns = [
                '/^Voici\s+(?:la\s+traduction|le\s+texte|la\s+description)[^\n:]*[:\n]\s*/ui',
                '/^Ce\s+qui\s+suit\s+est[^\n:]*[:\n]\s*/ui',
                '/^Here\s+is\s+the\s+translation[^\n:]*[:\n]\s*/ui',
            ];
            $content = preg_replace($introPatterns, '', $content);

            return trim((string) $content);
        } catch (\Throwable) {
            return '';
        }
    }
}
