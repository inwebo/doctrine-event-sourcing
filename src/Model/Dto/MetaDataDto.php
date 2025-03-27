<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Model\Dto;

use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Model\Interface\StateInterface;

/**
 * Represents configuration of annotated methods from subject eg :
 *
 * <code>
 * #[EventSource(method: 'getBar', property: 'Bar')]
 * public function setBar(mixed $bar): void {}
 * </code>
 */
readonly class MetaDataDto
{
    public function __construct(
        private \ReflectionMethod $subjectSetter,
        private \ReflectionMethod $subjectGetter,
        private \ReflectionMethod $stateGetter,
        private \ReflectionMethod $stateSetter,
        private \ReflectionProperty $subjectProperty,
    ) {
    }

    public function getSubjectSetter(): \ReflectionMethod
    {
        return $this->subjectSetter;
    }

    public function getStateGetter(): \ReflectionMethod
    {
        return $this->stateGetter;
    }

    public function getSubjectGetter(): \ReflectionMethod
    {
        return $this->subjectGetter;
    }

    public function getStateSetter(): \ReflectionMethod
    {
        return $this->stateSetter;
    }

    public function getSubjectProperty(): \ReflectionProperty
    {
        return $this->subjectProperty;
    }

    /**
     * @throws \ReflectionException
     */
    public function invokeSubjectGetter(HasStatesInterface $subject): mixed
    {
        return $this->getSubjectGetter()->invoke($subject);
    }

    /**
     * @throws \ReflectionException
     */
    public function invokeSubjectSetter(HasStatesInterface $subject, mixed $value): void
    {
        $this->getSubjectSetter()->invoke($subject, $value);
    }

    /**
     * @throws \ReflectionException
     */
    public function invokeStateGetter(StateInterface $state): mixed
    {
        return $this->getStateGetter()->invoke($state);
    }

    /**
     * @throws \ReflectionException
     */
    public function invokeStateSetter(StateInterface $state, mixed $value): void
    {
        $this->getStateSetter()->invoke($state, $value);
    }
}
