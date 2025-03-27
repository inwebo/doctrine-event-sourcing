<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid;

use Inwebo\DoctrineEventSourcing\Mapping\AggregateRoot;
use Inwebo\DoctrineEventSourcing\Mapping\AggregateSource;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\EventSourcingStatesTrait;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Foo\FooState;

#[AggregateRoot(stateClass: FooState::class, subjectSetter: 'setFoo')]
class InvalidSetterArgument implements HasStatesInterface
{
    use EventSourcingStatesTrait;

    public function __construct(
        #[AggregateSource(getter: 'getFirstName', setter: 'invalid')]
        private string $firstName,
    ) {
    }
}
