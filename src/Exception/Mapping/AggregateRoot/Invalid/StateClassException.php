<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateRoot\Invalid;

class StateClassException extends \Exception
{
    public function __construct(string $subjectClass, string $stateClass)
    {
        $message = sprintf('%s is annotated with %s, but %s does not exist, `stateClass:` argument MUST BE a valid class', $subjectClass, '#[AggregateRoot stateClass:"'.$stateClass.'"]]', $stateClass);
        parent::__construct($message);
    }
}
