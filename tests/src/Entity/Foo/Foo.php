<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\src\Entity\Foo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Inwebo\DoctrineEventSourcing\Listener\StoreListener;
use Inwebo\DoctrineEventSourcing\Mapping\AggregateRoot;
use Inwebo\DoctrineEventSourcing\Mapping\AggregateSource;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Model\Interface\StateInterface;

#[AggregateRoot(stateClass: FooState::class, subjectSetter: 'setFoo')]
#[ORM\EntityListeners([StoreListener::class])]
class Foo implements HasStatesInterface
{
    #[ORM\Id, ORM\GeneratedValue(strategy: 'IDENTITY'), ORM\Column(type: 'integer', unique: true)]
    private ?int $id = null;

    #[ORM\OneToMany(targetEntity: FooState::class, mappedBy: 'foo', cascade: ['persist'])]
    private Collection $states;

    public function __construct(
        #[AggregateSource(getter: 'getFirstName', setter: 'setFirstName')]
        #[ORM\Column(type: Types::TEXT)]
        private string $firstName,
        #[AggregateSource(getter: 'getLastName', setter: 'setLastName')]
        #[ORM\Column(type: Types::TEXT)]
        private string $lastName,
        #[AggregateSource(getter: 'getBirthDate', setter: 'setBirthDate')]
        #[ORM\Column(type: Types::DATETIME_MUTABLE)]
        private \DateTimeInterface $birthDate,
    ) {
        $this->states = new ArrayCollection();
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getBirthDate(): \DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): void
    {
        $this->birthDate = $birthDate;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<StateInterface>
     */
    public function getEventSourcingStates(): Collection
    {
        return $this->states;
    }
}
