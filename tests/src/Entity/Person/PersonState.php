<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\src\Entity\Person;

use Inwebo\DoctrineEventSourcing\Model\Interface\StateInterface;

class PersonState implements PersonInterface, StateInterface
{
    public function __construct(
        private ?string $firstName = null,
        private ?string $lastName = null,
    ) {
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }
}
