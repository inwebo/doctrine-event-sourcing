<?php

declare(strict_types=1);

/**
 * The Aggregator class is responsible for managing state transitions and mappings between subject and state objects.
 * It facilitates the application, creation, and update of states for entities that implement the HasStatesInterface.
 * The class relies on a MappingFactory for defining and retrieving the mappings between the subject and state properties.
 *
 * This class is the core component for state management, providing functionality to:
 * - Create new states from existing subjects
 * - Apply state changes to subjects
 * - Handle Doctrine's PreUpdate events for state changes
 * - Manage property mappings between subjects and states
 */

namespace Inwebo\DoctrineEventSourcing\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Model\Interface\StateInterface;

readonly class Aggregator
{
    /**
     * Constructs a new Aggregator instance.
     *
     * @param MappingFactory $factory The factory responsible for creating and managing property mappings
     */
    public function __construct(private MappingFactory $factory)
    {
    }

    /**
     * Returns the MappingFactory instance used by this Aggregator.
     *
     * @return MappingFactory The mapping factory instance managing property mappings
     */
    public function getMappingFactory(): MappingFactory
    {
        return $this->factory;
    }

    /**
     * Applies the values from a state object to a subject.
     *
     * Iterates through all mapped properties and copies their values from the state to the subject.
     * Handles null values appropriately by checking property nullability constraints.
     *
     * @param HasStatesInterface $subject The subject entity to receive state values
     * @param StateInterface     $state   The state object containing the values to apply
     *
     * @return HasStatesInterface The modified subject with updated state values
     */
    public function applyState(HasStatesInterface $subject, StateInterface $state): HasStatesInterface
    {
        foreach ($this->getMappingFactory()->getMapping() as $mapping) {
            $value = $mapping->invokeStateGetter($state);
            $parameterType = $mapping->getSubjectSetter()->getParameters()[0]->getType();
            /*
             * Can't set a null value to a non-nullable property.
             */
            if (false === is_null($parameterType) && false === $parameterType->allowsNull() && true === is_null($value)) {
                continue;
            } else {
                $mapping->invokeSubjectSetter($subject, $value);
            }
        }

        return $subject;
    }

    /**
     * Creates a new state object from a subject's current values.
     *
     * Creates and populates a new state instance by copying all mapped property
     * values from the subject and sets the subject reference in the state.
     *
     * @param HasStatesInterface $subject The subject to create a state from
     *
     * @return StateInterface The newly created and populated state object
     */
    public function createState(HasStatesInterface $subject): StateInterface
    {
        $state = $this->getMappingFactory()->newStateClass();

        foreach ($this->getMappingFactory()->getMapping() as $mapping) {
            $value = $mapping->invokeSubjectGetter($subject);
            $mapping->invokeStateSetter($state, $value);
        }

        $this->getMappingFactory()->getSubjectSetter()->invoke($state, $subject);

        return $state;
    }

    /**
     * Creates a new state object from Doctrine's PreUpdate event changes.
     *
     * Processes the change set from a Doctrine PreUpdate event and creates a new state
     * containing the updated values. Only changed properties are updated in the subject
     * before creating the new state.
     *
     * @param PreUpdateEventArgs $args The Doctrine event containing the changes
     *
     * @return StateInterface A new state object with the updated values
     */
    public function createStateFromChange(PreUpdateEventArgs $args): StateInterface
    {
        /** @var HasStatesInterface $subject */
        $subject = $args->getObject();

        $changeSet = $args->getEntityChangeSet();
        foreach ($changeSet as $fieldName => $value) {
            /** @var Mapping|null $mapping */
            $mapping = $this->getMappingFactory()->getMapping()->findFirst(function (int $key, Mapping $mapping) use ($fieldName) {
                return $mapping->getProperty()->getName() === $fieldName;
            });

            if (false === is_null($mapping)) {
                $mapping
                    ->getSubjectSetter()
                    ->invoke($subject, $value[1]);
            }
        }

        return $this->createState($subject);
    }

    /**
     * @return array<int, HasStatesInterface>
     */
    public function historic(HasStatesInterface $subject): array
    {
        $historic = [];

        $clone = null;
        $states = null;
        $previousState = null;
        foreach ($subject->getEventSourcingStates() as $state) {
            $clone = (true === is_null($clone)) ? clone $subject : clone $clone;
            $newState = $this->applyState($clone, $state);

            if (is_null($states)) {
                $states = new ArrayCollection();
            } else {
                $states = clone $states;
                $states->add($previousState);
            }

            $newState->setEventSourcingStates($states);

            $historic[] = $newState;

            $previousState = $state;
        }

        return $historic;
    }

    /**
     * Creates a new Aggregator instance for the given subject class.
     *
     * Factory method that creates an Aggregator with a new MappingFactory
     * configured for the specified subject class.
     *
     * @param class-string $subject The fully qualified class name of the subject
     *
     * @return self A new Aggregator instance configured for the subject class
     */
    public static function new(string $subject): self
    {
        return new self(new MappingFactory($subject));
    }
}
