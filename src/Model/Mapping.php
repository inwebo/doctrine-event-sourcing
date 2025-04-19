<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Model;

use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Model\Interface\StateInterface;

/**
 * The Mapping class defines the relationship between subject and state properties.
 * It provides reflection-based access to getters and setters for both subject and state objects,
 * facilitating the transfer of values between them.
 */
readonly class Mapping
{
    /**
     * Constructs a new Mapping instance.
     *
     * @param \ReflectionProperty $property      The reflected property being mapped
     * @param \ReflectionMethod   $subjectGetter The getter method for the subject property
     * @param \ReflectionMethod   $subjectSetter The setter method for the subject property
     * @param \ReflectionMethod   $stateGetter   The getter method for the state property
     * @param \ReflectionMethod   $stateSetter   The setter method for the state property
     */
    public function __construct(
        private \ReflectionProperty $property,
        private \ReflectionMethod $subjectGetter,
        private \ReflectionMethod $subjectSetter,
        private \ReflectionMethod $stateGetter,
        private \ReflectionMethod $stateSetter,
    ) {
    }

    /**
     * Returns the reflected property.
     *
     * @return \ReflectionProperty The property being mapped
     */
    public function getProperty(): \ReflectionProperty
    {
        return $this->property;
    }

    /**
     * Returns the subject's setter method.
     *
     * @return \ReflectionMethod The setter method for the subject
     */
    public function getSubjectSetter(): \ReflectionMethod
    {
        return $this->subjectSetter;
    }

    /**
     * Returns the subject's getter method.
     *
     * @return \ReflectionMethod The getter method for the subject
     */
    public function getSubjectGetter(): \ReflectionMethod
    {
        return $this->subjectGetter;
    }

    /**
     * Returns the state's getter method.
     *
     * @return \ReflectionMethod The getter method for the state
     */
    public function getStateGetter(): \ReflectionMethod
    {
        return $this->stateGetter;
    }

    /**
     * Returns the state's setter method.
     *
     * @return \ReflectionMethod The setter method for the state
     */
    public function getStateSetter(): \ReflectionMethod
    {
        return $this->stateSetter;
    }

    /**
     * Invokes the subject's getter method to retrieve a property value.
     *
     * @param HasStatesInterface $subject The subject instance
     *
     * @return mixed The value returned by the getter
     */
    public function invokeSubjectGetter(HasStatesInterface $subject): mixed
    {
        return $this->getSubjectGetter()->invoke($subject);
    }

    /**
     * Invokes the subject's setter method to set a property value.
     *
     * @param HasStatesInterface $subject The subject instance
     * @param mixed              $value   The value to set
     */
    public function invokeSubjectSetter(HasStatesInterface $subject, mixed $value): void
    {
        $this->getSubjectSetter()->invoke($subject, $value);
    }

    /**
     * Invokes the state's getter method to retrieve a property value.
     *
     * @param StateInterface $state The state instance
     *
     * @return mixed The value returned by the getter
     */
    public function invokeStateGetter(StateInterface $state): mixed
    {
        return $this->getStateGetter()->invoke($state);
    }

    /**
     * Invokes the state's setter method to set a property value.
     *
     * @param StateInterface $state The state instance
     * @param mixed          $value The value to set
     */
    public function invokeStateSetter(StateInterface $state, mixed $value): void
    {
        $this->getStateSetter()->invoke($state, $value);
    }
}
