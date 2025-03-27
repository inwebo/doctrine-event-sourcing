<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid;

use Inwebo\DoctrineEventSourcing\Mapping\EventSourcingAggregate;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\DummyState;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\EventSourcingStatesTrait;

/**
 * Test missing EventSource attributes.
 */
#[EventSourcingAggregate(stateClass: DummyState::class)]
class MissingEventSourceAttribute implements HasStatesInterface
{
    use EventSourcingStatesTrait;
}
