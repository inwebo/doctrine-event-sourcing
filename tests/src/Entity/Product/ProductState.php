<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\src\Entity\Product;

use Inwebo\DoctrineEventSourcing\Model\Interface\StateInterface;

class ProductState implements ProductInterface, StateInterface
{
    public function __construct(
        private string $name,
        private float $price,
        private \DateTimeInterface $createdAt,
        private ?\DateTimeInterface $discountStart = null,
        private ?\DateTimeInterface $discountEnd = null,
    ) {
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

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    public function setDiscountStart(?\DateTimeInterface $discountStart): void
    {
        $this->discountStart = $discountStart;
    }

    public function setDiscountEnd(?\DateTimeInterface $discountEnd): void
    {
        $this->discountEnd = $discountEnd;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
