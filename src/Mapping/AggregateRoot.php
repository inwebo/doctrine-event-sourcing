<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Mapping;

/**
 * Attribute to mark a class as an Aggregate Root in the event sourcing pattern.
 *
 * This attribute establishes the relationship between an aggregate root entity and its state objects,
 * defining how state changes are tracked and managed through Doctrine ORM mappings.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class AggregateRoot
{
    /**
     * Constant representing the state class constructor argument name.
     */
    public const string ARGUMENT_STATE_CLASS = 'stateClass';

    /**
     * Constant representing the subject setter constructor argument name.
     */
    public const string ARGUMENT_SUBJECT_SETTER = 'subjectSetter';

    public function __construct(
        /**
         * The fully qualified class name of the state entity.
         * MUST be the `targetEntity` argument of a Doctrine\ORM\Mapping\OneToMany attribute in subject class.
         */
        private string $stateClass, // @phpstan-ignore property.onlyWritten
        /**
         * The method name used to set the subject reference in the state entity.
         * MUST be the setter of a Doctrine\ORM\Mapping\ManyToOne `inversedBy` argument in a state class.
         */
        private string $subjectSetter, // @phpstan-ignore property.onlyWritten
    ) {
    }
}
