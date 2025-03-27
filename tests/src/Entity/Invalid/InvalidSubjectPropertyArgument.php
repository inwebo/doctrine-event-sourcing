<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid;

use Inwebo\DoctrineEventSourcing\Mapping\EventSource;
use Inwebo\DoctrineEventSourcing\Mapping\EventSourcingAggregate;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\EventSourcingStatesTrait;

#[EventSourcingAggregate(stateClass: InvalidSubjectPropertyArgumentState::class)]
class InvalidSubjectPropertyArgument implements HasStatesInterface
{
    use EventSourcingStatesTrait;

    public function __construct(private string $foo = 'bar')
    {
    }

    #[EventSource(method: 'getFoo', property: 'name')]
    public function setFoo(string $foo): void
    {
        $this->foo = $foo;
    }

    public function getFoo(): string
    {
        return $this->foo;
    }
}
