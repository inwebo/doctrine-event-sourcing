<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid;

use Inwebo\DoctrineEventSourcing\Mapping\AggregateRoot;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\EventSourcingStatesTrait;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Foo\FooState;

#[AggregateRoot(stateClass: FooState::class, subjectSetter: 'setBar')]
class InvalidSubjectSetter implements HasStatesInterface
{
    use EventSourcingStatesTrait;
}
