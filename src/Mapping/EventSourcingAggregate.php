<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Mapping;

/**
 * Subject MUST implement HasEventSourcingStatesInterface.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class EventSourcingAggregate
{
    public const string ARGUMENT_STATE_CLASS = 'stateClass';

    public function __construct(
        /**
         * which state class will represent subject's life cycle.
         */
        private string $stateClass, // @phpstan-ignore property.onlyWritten
    ) {
    }
}
