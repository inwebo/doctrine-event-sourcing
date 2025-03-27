<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\src\Entity\Person;

interface PersonInterface
{
    public function getFirstName(): string;

    public function getLastName(): string;
}
