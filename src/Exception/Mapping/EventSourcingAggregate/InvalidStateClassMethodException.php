<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Exception\Mapping\EventSourcingAggregate;

class InvalidStateClassMethodException extends \RuntimeException
{
    private const string ATTRIBUTE_PATTERN = "#[EventSource(method: '%s', property: '%s')]";

    private function getAttributePattern(string $method, string $property): string
    {
        return sprintf(self::ATTRIBUTE_PATTERN, $method, $property);
    }

    public function __construct(string $class, string $method, string $property, string $stateClass, int $code = 0, ?\Exception $previous = null)
    {
        $message = sprintf(
            "\n".'Please check your mapping in %s :'."\n".
            "\t".$this->getAttributePattern($method, $property)."\n".
            "\tBut method : %s::%s doesn't exists", $class, $stateClass, $method);

        parent::__construct($message, $code, $previous);
    }
}
