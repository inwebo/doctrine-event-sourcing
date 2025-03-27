<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Exception\Mapping\EventSource;

class MissingAttributeException extends \RuntimeException
{
    public function __construct(object $class, int $code = 0, ?\Exception $previous = null)
    {
        $message = "You MUST add at least one attribute : #[EventSource(method: 'getFoo', property: 'foo')]\n".
            'to subject : '.get_class($class);

        parent::__construct($message, $code, $previous);
    }
}
