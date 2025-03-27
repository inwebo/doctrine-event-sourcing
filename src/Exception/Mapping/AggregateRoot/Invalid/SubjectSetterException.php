<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateRoot\Invalid;

class SubjectSetterException extends \Exception
{
    public function __construct(string $subjectClass, string $stateClass, string $subjectSetter)
    {
        $message = sprintf('%s is annotated with %s, but `subjectSetter: %s` is invalid, ` %s::%s()` MUST BE a valid method', $subjectClass, '#[AggregateRoot stateClass:"'.$stateClass.'", subjectSetter:"'.$subjectSetter.'"]]', $subjectSetter, $stateClass, $subjectSetter);
        parent::__construct($message);
    }
}
