<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateSource\Invalid;

class MutatorException extends \Exception
{
    public function __construct(string $subjectClass, string $property, string $getterArgument, string $setterArgument)
    {
        $message = sprintf('%s::%s is annotated with #[AggregateSource getter:"%s" setter"%s" but %s::%s() is not a valid method. You MUST implement it int the %s.',
            $subjectClass,
            $property,
            $getterArgument,
            $setterArgument,
            $subjectClass,
            $getterArgument,
            $subjectClass
        );
        parent::__construct($message);
    }
}
