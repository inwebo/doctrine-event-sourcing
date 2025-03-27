<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Mapping;

/**
 * A subject's property to save in a state.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class AggregateSource
{
    public const string GETTER = 'getter';
    public const string SETTER = 'setter';

    public function __construct(
        /*
         * The getter method MUST exist in both Subject & State
         */
        private string $getter, // @phpstan-ignore property.onlyWritten
        /*
         * The setter method MUST exist in both Subject & State
         */
        private string $setter, // @phpstan-ignore property.onlyWritten
    ) {
    }
}
