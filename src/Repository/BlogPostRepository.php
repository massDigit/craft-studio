<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Blog\BlogPost;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class BlogPostRepository extends EntityRepository
{
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
