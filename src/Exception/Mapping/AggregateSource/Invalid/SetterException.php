<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateSource\Invalid;

class SetterException extends MutatorException
{
    public function __construct(string $subjectClass, string $property, string $getterArgument, string $setterArgument)
    {
        parent::__construct($subjectClass, $property, $getterArgument, $setterArgument);
    }
}
