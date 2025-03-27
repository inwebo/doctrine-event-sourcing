<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Exception\Mapping\EventSourcingAggregate;

use Inwebo\DoctrineEventSourcing\Mapping\EventSourcingAggregate;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;

class MissingAttributeException extends \RuntimeException
{
    public function __construct(HasStatesInterface $class, int $code = 0, ?\Exception $previous = null)
    {
        $message = sprintf('Did you forget to use %s attribute with %s ? ex :', EventSourcingAggregate::class, get_class($class))."\n".
        '#[EventSourcingAggregate(stateClass: FooState::class)]';

        parent::__construct($message, $code, $previous);
    }
}
