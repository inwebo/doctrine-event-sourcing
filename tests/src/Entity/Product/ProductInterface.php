<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\src\Entity\Product;

interface ProductInterface
{
    public function getName(): string;

    public function getPrice(): float;

    public function getDiscountStart(): ?\DateTimeInterface;

    public function getDiscountEnd(): ?\DateTimeInterface;
}
