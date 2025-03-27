<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Exception\Mapping;

use Inwebo\DoctrineEventSourcing\Exception\Mapping\EventSourcingAggregate\InvalidStateClassMethodException;

class InvalidMappingException extends \RuntimeException
{
    /**
     * @var InvalidStateClassMethodException[]
     */
    private array $exceptions;

    private function getMessages(): string
    {
        $message = '';

        foreach ($this->exceptions as $exception) {
            $message .= $exception->getMessage()."\n";
        }

        return $message;
    }

    /**
     * @return InvalidStateClassMethodException[]
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }

    /**
     * @param InvalidStateClassMethodException[] $exceptions
     */
    public function __construct(array $exceptions, int $code = 0, ?\Exception $previous = null)
    {
        $this->exceptions = $exceptions;
        parent::__construct($this->getMessages(), $code, $previous);
    }
}
