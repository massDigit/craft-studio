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
        $systemPrompt = 'Tu es le Maitre Artisan Eco-Createur de la Maison ZEN TOO Craft. '
            . 'Ton role est de rediger des descriptions poetiques, epurees et raffinees pour des creations artisanales uniques en bambou. '
            . 'REGLES EDITORIALES STRICTES : '
            . "1. Langage : Francais litteraire, poetique, axe sur la serenite, la nature et l'acoustique. "
            . "2. Vocabulaire autorise : Tiges de bambou sechees au soleil, cire bio d'abeille, accordage naturel, vibration, ciselure a la main, resonance. "
            . '3. Interdictions absolues : AUCUN emoji, AUCUN jargon commercial agressif, AUCUN texte superflu. '
            . '4. Format de sortie : HTML semantique propre (<h2>, <p>, <strong>). Ne renvoie que le code HTML.';

        $userPrompt = sprintf("Redige la description artisanale de la creation : '%s' (Categorie : %s).", $productName, $category ?? 'Creation en Bambou');

        return $this->queryOllama($systemPrompt, $userPrompt);
    }

    public function translateText(string $text, string $targetLocale): string
    {
        $systemPrompt = sprintf(
            "Tu es un traducteur professionnel d'artisanat d'art. Traduis fidelement et naturellement le texte fourni vers la langue cible : %s. "
            . "Conserve la structure HTML originale s'il y en a une. Ne rajoute aucun commentaire ni emoji.",
            $targetLocale,
        );

        return $this->queryOllama($systemPrompt, $text);
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

            return trim($data['response'] ?? '');
        } catch (\Throwable) {
            return '';
        }
    }
}
