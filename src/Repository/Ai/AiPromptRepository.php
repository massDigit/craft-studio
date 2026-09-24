<?php

declare(strict_types=1);

namespace App\Repository\Ai;

use App\Entity\Ai\AiPrompt;
use App\Enum\Ai\AiPromptType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AiPrompt>
 */
class AiPromptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AiPrompt::class);
    }

    public function findActiveByType(AiPromptType|string $type): ?AiPrompt
    {
        $typeValue = $type instanceof AiPromptType ? $type->value : $type;

        return $this->createQueryBuilder('p')
            ->andWhere('p.type = :type')
            ->andWhere('p.isActive = true')
            ->setParameter('type', $typeValue)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return AiPrompt[]
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.type', 'ASC')
            ->addOrderBy('p.isActive', 'DESC')
            ->addOrderBy('p.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return AiPrompt[]
     */
    public function findAlternatives(AiPromptType|string $type, ?int $excludeId = null): array
    {
        $typeValue = $type instanceof AiPromptType ? $type->value : $type;

        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.type = :type')
            ->setParameter('type', $typeValue)
            ->orderBy('p.updatedAt', 'DESC');

        if (null !== $excludeId) {
            $qb->andWhere('p.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return $qb->getQuery()->getResult();
    }

    public function deactivateOthers(AiPromptType|string $type, ?int $activeId = null): void
    {
        $typeValue = $type instanceof AiPromptType ? $type->value : $type;

        $qb = $this->createQueryBuilder('p')
            ->update()
            ->set('p.isActive', ':false')
            ->andWhere('p.type = :type')
            ->setParameter('false', false)
            ->setParameter('type', $typeValue);

        if (null !== $activeId) {
            $qb->andWhere('p.id != :activeId')
               ->setParameter('activeId', $activeId);
        }

        $qb->getQuery()->execute();
    }
}
