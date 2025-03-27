<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid;

use Inwebo\DoctrineEventSourcing\Mapping\EventSource;
use Inwebo\DoctrineEventSourcing\Mapping\EventSourcingAggregate;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\EventSourcingStatesTrait;

#[EventSourcingAggregate(stateClass: InvalidSubjectState::class)]
class InvalidSubjectMethod implements HasStatesInterface
{
    use EventSourcingStatesTrait;

    public function __construct(
        private string $name = 'name',
    ) {
    }

    #[EventSource(method: 'getName', property: 'name')]
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
