<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateRoot;

use Inwebo\DoctrineEventSourcing\Mapping\AggregateRoot;

class AbstractAggregateRootException extends \Exception
{
    protected string $annotation = 'AggregateRoot';

    protected function getAttribute(): string
    {
        return sprintf('#[%s %s: "", %s: ""]', $this->annotation, AggregateRoot::ARGUMENT_STATE_CLASS, AggregateRoot::ARGUMENT_SUBJECT_SETTER);
    }
}
