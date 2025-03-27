<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid;

use Inwebo\DoctrineEventSourcing\Mapping\EventSourcingAggregate;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\EventSourcingStatesTrait;

/**
 * Test Unknown stateClass.
 */
#[EventSourcingAggregate(stateClass: UnknowState::class)]
class InvalidStateClass implements HasStatesInterface
{
    use EventSourcingStatesTrait;
}
