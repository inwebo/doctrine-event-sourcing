<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateRoot\Missing;

use Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateRoot\AbstractAggregateRootException;

class StateClassArgumentException extends AbstractAggregateRootException
{
    public function __construct(string $subjectClass)
    {
        $message = sprintf('%s is annotated with %s, but argument `stateClass:` is missing, you MUST add it ', $subjectClass, '#[AggregateRoot]');
        parent::__construct($message);
    }
}
