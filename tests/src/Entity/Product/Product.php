<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\src\Entity\Product;

use Doctrine\Common\Collections\ArrayCollection;
use Inwebo\DoctrineEventSourcing\Mapping\EventSource;
use Inwebo\DoctrineEventSourcing\Mapping\EventSourcingAggregate;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\EventSourcingStatesTrait;

#[EventSourcingAggregate(stateClass: ProductState::class)]
class Product implements HasStatesInterface, ProductInterface
{
    use EventSourcingStatesTrait;

    public function __construct(
        private string $name,
        private float $price,
        private \DateTimeInterface $createdAt,
        private ?\DateTimeInterface $discountStart = null,
        private ?\DateTimeInterface $discountEnd = null,
    ) {
        $this->states = new ArrayCollection();
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }


    public function getDiscountStart(): ?\DateTimeInterface
    {
        return $this->discountStart;
    }

    public function getDiscountEnd(): ?\DateTimeInterface
    {
        return $this->discountEnd;
    }

    #[EventSource(method: 'getName', property: 'name')]
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    #[EventSource(method: 'getPrice', property: 'price')]
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    #[EventSource(method: 'getDiscountStart', property: 'discountStart')]
    public function setDiscountStart(?\DateTimeInterface $discountStart): void
    {
        $this->discountStart = $discountStart;
    }

    #[EventSource(method: 'getDiscountEnd', property: 'discountEnd')]
    public function setDiscountEnd(?\DateTimeInterface $discountEnd): void
    {
        $this->discountEnd = $discountEnd;
    }

    #[EventSource(method: 'getCreatedAt', property: 'createdAt')]
    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setEventSourceStates(array $sources): void
    {
        $this->states = new ArrayCollection($sources);
    }
}
