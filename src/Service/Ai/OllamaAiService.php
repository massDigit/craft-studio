<?php

declare(strict_types=1);

namespace App\Service\Ai;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OllamaAiService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $ollamaEndpoint = 'http://ollama:11434/api/chat',
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

        $response = preg_replace('/^```(?:json)?\s*/i', '', $response);
        $response = preg_replace('/\s*```$/', '', $response);

        $decoded = json_decode($response, true);
        if (is_array($decoded)) {
            foreach ($fields as $key => $val) {
                if (!isset($decoded[$key])) {
                    $decoded[$key] = $val;
                }
            }
            return $decoded;
        }

        return $fields;
    }

    public function improveText(string $currentText, string $instructions): string
    {
        $systemPrompt = 'Tu es un maître luthier et ébéniste de l\'atelier ZEN TOO CRAFT. '
            . 'Nous fabriquons des instruments de musique classique et acoustique. Nos créations sont 100% végétales (exclusivement en bambou).'
            . "\n\nREGLES :"
            . "\n1. Utilise un champ lexical strictement axé sur la lutherie, l'acoustique, la botanique du bambou, la menuiserie et l'artisanat traditionnel."
            . "\n2. Reste ancré dans la réalité physique de l'objet (dimensions, sonorité, technique de coupe, hygrométrie, huilage naturel)."
            . "\n3. Applique à la lettre la consigne de réécriture de l'utilisateur."
            . "\n4. Renvoie uniquement la nouvelle description. Aucune phrase d'introduction.";

        $userPrompt = sprintf(
            "TEXTE ACTUEL :\n\"%s\"\n\nCONSIGNE DE CORRECTION :\n\"%s\"\n\nRéécris le texte :", 
            $currentText, 
            $instructions
        );

        $response = $this->queryOllama($systemPrompt, $userPrompt);
        
        $introPatterns = [
            '/^Voici\s+(?:le\s+texte|la\s+description)[^\n:]*[:\n]\s*/ui',
            '/^Voici\s+la\s+version[^\n:]*[:\n]\s*/ui',
            '/^Bien\s+sûr[^\n]*\n/ui'
        ];
        $response = preg_replace($introPatterns, '', trim($response));
        
        // On s'assure que si l'IA utilise <br> (habitude), on le convertit en vrais sauts de ligne
        $response = str_ireplace(['<br><br>', '<br>', '<br/>', '<br />'], "\n", $response);

        return trim($response);
    }

    public function generateTechnicalSheet(string $productName, ?string $category = null, ?string $expectedTitle = null): array
    {
        $systemPrompt = 'Tu es un artisan luthier de l\'atelier ZEN TOO CRAFT. '
            . 'Tu rédiges des fiches techniques pour des instruments acoustiques fabriqués intégralement en bambou.'
            . "\n\nREGLES :"
            . "\n1. Si le sujet est technique, utilise un vocabulaire de lutherie (fibres, résonance). Si le sujet est logistique (livraison, sur-mesure), reste juste professionnel et factuel."
            . "\n2. Le produit est une pièce de boiserie pure (100% végétal, traité par la chaleur et huilé naturellement)."
            . "\n3. Renvoie UNIQUEMENT un objet JSON. Aucun texte avant, aucun texte après."
            . "\n4. Format EXACT : {\"title\": \"Titre\", \"description\": \"Texte factuel ici...\"}"
            . "\n5. CRITIQUE : N'insère AUCUN VRAI SAUT DE LIGNE dans la valeur de description. Écris la description sur une seule ligne continue. Pour faire des paragraphes, écris textuellement les balises <br><br>."
            . "\n6. CRITIQUE : SOIS TRÈS BREF ET CONCIS. 4 paragraphes ou points au MAXIMUM.";

        if ($expectedTitle) {
            $userPrompt = sprintf(
                "Produit : '%s' (Catégorie : %s).\n"
                . "Rédige la section spécifiquement intitulée : \"%s\".\n"
                . "La description DOIT impérativement traiter de ce sujet précis (livraison, entretien, ou technique selon le titre). "
                . "Le 'title' de ton JSON doit être exactement \"%s\".", 
                $productName, $category ?? 'Instrument acoustique en bambou', $expectedTitle, $expectedTitle
            );
        } else {
            $userPrompt = sprintf("Génère la fiche technique principale (dimensions, technique de fabrication, traitement du bois) pour : '%s' (Catégorie : %s).", $productName, $category ?? 'Instrument acoustique en bambou');
        }

        $response = $this->queryOllama($systemPrompt, $userPrompt);

        // Extraction robuste du JSON en ignorant le texte potentiellement généré autour
        $decoded = null;
        if (preg_match('/\{.*\}/s', $response, $matches)) {
            $jsonString = $matches[0];
            $decoded = json_decode($jsonString, true);
            
            // Failsafe : si le LLM a inséré des sauts de ligne réels dans la chaîne (JSON_ERROR_CTRL_CHAR)
            if ($decoded === null && json_last_error() === JSON_ERROR_CTRL_CHAR) {
                // Remplacer les sauts de ligne littéraux par des échappements \\n pour réparer le JSON
                $jsonString = preg_replace("/\r\n|\n|\r/", "\\n", $jsonString);
                // Restaurer les sauts de ligne hors des guillemets n'est pas nécessaire, JSON accepte une seule ligne.
                $decoded = json_decode($jsonString, true);
            }
        }

        if (is_array($decoded) && isset($decoded['description'])) {
            // Le modèle oublie parfois le title exact, on le force s'il était attendu
            if (!isset($decoded['title']) || ($expectedTitle && $decoded['title'] !== $expectedTitle)) {
                $decoded['title'] = $expectedTitle ?: 'Caractéristiques Techniques';
            }
            
            // On reconvertit les <br> en vrais sauts de ligne (\n) pour un affichage propre dans le textarea
            $decoded['description'] = str_ireplace(['<br><br>', '<br>', '<br/>', '<br />'], "\n", $decoded['description']);
            
            return $decoded;
        }

        // DEBUG : Si le JSON échoue, on affiche la réponse brute pour comprendre l'erreur
        return [
            'title' => '⚠️ ' . ($expectedTitle ?: 'Erreur de format IA'),
            'description' => "ERREUR DE FORMATAGE JSON.\nVoici ce que l'IA a répondu en brut :\n\n" . $response
        ];
    }

    private function queryOllama(string $systemPrompt, string $userPrompt): string
    {
        try {
            $response = $this->httpClient->request('POST', $this->ollamaEndpoint, [
                'json' => [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'stream' => false,
                    'options' => [
                        'temperature' => 0.1,
                        'top_p' => 0.5,
                    ],
                ],
                'timeout' => 180, // Augmentation drastique du timeout pour les machines locales plus lentes
            ]);

            $data = $response->toArray();

            $content = trim($data['message']['content'] ?? '');

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

            return $content;
        } catch (\Throwable $e) {
            return 'ERREUR HTTP/OLLAMA: ' . $e->getMessage();
        }
    }
}
