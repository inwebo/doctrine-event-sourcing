<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Exception\Mapping\EventSource;

use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;

class PropertyArgumentException extends \RuntimeException
{
    protected string $invalidMessage = 'Method %s::%s() has an invalid annotation #[EventSource]'."\n\t".
        'Argument property: %s, but  but this property doesn\'t exists %s::$%s.'
    ;

    public function __construct(HasStatesInterface $subject, string $methodName, string $propertyArgument, int $code = 0, ?\Exception $previous = null)
    {
        $message = sprintf(
            $this->invalidMessage,
            $subject::class,
            $methodName,
            $propertyArgument,
            $subject::class,
            $propertyArgument,
        );

        parent::__construct($message, $code, $previous);
    }
}
