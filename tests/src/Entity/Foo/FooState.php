<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\src\Entity\Foo;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Inwebo\DoctrineEventSourcing\Model\Interface\StateInterface;

class FooState implements StateInterface
{
    #[ORM\ManyToOne(targetEntity: Foo::class, inversedBy: 'states')]
    #[ORM\JoinColumn(name: 'foo_id', referencedColumnName: 'id', nullable: false)]
    private Foo $foo;

    public function __construct(
        #[ORM\Column(type: Types::TEXT, nullable: true)]
        private ?string $firstName = null,
        #[ORM\Column(type: Types::TEXT, nullable: true)]
        private ?string $lastName = null,
        #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
        private ?\DateTimeInterface $birthDate = null,
    ) {
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): void
    {
        $this->birthDate = $birthDate;
    }

    public function setFoo(Foo $foo): void
    {
        $this->foo = $foo;
    }

    public function getFoo(): Foo
    {
        return $this->foo;
    }
}
