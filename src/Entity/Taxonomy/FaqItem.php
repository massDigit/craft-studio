<?php

declare(strict_types=1);

namespace App\Entity\Taxonomy;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

#[ORM\Entity]
#[ORM\Table(name: 'app_faq_item')]
class FaqItem implements ResourceInterface, TranslatableInterface
{
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Taxon::class, inversedBy: 'faqItems')]
    #[ORM\JoinColumn(name: 'taxon_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?Taxon $taxon = null;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $position = 0;

    public function __construct()
    {
        $this->initializeTranslationsCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaxon(): ?Taxon
    {
        return $this->taxon;
    }

    public function setTaxon(?Taxon $taxon): void
    {
        $this->taxon = $taxon;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getQuestion(): ?string
    {
        return $this->getTranslation()->getQuestion();
    }

    public function setQuestion(?string $question): void
    {
        $this->getTranslation()->setQuestion($question);
    }

    public function getAnswer(): ?string
    {
        return $this->getTranslation()->getAnswer();
    }

    public function setAnswer(?string $answer): void
    {
        $this->getTranslation()->setAnswer($answer);
    }

    protected function createTranslation(): TranslationInterface
    {
        return new FaqItemTranslation();
    }
}
