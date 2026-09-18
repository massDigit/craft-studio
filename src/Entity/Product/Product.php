<?php

declare(strict_types=1);

namespace App\Entity\Product;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Product as BaseProduct;
use Sylius\Component\Product\Model\ProductTranslationInterface;
use Sylius\MolliePlugin\Entity\ProductInterface;
use Sylius\MolliePlugin\Entity\ProductTrait;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_product')]
class Product extends BaseProduct implements ProductInterface
{
    use ProductTrait;

    /** @var Collection<int, ProductAudio> */
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductAudio::class, cascade: ['all'], orphanRemoval: true)]
    private Collection $audios;

    /** @var Collection<int, ProductTechnicalSheetItem> */
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductTechnicalSheetItem::class, cascade: ['all'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $technicalSheetItems;

    public function __construct()
    {
        parent::__construct();

        $this->audios = new ArrayCollection();
        $this->technicalSheetItems = new ArrayCollection();
    }

    protected function createTranslation(): ProductTranslationInterface
    {
        return new ProductTranslation();
    }

    /**
     * @return Collection<int, ProductAudio>
     */
    public function getAudios(): Collection
    {
        return $this->audios;
    }

    public function hasAudios(): bool
    {
        return !$this->audios->isEmpty();
    }

    public function addAudio(ProductAudio $audio): self
    {
        if (!$this->audios->contains($audio)) {
            $this->audios->add($audio);
            $audio->setProduct($this);
        }

        return $this;
    }

    public function removeAudio(ProductAudio $audio): self
    {
        if ($this->audios->removeElement($audio)) {
            if ($audio->getProduct() === $this) {
                $audio->setProduct(null);
            }
        }

        return $this;
    }

    public function getPrimaryAudio(): ?ProductAudio
    {
        foreach ($this->audios as $audio) {
            if ($audio->isPrimary()) {
                return $audio;
            }
        }

        return $this->audios->first() ?: null;
    }

    /**
     * @return Collection<int, ProductTechnicalSheetItem>
     */
    public function getTechnicalSheetItems(): Collection
    {
        return $this->technicalSheetItems;
    }

    public function hasTechnicalSheetItems(): bool
    {
        return !$this->technicalSheetItems->isEmpty();
    }

    public function addTechnicalSheetItem(ProductTechnicalSheetItem $item): self
    {
        if (!$this->technicalSheetItems->contains($item)) {
            $this->technicalSheetItems->add($item);
            $item->setProduct($this);
        }

        return $this;
    }

    public function removeTechnicalSheetItem(ProductTechnicalSheetItem $item): self
    {
        if ($this->technicalSheetItems->removeElement($item)) {
            if ($item->getProduct() === $this) {
                $item->setProduct(null);
            }
        }

        return $this;
    }
}
