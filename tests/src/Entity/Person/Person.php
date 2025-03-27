<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\src\Entity\Person;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Inwebo\DoctrineEventSourcing\Mapping\EventSource;
use Inwebo\DoctrineEventSourcing\Mapping\EventSourcingAggregate;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\EventSourcingStatesTrait;
use Inwebo\DoctrineEventSourcing\Tests\src\Listener\PersonStoreListener;

#[EventSourcingAggregate(stateClass: PersonState::class)]
#[ORM\EntityListeners([PersonStoreListener::class])]
class Person implements PersonInterface, HasStatesInterface
{
    use EventSourcingStatesTrait;

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    #[EventSource(method: 'getFirstName', property: 'firstName')]
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    #[EventSource(method: 'getLastName', property: 'lastName')]
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function setEventSourcingStates(array $states): void
    {
        $this->states = new ArrayCollection($states);
    }

    public function __construct(
        private string $firstName,
        private string $lastName,
    ) {
        $this->states = new ArrayCollection();
    }
}
