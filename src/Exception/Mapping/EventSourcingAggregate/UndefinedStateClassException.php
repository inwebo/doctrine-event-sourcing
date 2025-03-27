<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Exception\Mapping\EventSourcingAggregate;

class UndefinedStateClassException extends \RuntimeException
{
    public function __construct(string $stateClass, int $code = 0, ?\Exception $previous = null)
    {
        $message = 'Undefined state class "'.$stateClass.'" did you forget to import it ?';

        parent::__construct($message, $code, $previous);
    }
}
