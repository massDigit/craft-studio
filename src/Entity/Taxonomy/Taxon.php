<?php

declare(strict_types=1);

namespace App\Entity\Taxonomy;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Taxon as BaseTaxon;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_taxon')]
class Taxon extends BaseTaxon
{
    #[ORM\OneToMany(mappedBy: 'taxon', targetEntity: FaqItem::class, cascade: ['all'], orphanRemoval: true)]
    private $faqItems;

    public function __construct()
    {
        parent::__construct();
        $this->faqItems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getFaqItems(): \Doctrine\Common\Collections\Collection
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

    protected function createTranslation(): TaxonTranslationInterface
    {
        return new TaxonTranslation();
    }
}
