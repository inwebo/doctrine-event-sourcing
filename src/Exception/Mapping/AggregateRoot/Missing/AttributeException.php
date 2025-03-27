<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateRoot\Missing;

use Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateRoot\AbstractAggregateRootException;

class AttributeException extends AbstractAggregateRootException
{
    public function __construct(string $subjectClass)
    {
        $message = sprintf('%s is not annotated with %s, try to add it', $subjectClass, $this->getAttribute());
        parent::__construct($message);
    }
}
