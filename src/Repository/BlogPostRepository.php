<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Blog\BlogPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BlogPost>
 */
class BlogPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogPost::class);
    }

    /**
     * @return array<int, BlogPost>
     */
    public function findPublished(): array
    {
        /** @var array<int, BlogPost> $results */
        $results = $this->createQueryBuilder('b')
            ->andWhere('b.published = :published')
            ->setParameter('published', true)
            ->orderBy('b.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $results;
    }
}
