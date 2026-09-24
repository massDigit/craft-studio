<?php

declare(strict_types=1);

namespace App\Service\Ai;

use App\Enum\Ai\AiPromptType;
use App\Repository\Ai\AiPromptRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OllamaAiService
{
    public const DEFAULT_BRAND_CONTEXT = "Nous sommes l'atelier de création ZEN TOO CRAFT, un studio d'artisanat d'art français d'exception. Nous concevons et façonnons à la main des créations végétales durables :
1. Instruments de musique à vent en bambou noble : flûtes japonaises Shakuhachi, flûtes traversières baroques et contemporaines, façonnées avec précision acoustique, traitement thermique et huilage naturel.
2. Luminaires d'art et lampes d'ambiance ajourées : créés à partir de calebasses (gourdes végétales séchées, évidées et minutieusement sculptées/perforées) et de coques de noix de coco polies.
3. Éléments décoratifs d'intérieur : pièces uniques combinant le bambou noble, la calebasse sculptée et la coque de noix de coco.
L'axe de notre maison est purement artistique, technique, acoustique, lumineux et traditionnel. INTERDICTION ABSOLUE de parler de méditation, de relaxation New Age ou de sonothérapie. Fabrication 100% manuelle, locale et sur-mesure.";

    public const DEFAULT_PRODUCT_PROMPT = "Rédige une description poétique et raffinée en Français pour cette création artisanale en bambou de la Maison ZEN TOO CRAFT. Mets en valeur le geste de l'artisan, la noblesse et l'acoustique du végétal, ainsi que la singularité de la pièce. Adopte un ton élégant et chaleureux, sans aucun emoji ni jargon commercial agressif.";

    public const DEFAULT_TAXON_PROMPT = "Tu es le directeur artistique de la Maison ZEN TOO CRAFT. Rédige une présentation raffinée et élégante en Français pour la collection d'objets en bambou indiquée. Mets en valeur la pureté du végétal, le travail acoustique ou esthétique de l'atelier, et l'exclusivité du fait-main.";

    public const DEFAULT_FAQ_PROMPT = "Tu es le rédacteur officiel du studio de création ZEN TOO CRAFT. MISSION : Rédige UNE question fréquente (FAQ) très pertinente et sa réponse pour la catégorie donnée. Ta réponse doit refléter notre philosophie artisanale, l'authenticité du bambou et l'exigence du fait-main.";

    public const DEFAULT_BLOG_PROMPT = "Tu es le rédacteur éditorial de l'Atelier ZEN TOO CRAFT. Tu rédiges pour le Journal de l'Artisan des articles clairs, structurés et captivants, fondés sur des explications réelles et concrètes autour du travail des matières (bambou noble, calebasse, coque de noix de coco), des savoir-faire d'atelier et des créations. Le contenu doit instruire le lecteur et valoriser la réalité du travail artisanal, sans bavardage superflu, sans romancement artificiel et sans aucun jargon commercial ou publicitaire.";

    public const DEFAULT_TRANSLATION_PROMPT = "You are a professional translator for luxury handcrafted art studio ZEN TOO CRAFT. Translate the text with high fidelity, preserving our poetic, authentic and artisanal brand voice.";

    // Directives techniques immuables garanties et gérées par le code applicatif (invisibles de l'admin)
    private const TECHNICAL_CONSTRAINT_PRODUCT = "\n\nDIRECTIVES TECHNIQUES STRICTES :\n- Tu dois répondre UNIQUEMENT avec du code HTML valide (ex: <p>, <h3>, <ul>, <li>, <strong>).\n- AUCUNE phrase d'introduction ni de conclusion (interdiction absolue de dire 'Voici la description').\n- AUCUN bloc Markdown (pas de ```html).\n- Aucun tiret cadratin (« — »).";

    private const TECHNICAL_CONSTRAINT_TAXON = "\n\nDIRECTIVES TECHNIQUES STRICTES :\n- Rédige directement le texte de présentation en paragraphes sobres.\n- AUCUNE phrase d'introduction ni formule de politesse.\n- Aucun tiret cadratin (« — »).";

    private const TECHNICAL_CONSTRAINT_FAQ = "\n\nDIRECTIVES TECHNIQUES STRICTES :\n- Tu dois impérativement formater ta réponse avec exactement ces balises XML et rien d'autre :\n<question>La question ici</question>\n<answer>La réponse détaillée ici</answer>\n- AUCUNE phrase d'introduction, de salutation ni de conclusion.";

    private const TECHNICAL_CONSTRAINT_BLOG_POST = "\n\nDIRECTIVES TECHNIQUES STRICTES :\n- Tu dois impérativement formater ta réponse avec ces balises XML précises :\n<title>Titre clair et captivant</title>\n<topic>Rubrique ou Thématique</topic>\n<slug>slug-url-optimise-sans-accents</slug>\n<excerpt>Chapeau d'accroche clair et informatif (2 phrases engageantes)</excerpt>\n<content>Corps complet de l'article en code HTML valide (structuré avec des balises <p>, <h2> et <strong>)</content>\n- RÈGLES ÉDITORIALES ET FACTUELLES (CRITIQUE) :\n  * Respecte scrupuleusement l'angle narratif et la tonalité demandés par l'utilisateur (ex: technique, pédagogique, formel ou sensible).\n  * Privilégie les faits, les explications concrètes et la clarté d'apprentissage : évite tout lyrisme creux, toute métaphore excessive et tout romancement artificiel.\n  * INTERDICTION FORMELLE d'inventer des balises d'images, de figures, de liens ou de styles (aucune balise <img>, <figure>, ni <a>, aucun attribut style). UNIQUEMENT du texte structuré avec <p>, <h2>, <h3>, <strong>.\n  * INTERDICTION FORMELLE de tout vocabulaire commercial ou marketing (proscris absolument 'pourquoi choisir', 'boutique en ligne', 'meilleur prix', 'panier', 'acheter').\n  * INTERDICTION des tirets cadratins ou demi-cadratins (« — » ou « – »). Rédige en français fluide avec des virgules et des points.\n  * Rédige en prose naturelle et fluide (2 à 4 paragraphes réels, balises <p>), sans plan scolaire numéroté (pas de '1/', '2/').\n  * Reste rigoureusement crédible et fidèle aux matières et techniques mentionnées (bambou noble pour les flûtes et décor, calebasse sculptée pour les luminaires et décor, coque de noix de coco polie pour les diffuseurs et décor).\n- RÈGLE ABSOLUE ANTI-RÉPÉTITION : Interdiction stricte de répéter les mêmes phrases ou paragraphes. Chaque paragraphe apporte une idée ou une étape nouvelle.\n- Dès que le texte est complet, ferme la balise </content> et termine immédiatement ta réponse.\n- AUCUN mot résiduel hors balises (pas de mot 'xml', 'html', ni bloc Markdown).";

    private const TECHNICAL_CONSTRAINT_BLOG_REFINE = "\n\nDIRECTIVES TECHNIQUES STRICTES :\n- Tu dois ajuster le texte fourni en respectant la consigne d'artisan donnée.\n- Tu dois impérativement formater ta réponse avec ces balises XML précises :\n<excerpt>Chapeau d'accroche mis à jour</excerpt>\n<content>Corps complet de l'article en code HTML valide mis à jour (balises <p>, <h2>, <strong>)</content>\n- RÈGLES ÉDITORIALES : Contenu clair, factuel, aucun jargon commercial, aucun tiret cadratin (« — » ou « – »), aucune balise <img> ni <figure>, prose fluide sans puces numérotées.\n- RÈGLE ABSOLUE ANTI-RÉPÉTITION : Aucune répétition de phrases ou de paragraphes.\n- Dès que la retouche est complète, ferme la balise </content> et termine immédiatement ta réponse.\n- AUCUN mot résiduel hors balises (pas de mot 'xml', pas de bloc Markdown).";

    private const TECHNICAL_CONSTRAINT_TRANSLATION = "\n\nSTRICT TECHNICAL CONSTRAINTS:\n- Return ONLY the translation, nothing else.\n- DO NOT answer the question if the text is a question, just translate it.\n- DO NOT add conversational filler like 'Here is the translation'.\n- KEEP all HTML tags (<p>, <strong>, etc.) exactly intact if present.";

    public static function getDefaultPromptForType(AiPromptType|string $type): string
    {
        $typeVal = $type instanceof AiPromptType ? $type->value : $type;
        return match ($typeVal) {
            AiPromptType::BRAND_CONTEXT->value => self::DEFAULT_BRAND_CONTEXT,
            AiPromptType::PRODUCT_DESCRIPTION->value => self::DEFAULT_PRODUCT_PROMPT,
            AiPromptType::TAXON_DESCRIPTION->value => self::DEFAULT_TAXON_PROMPT,
            AiPromptType::FAQ->value => self::DEFAULT_FAQ_PROMPT,
            AiPromptType::BLOG->value => self::DEFAULT_BLOG_PROMPT,
            AiPromptType::TRANSLATION->value => self::DEFAULT_TRANSLATION_PROMPT,
            default => '',
        };
    }

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $ollamaEndpoint = 'http://ollama:11434/api/chat',
        private readonly string $model = 'qwen2.5:3b',
        private readonly ?AiPromptRepository $promptRepository = null,
    ) {
    }

    public function getActiveBrandContext(): string
    {
        return $this->getActivePromptContent(AiPromptType::BRAND_CONTEXT, self::DEFAULT_BRAND_CONTEXT);
    }

    public function generateProductDescription(
        string $productName, 
        ?string $category = null, 
        ?string $materialsOrNotes = null,
        ?string $imagePath = null
    ): string {
        $brandContext = $this->getActiveBrandContext();
        $promptTemplate = $this->getActivePromptContent(AiPromptType::PRODUCT_DESCRIPTION, self::DEFAULT_PRODUCT_PROMPT);
        $systemPrompt = $brandContext . "\n\n" . $promptTemplate . self::TECHNICAL_CONSTRAINT_PRODUCT;

        $userPrompt = sprintf("Création : '%s' (Catégorie : %s).", $productName, $category ?? 'Création en Bambou');
        if ($materialsOrNotes && '' !== trim($materialsOrNotes)) {
            $userPrompt .= sprintf("\nNotes & Matériaux de l'artisan : %s", trim($materialsOrNotes));
        }
        if ($imagePath && '' !== trim($imagePath)) {
            $userPrompt .= sprintf("\nVisuel / Illustration jointe : %s", basename($imagePath));
        }

        return $this->queryOllama($systemPrompt, $userPrompt, $imagePath);
    }

    public function generateTaxonDescription(
        string $taxonName, 
        ?string $parentTaxonName = null, 
        ?string $currentDescription = null,
        ?string $imagePath = null
    ): string {
        $brandContext = $this->getActiveBrandContext();
        $promptTemplate = $this->getActivePromptContent(AiPromptType::TAXON_DESCRIPTION, self::DEFAULT_TAXON_PROMPT);
        $systemPrompt = $brandContext . "\n\n" . $promptTemplate . self::TECHNICAL_CONSTRAINT_TAXON;

        $userPrompt = sprintf("Catégorie / Collection : '%s'.", $taxonName);
        if ($parentTaxonName && '' !== trim($parentTaxonName)) {
            $userPrompt .= sprintf(" (Catégorie parente : '%s')", trim($parentTaxonName));
        }
        if ($currentDescription && '' !== trim($currentDescription)) {
            $userPrompt .= sprintf("\nDescription ou ébauche actuelle : %s", trim(strip_tags($currentDescription)));
        }
        if ($imagePath && '' !== trim($imagePath)) {
            $userPrompt .= sprintf("\nVisuel / Illustration jointe : %s", basename($imagePath));
        }

        return trim($this->queryOllama($systemPrompt, $userPrompt, $imagePath));
    }

    public function generateFaqItem(
        string $taxonName, 
        ?string $taxonDescription = null,
        ?string $imagePath = null
    ): array {
        $brandContext = $this->getActiveBrandContext();
        $promptTemplate = $this->getActivePromptContent(AiPromptType::FAQ, self::DEFAULT_FAQ_PROMPT);
        $systemPrompt = $brandContext . "\n\n" . $promptTemplate . self::TECHNICAL_CONSTRAINT_FAQ;

        $userPrompt = sprintf("Catégorie : '%s'.", $taxonName);
        if ($taxonDescription && '' !== trim($taxonDescription)) {
            $userPrompt .= sprintf("\nDescription de la catégorie : %s", trim(strip_tags($taxonDescription)));
        }
        if ($imagePath && '' !== trim($imagePath)) {
            $userPrompt .= sprintf("\nVisuel / Illustration jointe : %s", basename($imagePath));
        }

        $response = $this->queryOllama($systemPrompt, $userPrompt, $imagePath);

        $question = '';
        $answer = '';

        if (preg_match('/<question>(.*?)<\/question>/is', $response, $matchesQ)) {
            $question = trim($matchesQ[1]);
        }

        if (preg_match('/<answer>(.*?)<\/answer>/is', $response, $matchesA)) {
            $answer = trim($matchesA[1]);
        }

        // Failsafe tolérant : si l'IA n'a pas inclus les balises XML ou si une est manquante
        if ('' === $question || '' === $answer) {
            $cleanResponse = trim(strip_tags($response, '<p><b><strong><em>'));
            if (preg_match('/^(.*?\?)\s*(.*)$/us', $cleanResponse, $fallbackMatches)) {
                if ('' === $question) {
                    $question = trim($fallbackMatches[1]);
                }
                if ('' === $answer) {
                    $answer = trim($fallbackMatches[2]);
                }
            }
        }

        // Failsafe ultime si le texte ne contient aucun point d'interrogation
        if ('' === $question) {
            $question = sprintf('Quelles sont les particularités de nos créations pour %s ?', $taxonName);
        }
        if ('' === $answer) {
            $answer = '' !== trim($response) ? trim(strip_tags($response)) : 'Chaque création est façonnée à la main au sein de notre atelier d\'art, avec une exigence absolue de qualité et d\'authenticité.';
        }

        // Nettoyage supplémentaire au cas où le LLM aurait ajouté "Question:" dans les balises
        $question = preg_replace('/^(Question|Q)\s*:\s*/i', '', $question);
        $answer = preg_replace('/^(Réponse|R|Answer)\s*:\s*/i', '', $answer);

        return [
            'question' => $question,
            'answer' => $answer,
        ];
    }

    public function generateBlogArticle(string $title, ?string $topic = null, ?string $keywords = null): string
    {
        $brandContext = $this->getActiveBrandContext();
        $promptTemplate = $this->getActivePromptContent(AiPromptType::BLOG, self::DEFAULT_BLOG_PROMPT);
        $systemPrompt = $brandContext . "\n\n" . $promptTemplate;

        $userPrompt = sprintf("Titre de l'article : '%s'.", $title);
        if ($topic && '' !== trim($topic)) {
            $userPrompt .= sprintf("\nThématique : %s", trim($topic));
        }
        if ($keywords && '' !== trim($keywords)) {
            $userPrompt .= sprintf("\nMots-clés : %s", trim($keywords));
        }

        return trim($this->queryOllama($systemPrompt, $userPrompt));
    }

    /**
     * Génère un article complet pour le Journal de l'Artisan avec cadrage riche (sujet, angle libre, ton libre, faits réels, longueur)
     *
     * @return array{title: string, topic: string, slug: string, excerpt: string, content: string}
     */
    public function generateBlogPostFull(
        string $topic,
        ?string $angle = null,
        ?string $tone = null,
        ?string $keypoints = null,
        ?string $targetTopic = null,
        ?string $length = 'medium'
    ): array {
        $brandContext = $this->getActiveBrandContext();
        $promptTemplate = $this->getActivePromptContent(AiPromptType::BLOG, self::DEFAULT_BLOG_PROMPT);
        $systemPrompt = $brandContext . "\n\n" . $promptTemplate . self::TECHNICAL_CONSTRAINT_BLOG_POST;

        $targetLengthWords = match ($length) {
            'short' => 'environ 350 à 450 mots',
            'long' => 'environ 900 à 1100 mots',
            default => 'environ 650 à 750 mots',
        };

        $userPrompt = sprintf("Sujet / Thème de l'article : '%s'.", $topic);
        if ($angle && '' !== trim($angle)) {
            $userPrompt .= sprintf("\nAngle narratif souhaité : %s", trim($angle));
        }
        if ($tone && '' !== trim($tone)) {
            $userPrompt .= sprintf("\nTonalité de rédaction : %s", trim($tone));
        }
        if ($targetTopic && '' !== trim($targetTopic)) {
            $userPrompt .= sprintf("\nRubrique ou Thématique du Journal : %s", trim($targetTopic));
        }
        if ($keypoints && '' !== trim($keypoints)) {
            $userPrompt .= sprintf("\nFaits réels, matières et gestes d'atelier indispensables à inclure : %s", trim($keypoints));
        }
        $userPrompt .= sprintf("\nLongueur souhaitée : %s.", $targetLengthWords);
        $userPrompt .= "\n\nEXIGENCES ÉDITORIALES :"
            . "\n- Respecte fidèlement l'angle narratif et la tonalité indiqués ci-dessus (ex: technique, pédagogique, formel ou sensible selon le choix)."
            . "\n- Développe un contenu structuré, captivant et factuel fondé sur les matières et les gestes réels mentionnés."
            . "\n- Proscris tout bavardage creux, toute métaphore excessive et tout romancement artificiel."
            . "\n- ZÉRO jargon marketing, commercial ou e-commerce."
            . "\n- AUCUN tiret cadratin (« — ») ni demi-cadratin (« – »)."
            . "\n- Structure en 2 à 4 vrais paragraphes de prose (balises <p>), sans numérotation artificielle ni puces.";

        $response = $this->queryOllama($systemPrompt, $userPrompt, null, 1200, [
            'temperature' => 0.35,
            'top_p' => 0.85,
            'top_k' => 40,
            'repeat_penalty' => 1.25,
            'repeat_last_n' => 256,
        ]);

        // Extraction par balises XML
        $title = '';
        $extractedTopic = $targetTopic && '' !== trim($targetTopic) ? trim($targetTopic) : 'Savoir-Faire';
        $slug = '';
        $excerpt = '';
        $content = '';

        if (preg_match('/<title>(.*?)<\/title>/is', $response, $m)) {
            $title = trim(strip_tags($m[1]));
        }
        if (preg_match('/<topic>(.*?)<\/topic>/is', $response, $m)) {
            $extractedTopic = trim(strip_tags($m[1]));
        }
        if (preg_match('/<slug>(.*?)<\/slug>/is', $response, $m)) {
            $slug = trim(strip_tags($m[1]));
        }
        if (preg_match('/<excerpt>(.*?)<\/excerpt>/is', $response, $m)) {
            $excerpt = trim(strip_tags($m[1]));
        }
        if (preg_match('/<content>(.*?)<\/content>/is', $response, $m)) {
            $content = trim($m[1]);
        }

        // Failsafes robustes
        if ('' === $title) {
            $title = $topic;
        }
        $slug = (new \Symfony\Component\String\Slugger\AsciiSlugger())->slug($title)->lower()->toString();
        $slug = preg_replace('/\.(?:jpg|png|webp|jpeg|html)$/i', '', $slug);

        if ('' === $content) {
            $cleaned = preg_replace('/<(?:title|topic|slug|excerpt)>.*?<\/(?:title|topic|slug|excerpt)>/is', '', $response);
            $content = trim((string) $cleaned);
        }

        // Nettoyage strict des balises (interdiction images inventées, styles, figures, scripts)
        $content = preg_replace('/<\/?(?:figure|figcaption|img|script|style|form|button|iframe|details|summary|aside|article|content|footer)[^>]*>/i', '', $content);
        $content = preg_replace('/\s*style\s*=\s*["\'][^"\']*["\']/i', '', $content);
        $content = preg_replace('/<a\b[^>]*>(.*?)<\/a>/is', '$1', $content);
        $content = preg_replace('/<\/p>\s*<\/h[1-6]>/i', '</p>', $content);
        $content = preg_replace('/<h[4-6][^>]*>(.*?)<\/h[4-6]>/i', '<h3>$1</h3>', $content);
        $content = strip_tags($content, '<p><h2><h3><strong><em><ul><ol><li><blockquote><br>');

        // Formater en paragraphes si le LLM n'a pas inclus de balises HTML
        if (!str_contains($content, '<p>') && !str_contains($content, '<h2>')) {
            $paragraphs = array_filter(explode("\n\n", str_replace("\r", "", $content)));
            $content = implode("", array_map(fn($p) => "<p>" . trim($p) . "</p>", $paragraphs));
        }

        $content = $this->cleanDuplicateParagraphs($content);
        $content = $this->humanizeArtisanContent($content);

        // Failsafe pour l'extrait : doit être un vrai chapeau d'accroche substantiel
        $cleanExcerpt = trim(strip_tags($excerpt));
        if ('' === $cleanExcerpt || mb_strlen($cleanExcerpt) < 35 || str_contains($cleanExcerpt, '©') || str_contains($cleanExcerpt, 'boutique en ligne') || str_contains($cleanExcerpt, 'pourquoi choisir')) {
            if (preg_match('/<p>(.*?)<\/p>/is', $content, $pMatches) && mb_strlen(trim(strip_tags($pMatches[1]))) >= 35) {
                $excerpt = trim(strip_tags($pMatches[1]));
            } else {
                $excerpt = mb_substr(trim(strip_tags($content)), 0, 220) . '...';
            }
        }
        $excerpt = $this->cleanDuplicateParagraphs($excerpt);
        $excerpt = $this->humanizeArtisanContent($excerpt, true);
        $title = $this->humanizeArtisanContent($title);

        return [
            'title' => $title,
            'topic' => $extractedTopic,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'content' => $content,
        ];
    }

    /**
     * Applique une retouche ciblée demandée par l'artisan sur un brouillon existant
     *
     * @return array{excerpt: string, content: string}
     */
    public function refineBlogPostContent(
        string $currentContent,
        ?string $currentExcerpt = null,
        string $refineInstruction = ''
    ): array {
        $brandContext = $this->getActiveBrandContext();
        $promptTemplate = $this->getActivePromptContent(AiPromptType::BLOG, self::DEFAULT_BLOG_PROMPT);
        $systemPrompt = $brandContext . "\n\n" . $promptTemplate . self::TECHNICAL_CONSTRAINT_BLOG_REFINE;

        $userPrompt = "Voici le brouillon actuel de l'article :\n";
        if ($currentExcerpt && '' !== trim($currentExcerpt)) {
            $userPrompt .= "<excerpt>" . trim($currentExcerpt) . "</excerpt>\n";
        }
        $userPrompt .= "<content>" . trim($currentContent) . "</content>\n\n";
        $userPrompt .= "CONSIGNE DE RETOUCHE DEMANDÉE PAR L'ARTISAN :\n" . $refineInstruction;
        $userPrompt .= "\n\nEXIGENCES ÉDITORIALES :\n"
            . "- Respecte scrupuleusement la consigne demandée en restant clair, instructif et factuel.\n"
            . "- Zéro bavardage creux ni jargon promotionnel.\n"
            . "- Aucun tiret cadratin (« — ») ni demi-cadratin (« – »).";

        $response = $this->queryOllama($systemPrompt, $userPrompt, null, 1200, [
            'temperature' => 0.35,
            'top_p' => 0.85,
            'top_k' => 40,
            'repeat_penalty' => 1.25,
            'repeat_last_n' => 256,
        ]);

        $newExcerpt = $currentExcerpt ?? '';
        $newContent = $currentContent;

        if (preg_match('/<excerpt>(.*?)<\/excerpt>/is', $response, $m)) {
            $newExcerpt = trim(strip_tags($m[1]));
        }
        if (preg_match('/<content>(.*?)<\/content>/is', $response, $m)) {
            $newContent = trim($m[1]);
        } else {
            $cleaned = preg_replace('/<excerpt>.*?<\/excerpt>/is', '', $response);
            if ('' !== trim((string) $cleaned)) {
                $newContent = trim((string) $cleaned);
            }
        }

        // Nettoyage strict des balises (interdiction images inventées, styles, figures, scripts)
        $newContent = preg_replace('/<\/?(?:figure|figcaption|img|script|style|form|button|iframe|details|summary|aside|article|content|footer)[^>]*>/i', '', $newContent);
        $newContent = preg_replace('/\s*style\s*=\s*["\'][^"\']*["\']/i', '', $newContent);
        $newContent = preg_replace('/<a\b[^>]*>(.*?)<\/a>/is', '$1', $newContent);
        $newContent = preg_replace('/<\/p>\s*<\/h[1-6]>/i', '</p>', $newContent);
        $newContent = preg_replace('/<h[4-6][^>]*>(.*?)<\/h[4-6]>/i', '<h3>$1</h3>', $newContent);
        $newContent = strip_tags($newContent, '<p><h2><h3><strong><em><ul><ol><li><blockquote><br>');

        $newContent = $this->cleanDuplicateParagraphs($newContent);
        $newContent = $this->humanizeArtisanContent($newContent);
        $newExcerpt = $this->cleanDuplicateParagraphs($newExcerpt);
        $newExcerpt = $this->humanizeArtisanContent($newExcerpt, true);

        return [
            'excerpt' => $newExcerpt,
            'content' => $newContent,
        ];
    }

    public function translateText(string $text, string $targetLocale, ?string $sourceLocale = 'fr'): string
    {
        $languageName = match (strtolower($targetLocale)) {
            'en', 'en_us', 'en_gb' => 'English',
            'fr', 'fr_fr' => 'French',
            'es', 'es_es' => 'Spanish',
            'de', 'de_de' => 'German',
            'it', 'it_it' => 'Italian',
            default => $targetLocale,
        };

        $basePrompt = $this->getActivePromptContent(AiPromptType::TRANSLATION, self::DEFAULT_TRANSLATION_PROMPT);

        $systemPrompt = sprintf(
            "Target language: %s.\n\n%s%s",
            $languageName,
            $basePrompt,
            self::TECHNICAL_CONSTRAINT_TRANSLATION
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
        $brandContext = $this->getActiveBrandContext();
        $systemPrompt = $brandContext . "\n\n"
            . 'Tu es un maître luthier et ébéniste de l\'atelier ZEN TOO CRAFT. '
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
        $brandContext = $this->getActiveBrandContext();
        $systemPrompt = $brandContext . "\n\n"
            . 'Tu es un artisan luthier de l\'atelier ZEN TOO CRAFT. '
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
                $productName,
                $category ?? 'Instrument acoustique en bambou',
                $expectedTitle,
                $expectedTitle
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
                $jsonString = preg_replace("/\r\n|\n|\r/", "\\n", $jsonString);
                $decoded = json_decode($jsonString, true);
            }
        }

        if (is_array($decoded) && isset($decoded['description'])) {
            if (!isset($decoded['title']) || ($expectedTitle && $decoded['title'] !== $expectedTitle)) {
                $decoded['title'] = $expectedTitle ?: 'Caractéristiques Techniques';
            }

            $decoded['description'] = str_ireplace(['<br><br>', '<br>', '<br/>', '<br />'], "\n", $decoded['description']);

            return $decoded;
        }

        return [
            'title' => '⚠️ ' . ($expectedTitle ?: 'Erreur de format IA'),
            'description' => "ERREUR DE FORMATAGE JSON.\nVoici ce que l'IA a répondu en brut :\n\n" . $response
        ];
    }

    private function getActivePromptContent(AiPromptType $type, string $defaultContent): string
    {
        if (null !== $this->promptRepository) {
            $prompt = $this->promptRepository->findActiveByType($type);
            if (null !== $prompt && '' !== trim($prompt->getContent() ?? '')) {
                return trim($prompt->getContent());
            }
        }

        return $defaultContent;
    }

    /**
     * @param array<string, mixed> $customOptions
     */
    private function queryOllama(
        string $systemPrompt,
        string $userPrompt,
        ?string $imagePath = null,
        int $numPredict = 800,
        array $customOptions = []
    ): string {
        try {
            $isMultimodalModel = (bool) preg_match('/(llava|vision|vl|multimodal)/i', $this->model);

            $userMessage = [
                'role' => 'user',
                'content' => $userPrompt,
            ];

            if ($isMultimodalModel && $imagePath && file_exists($imagePath)) {
                $fileContent = @file_get_contents($imagePath);
                if ($fileContent !== false) {
                    $userMessage['images'] = [base64_encode($fileContent)];
                }
            }

            // Options équilibrées : créativité fluide, température adaptée et pénalité de répétition
            $options = array_merge([
                'temperature' => 0.65,
                'top_p' => 0.9,
                'top_k' => 40,
                'repeat_penalty' => 1.25,
                'repeat_last_n' => 256,
                'num_predict' => $numPredict,
            ], $customOptions);

            $response = $this->httpClient->request('POST', $this->ollamaEndpoint, [
                'json' => [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        $userMessage,
                    ],
                    'stream' => false,
                    'options' => $options,
                ],
                'timeout' => 180,
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

    /**
     * Garde-fou applicatif : élimine les boucles de répétition de paragraphes
     */
    private function cleanDuplicateParagraphs(string $content): string
    {
        if (preg_match_all('/<p>(.*?)<\/p>/is', $content, $matches)) {
            $seen = [];
            $hasDuplicates = false;
            $uniqueParagraphs = [];

            foreach ($matches[1] as $idx => $inner) {
                $normalized = mb_strtolower(trim(preg_replace('/\s+/u', ' ', strip_tags($inner))));
                if ('' === $normalized) {
                    continue;
                }

                $isDupe = false;
                foreach ($seen as $prev) {
                    if ($prev === $normalized || (strlen($normalized) > 40 && str_contains($prev, substr($normalized, 0, 40)))) {
                        $isDupe = true;
                        $hasDuplicates = true;
                        break;
                    }
                }

                if (!$isDupe) {
                    $seen[] = $normalized;
                    $uniqueParagraphs[] = $matches[0][$idx];
                }
            }

            if ($hasDuplicates && count($uniqueParagraphs) > 0) {
                // Remplacement du premier bloc par les paragraphes uniques nettoyés
                $first = true;
                $cleaned = preg_replace_callback('/<p>.*?<\/p>/is', function() use (&$first, $uniqueParagraphs) {
                    if ($first) {
                        $first = false;
                        return implode("\n", $uniqueParagraphs);
                    }
                    return '';
                }, $content);

                return trim((string) $cleaned);
            }
        }

        return trim($content);
    }

    /**
     * Humanise le texte en éliminant les marqueurs d'IA, résidus XML/HTML et clichés e-commerce
     */
    private function humanizeArtisanContent(string $text, bool $isExcerpt = false): string
    {
        // 1. Nettoyage radical des résidus XML / Markdown en tête ou queue de texte
        $text = preg_replace('/^\s*(?:```(?:xml|html)?|xml|html)\s*/ui', '', $text);
        $text = preg_replace('/\s*```$/ui', '', $text);

        // 2. Nettoyage des balises et structures parasites introduites parfois par les LLM
        $text = preg_replace('/<footer>.*?<\/footer>/is', '', $text);
        $text = preg_replace('/<button[^>]*>.*?<\/button>/is', '', $text);
        $text = preg_replace('/<div class="author[^"]*">.*?<\/div>/is', '', $text);
        $text = preg_replace('/<\/?(?:article|content|section|footer|div|button)[^>]*>/i', '', $text);

        // 3. Suppression des paragraphes ou phrases au ton e-commerce / commercial agressif
        $bannedMarketingPatterns = [
            '/^.*?(?:pourquoi choisir|boutique en ligne|meilleur prix|panier|aventure achat|achats sans stress).*?$/uim',
            '/^.*?©\s*\d{4}\s*zen too craft.*?$/uim',
            '/^.*?(?:retour au journal|cliquez ici|découvrez notre boutique).*?$/uim',
        ];
        foreach ($bannedMarketingPatterns as $pattern) {
            $text = preg_replace($pattern, '', $text);
        }

        // 4. Élimination des tirets cadratins (« — ») et demi-cadratins (« – ») : marqueurs typiques d'IA
        $text = str_replace([" — ", " – "], ", ", $text);
        $text = str_replace(["—", "–"], "-", $text);

        // 5. Corrections lexicales automatiques des déformations fréquentes des modèles légers
        $typoReplacements = [
            '/\bcalbleasse\b/ui' => 'calebasse',
            '/\bcalbrese\b/ui' => 'calebasse',
            '/\bcalice\b/ui' => 'calebasse',
            '/\bcocotier provençale?\b/ui' => 'coque de noix de coco',
            '/\bbamboos\b/ui' => 'bambous',
            '/\bspécimens naturels à grande taille \(plusieurs centimètres\)/ui' => 'bambous rigoureusement sélectionnés',
            '/\bun coupable rarement fait des choses exceptionnelles/ui' => 'le geste de l\'artisan façonne chaque pièce avec patience',
            '/\baprès plusieurs milliards d\'années\b/ui' => 'au fil des saisons et du séchage',
        ];
        foreach ($typoReplacements as $regex => $replacement) {
            $text = preg_replace($regex, $replacement, $text);
        }

        // 6. Nettoyage des balises vides ou orphelines
        $text = preg_replace('/<p>\s*(?:&nbsp;|<br\s*\/?>)?\s*<\/p>/ui', '', $text);
        $text = trim((string) preg_replace("/\n{3,}/", "\n\n", $text));

        // 7. Retrait des mentions "xml" ou "html" résiduelles isolées en début de paragraphe
        $text = preg_replace('/<p>\s*(?:xml|html)\s*<br\s*\/?>\s*/ui', '<p>', $text);
        $text = preg_replace('/<p>\s*(?:xml|html)\s+/ui', '<p>', $text);
        $text = preg_replace('/^(?:xml|html)\s+/ui', '', $text);

        return trim($text);
    }
}
