<?php

declare(strict_types=1);

namespace App\Service\Ai;

use App\Entity\Ai\AiPrompt;
use App\Enum\Ai\AiPromptType;
use App\Repository\Ai\AiPromptRepository;
use Doctrine\ORM\EntityManagerInterface;

class AiPromptManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AiPromptRepository $promptRepository,
    ) {
    }

    /**
     * Sauvegarde un prompt et garantit qu'un seul prompt est actif par type.
     */
    public function save(AiPrompt $prompt, bool $flush = true): void
    {
        $type = $prompt->getType();
        if (null === $type) {
            return;
        }

        if ($prompt->isActive()) {
            // Désactiver tous les autres prompts de ce type
            $this->promptRepository->deactivateOthers($type, $prompt->getId());
        } else {
            // S'il est inactif, vérifier s'il existe déjà un prompt actif pour ce type
            $currentActive = $this->promptRepository->findActiveByType($type);
            // S'il n'y en a aucun actif ou si le seul actif est ce prompt lui-même, forcer à actif
            if (null === $currentActive || $currentActive->getId() === $prompt->getId()) {
                $prompt->setIsActive(true);
            }
        }

        $this->entityManager->persist($prompt);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * Bascule l'état actif/inactif d'un prompt en respectant l'exclusivité par type.
     * 
     * @return array{success: bool, message: string}
     */
    public function toggleActive(AiPrompt $prompt): array
    {
        $type = $prompt->getType();
        if (null === $type) {
            return ['success' => false, 'message' => 'Type de prompt invalide.'];
        }

        if ($prompt->isActive()) {
            // L'utilisateur souhaite désactiver le prompt actif
            $alternatives = $this->promptRepository->findAlternatives($type, $prompt->getId());

            if (empty($alternatives)) {
                return [
                    'success' => false,
                    'message' => sprintf(
                        'Impossible de désactiver le seul prompt existant pour le type "%s". Veuillez d\'abord créer ou activer un prompt alternatif.',
                        $type->getLabel()
                    ),
                ];
            }

            // Basculer le prompt courant à inactif
            $prompt->setIsActive(false);
            $prompt->setUpdatedAt(new \DateTimeImmutable());

            // Activer la première alternative trouvée
            $newActive = $alternatives[0];
            $newActive->setIsActive(true);
            $newActive->setUpdatedAt(new \DateTimeImmutable());

            $this->entityManager->flush();

            return [
                'success' => true,
                'message' => sprintf(
                    'Le prompt "%s" a été désactivé. Le prompt alternatif "%s" a été automatiquement activé pour le type "%s".',
                    $prompt->getLabel(),
                    $newActive->getLabel(),
                    $type->getLabel()
                ),
            ];
        }

        // L'utilisateur souhaite activer ce prompt
        // 1. Désactiver tous les autres prompts du même type
        $this->promptRepository->deactivateOthers($type, $prompt->getId());

        // 2. Activer ce prompt
        $prompt->setIsActive(true);
        $prompt->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return [
            'success' => true,
            'message' => sprintf('Le prompt "%s" est désormais le prompt actif pour le type "%s".', $prompt->getLabel(), $type->getLabel()),
        ];
    }

    /**
     * Supprime un prompt en réassignant l'état actif si nécessaire.
     */
    public function delete(AiPrompt $prompt): void
    {
        $type = $prompt->getType();
        $wasActive = $prompt->isActive();
        $promptId = $prompt->getId();

        if ($wasActive && null !== $type) {
            $alternatives = $this->promptRepository->findAlternatives($type, $promptId);
            if (!empty($alternatives)) {
                $newActive = $alternatives[0];
                $newActive->setIsActive(true);
                $newActive->setUpdatedAt(new \DateTimeImmutable());
            }
        }

        $this->entityManager->remove($prompt);
        $this->entityManager->flush();
    }
}
