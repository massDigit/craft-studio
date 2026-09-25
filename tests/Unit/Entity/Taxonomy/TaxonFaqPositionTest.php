<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity\Taxonomy;

use App\Entity\Taxonomy\FaqItem;
use App\Entity\Taxonomy\Taxon;
use PHPUnit\Framework\TestCase;

final class TaxonFaqPositionTest extends TestCase
{
    public function testFaqItemPositionAcceptsNullAndDefaultsToZero(): void
    {
        $faqItem = new FaqItem();
        $this->assertSame(0, $faqItem->getPosition());

        $faqItem->setPosition(null);
        $this->assertSame(0, $faqItem->getPosition());

        $faqItem->setPosition(5);
        $this->assertSame(5, $faqItem->getPosition());

        $faqItem->setPosition(null);
        $this->assertSame(0, $faqItem->getPosition());
    }

    public function testPreservesDistinctPositions(): void
    {
        $taxon = new Taxon();

        $faq1 = new FaqItem();
        $faq1->setPosition(0);
        $taxon->addFaqItem($faq1);

        $faq2 = new FaqItem();
        $faq2->setPosition(1);
        $taxon->addFaqItem($faq2);

        $faq3 = new FaqItem();
        $faq3->setPosition(5);
        $taxon->addFaqItem($faq3);

        $taxon->normalizeFaqPositions();

        $this->assertSame(0, $faq1->getPosition());
        $this->assertSame(1, $faq2->getPosition());
        $this->assertSame(5, $faq3->getPosition());
    }

    public function testNewItemCollidingWithExistingTakesItsPositionAndShiftsExisting(): void
    {
        $taxon = new Taxon();

        // Simulate existing persisted FAQ with ID 10 and position 0
        $existingFaq = new FaqItem();
        $this->setEntityId($existingFaq, 10);
        $existingFaq->setPosition(0);
        $taxon->addFaqItem($existingFaq);

        // Simulate new FAQ (ID null) also assigned position 0
        $newFaq = new FaqItem();
        $newFaq->setPosition(0);
        $taxon->addFaqItem($newFaq);

        $taxon->normalizeFaqPositions();

        // The more recent item (newFaq) must take position 0
        // The older existing item (existingFaq) must be shifted to position 1
        $this->assertSame(0, $newFaq->getPosition());
        $this->assertSame(1, $existingFaq->getPosition());
    }

    public function testMultipleCollisionsCascadeCorrectly(): void
    {
        $taxon = new Taxon();

        $faqOld1 = new FaqItem();
        $this->setEntityId($faqOld1, 1);
        $faqOld1->setPosition(0);
        $taxon->addFaqItem($faqOld1);

        $faqOld2 = new FaqItem();
        $this->setEntityId($faqOld2, 2);
        $faqOld2->setPosition(1);
        $taxon->addFaqItem($faqOld2);

        // A new FAQ inserted with position 0
        $faqNew = new FaqItem();
        $faqNew->setPosition(0);
        $taxon->addFaqItem($faqNew);

        $taxon->normalizeFaqPositions();

        // faqNew takes 0
        // faqOld1 gets pushed to 1 -> collides with faqOld2 -> faqOld2 gets pushed to 2
        $this->assertSame(0, $faqNew->getPosition());
        $this->assertSame(1, $faqOld1->getPosition());
        $this->assertSame(2, $faqOld2->getPosition());
    }

    public function testTwoExistingPersistedItemsWithSamePositionResolveByHigherId(): void
    {
        $taxon = new Taxon();

        $older = new FaqItem();
        $this->setEntityId($older, 5);
        $older->setPosition(2);
        $taxon->addFaqItem($older);

        $moreRecent = new FaqItem();
        $this->setEntityId($moreRecent, 8);
        $moreRecent->setPosition(2);
        $taxon->addFaqItem($moreRecent);

        $taxon->normalizeFaqPositions();

        // More recent (id 8) takes position 2
        // Older (id 5) takes position 3
        $this->assertSame(2, $moreRecent->getPosition());
        $this->assertSame(3, $older->getPosition());
    }

    private function setEntityId(FaqItem $item, int $id): void
    {
        $reflection = new \ReflectionClass(FaqItem::class);
        $prop = $reflection->getProperty('id');
        $prop->setAccessible(true);
        $prop->setValue($item, $id);
    }
}
