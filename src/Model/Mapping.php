<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Model;

use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Model\Interface\StateInterface;

/**
 * Represent a valid Mapping of #[AggregateSource] in a subject.
 * The mutators exist in both Subject & State.
 */
readonly class Mapping
{
    public function __construct(
        private \ReflectionProperty $property,
        private \ReflectionMethod $subjectGetter,
        private \ReflectionMethod $subjectSetter,
        private \ReflectionMethod $stateGetter,
        private \ReflectionMethod $stateSetter,
    ) {
    }

    public function getProperty(): \ReflectionProperty
    {
        return $this->property;
    }

    public function getSubjectSetter(): \ReflectionMethod
    {
        return $this->subjectSetter;
    }

    public function getSubjectGetter(): \ReflectionMethod
    {
        return $this->subjectGetter;
    }

    public function getStateGetter(): \ReflectionMethod
    {
        return $this->stateGetter;
    }

    public function getStateSetter(): \ReflectionMethod
    {
        return $this->stateSetter;
    }

    public function invokeSubjectGetter(HasStatesInterface $subject): mixed
    {
        return $this->getSubjectGetter()->invoke($subject);
    }

    public function invokeSubjectSetter(HasStatesInterface $subject, mixed $value): void
    {
        $this->getSubjectSetter()->invoke($subject, $value);
    }

    public function invokeStateGetter(StateInterface $state): mixed
    {
        return $this->getStateGetter()->invoke($state);
    }

    public function invokeStateSetter(StateInterface $state, mixed $value): void
    {
        $this->getStateSetter()->invoke($state, $value);
    }
}
