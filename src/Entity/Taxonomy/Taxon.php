<?php

declare(strict_types=1);

namespace App\Entity\Taxonomy;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Taxon as BaseTaxon;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_taxon')]
#[ORM\HasLifecycleCallbacks]
class Taxon extends BaseTaxon
{
    /** @var Collection<array-key, FaqItem> */
    #[ORM\OneToMany(mappedBy: 'taxon', targetEntity: FaqItem::class, cascade: ['all'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'DESC'])]
    private Collection $faqItems;

    public function __construct()
    {
        parent::__construct();
        $this->faqItems = new ArrayCollection();
    }

    /**
     * @return Collection<array-key, FaqItem>
     */
    public function getFaqItems(): Collection
    {
        return $this->faqItems;
    }

    public function addFaqItem(FaqItem $faqItem): void
    {
        if (!$this->faqItems->contains($faqItem)) {
            $this->faqItems->add($faqItem);
            $faqItem->setTaxon($this);
        }
    }

    public function removeFaqItem(FaqItem $faqItem): void
    {
        if ($this->faqItems->contains($faqItem)) {
            $this->faqItems->removeElement($faqItem);
            if ($faqItem->getTaxon() === $this) {
                $faqItem->setTaxon(null);
            }
        }
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function normalizeFaqPositions(): void
    {
        if ($this->faqItems->isEmpty()) {
            return;
        }

        $items = $this->faqItems->toArray();

        // Index each item with its recency score:
        // Persisted items have an ID (higher ID = more recent).
        // New items ($id === null) are considered more recent than existing items,
        // and retain their form submission / collection order.
        $itemsWithMetadata = [];
        foreach ($items as $index => $item) {
            $recencyScore = $item->getId() !== null ? (int) $item->getId() : (1_000_000_000 + $index);
            $itemsWithMetadata[] = [
                'item' => $item,
                'requestedPosition' => $item->getPosition(),
                'recencyScore' => $recencyScore,
                'originalIndex' => $index,
            ];
        }

        // Sort items:
        // 1. Primary: requestedPosition ASC
        // 2. Secondary: if positions are equal, the more recent item takes precedence (recencyScore DESC).
        // This ensures "la plus récente prend la position de la plus ancienne".
        usort($itemsWithMetadata, function (array $a, array $b): int {
            if ($a['requestedPosition'] !== $b['requestedPosition']) {
                return $a['requestedPosition'] <=> $b['requestedPosition'];
            }

            return $b['recencyScore'] <=> $a['recencyScore'];
        });

        // Resolve collisions sequentially:
        // The first item at a given position retains that position.
        // Subsequent conflicting items (the older ones) are shifted down.
        $lastAssigned = -1;
        foreach ($itemsWithMetadata as $entry) {
            $pos = $entry['requestedPosition'];
            if ($pos <= $lastAssigned) {
                $pos = $lastAssigned + 1;
            }
            $entry['item']->setPosition($pos);
            $lastAssigned = $pos;
        }
    }

    protected function createTranslation(): TaxonTranslationInterface
    {
        return new TaxonTranslation();
    }
}
