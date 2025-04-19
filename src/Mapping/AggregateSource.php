<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Mapping;

/**
 * An attribute that defines the mapping between subject and state properties.
 * Used to mark properties that should be synchronized between a subject entity and its state.
 * Must be applied to properties that have corresponding getter and setter methods in both classes.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class AggregateSource
{
    /**
     * Constant representing the getter method type in property mapping.
     */
    public const string GETTER = 'getter';

    /**
     * Constant representing the setter method type in property mapping.
     */
    public const string SETTER = 'setter';

    /**
     * Constructs a new AggregateSource attribute instance.
     *
     * @param string $getter The name of the getter method that must exist in both Subject and State classes
     * @param string $setter The name of the setter method that must exist in both Subject and State classes
     */
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
