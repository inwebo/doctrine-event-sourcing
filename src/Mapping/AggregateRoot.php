<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Mapping;

/**
 * An entity with doctrine life cycle events.
 * The #[AggregateSource] properties will be saved.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class AggregateRoot
{
    public const string ARGUMENT_STATE_CLASS = 'stateClass';
    public const string ARGUMENT_SUBJECT_SETTER = 'subjectSetter';

    public function __construct(
        /*
         * MUST be the `targetEntity` argument of a Doctrine\ORM\Mapping\OneToMany attribute in subject class
         */
        private string $stateClass, // @phpstan-ignore property.onlyWritten
        /*
         * MUST be the setter of a Doctrine\ORM\Mapping\ManyToOne `inversedBy` argument in a state class
         */
        private string $subjectSetter, // @phpstan-ignore property.onlyWritten
    ) {
    }
}
