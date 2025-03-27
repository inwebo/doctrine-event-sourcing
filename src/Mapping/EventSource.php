<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Mapping;

#[\Attribute(\Attribute::TARGET_METHOD)]
/**
 * Must annotate a setter or mutator method methods of a subject.
 * Must be present one in a subject class.
 */
class EventSource
{
    public const string ARGUMENT_METHOD_NAME = 'method';
    public const string ARGUMENT_PROPERTY_NAME = 'property';

    public function __construct(
        /**
         * Which method of a state will be invoked to populate annotated method ?
         */
        private string $method, // @phpstan-ignore property.onlyWritten
        /**
         * Property name to display in historic.
         */
        private string $property, // @phpstan-ignore property.onlyWritten
    ) {
    }
}
