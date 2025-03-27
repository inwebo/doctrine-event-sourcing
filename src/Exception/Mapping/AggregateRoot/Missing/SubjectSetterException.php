<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateRoot\Missing;

class SubjectSetterException extends \Exception
{
    public function __construct(string $subjectClass, string $stateClass)
    {
        $message = sprintf('%s is annotated with %s, but `subjectSetter:` is missing, try to add it', $subjectClass, '#[AggregateRoot stateClass:"'.$stateClass.'"]]');
        parent::__construct($message);
    }
}
