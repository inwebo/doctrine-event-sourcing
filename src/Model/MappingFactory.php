<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateRoot\Invalid;
use Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateRoot\Invalid\StateClassException;
use Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateRoot\Missing\AttributeException;
use Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateRoot\Missing\StateClassArgumentException;
use Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateRoot\Missing\SubjectSetterException;
use Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateSource\Invalid\GetterException;
use Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateSource\Invalid\SetterException;
use Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateSource\Missing\GetterArgumentException;
use Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateSource\Missing\SetterArgumentException;
use Inwebo\DoctrineEventSourcing\Exception\MissingHasStatesInterfaceException;
use Inwebo\DoctrineEventSourcing\Mapping;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Model\Interface\StateInterface;
use Inwebo\DoctrineEventSourcing\Model\Mapping as MappingDto;

/**
 * Factory class responsible for creating and managing mappings between aggregate roots and their states.
 * It analyzes class attributes and relationships to establish property mappings and validate class structures.
 *
 * This class handles:
 * - Validation of aggregate root and source attributes
 * - Mapping of getters and setters between subject and state classes
 * - Creation and management of property mappings
 * - Verification of required interfaces and class relationships
 */
class MappingFactory
{
    public const string HAS_STATES_INTERFACE = HasStatesInterface::class;
    /**
     * @var class-string FQCN subject
     */
    protected string $subjectClass;
    /**
     * @var string FQCN state
     */
    protected string $stateClass;
    /**
     * @var \ReflectionClass<object>
     */
    protected \ReflectionClass $subjectReflectionClass;
    /**
     * @var ArrayCollection<int, MappingDto>
     */
    protected ArrayCollection $mapping;

    protected \ReflectionMethod $subjectSetter;

    /**
     * Creates a new MappingFactory instance for the given subject class.
     * Validates the class structure and establishes mappings between the subject and its state class.
     *
     * @param class-string $subjectClass The fully qualified class name of the subject to be mapped
     *
     * @throws \ErrorException                    If the subject class is Unknown
     * @throws AttributeException                 If the subject is not annotated with #[AggregateRoot]
     * @throws SetterArgumentException            If #[AggregateRoot] stateClass is missing
     * @throws GetterException                    If a getter is missing in subjectClass or stateClass
     * @throws SetterException                    If a setter is unknown
     * @throws GetterArgumentException            If #[AggregateSource] getter argument is missing
     * @throws StateClassArgumentException        If #[AggregateRoot] stateClass is missing
     * @throws SubjectSetterException             If #[AggregateRoot] subjectSetter argument is missing
     * @throws StateClassException                If #[AggregateRoot] stateClass argument is invalid
     * @throws Invalid\SubjectSetterException     If #[AggregateRoot] subjectSetter argument is invalid
     * @throws MissingHasStatesInterfaceException If the subject class doesn't implement HasStatesInterface
     * @throws \ReflectionException               Is never thrown
     */
    public function __construct(string $subjectClass)
    {
        $this->setSubjectClass($subjectClass);
        $this->subjectReflectionClass = new \ReflectionClass($this->getSubjectClass());
        $this->mapping = new ArrayCollection();
        $this->setAggregateRoot();
        $this->setAggregateSource();
    }

    /**
     * Sets and validates the subject class, ensuring it implements the required interface.
     *
     * @param class-string $subjectClass The FQCN of the subject class
     *
     * @throws MissingHasStatesInterfaceException If the class doesn't implement HasStatesInterface
     * @throws \ErrorException                    If the class doesn't exist
     */
    protected function setSubjectClass(string $subjectClass): void
    {
        $classImplements = @class_implements($subjectClass);
        if (false !== $classImplements) {
            if (!in_array(self::HAS_STATES_INTERFACE, $classImplements, true)) {
                throw new MissingHasStatesInterfaceException($subjectClass);
            }

            $this->subjectClass = $subjectClass;
        } else {
            throw new \ErrorException(sprintf('Unknown or missing subject class: %s.', $subjectClass));
        }
    }

    /**
     * Returns the reflection method for setting the subject on the state class.
     */
    public function getSubjectSetter(): \ReflectionMethod
    {
        return $this->subjectSetter;
    }

    /**
     * Returns the fully qualified class name of the subject.
     *
     * @return class-string The FQCN of the subject class
     */
    public function getSubjectClass(): string
    {
        return $this->subjectClass;
    }

    /**
     * Returns the fully qualified class name of the state class.
     */
    public function getStateClass(): string
    {
        return $this->stateClass;
    }

    /**
     * Creates and returns a new instance of the state class.
     */
    public function newStateClass(): StateInterface
    {
        /** @var StateInterface $stateClass */
        $stateClass = new ($this->getStateClass());

        return $stateClass;
    }

    /**
     * @return \ReflectionClass<object>
     */
    /**
     * Returns the reflection class instance for the subject class.
     *
     * @return \ReflectionClass<object>
     */
    public function getSubjectReflectionClass(): \ReflectionClass
    {
        return $this->subjectReflectionClass;
    }

    /**
     * @return ArrayCollection<int, MappingDto>
     */
    /**
     * Returns the collection of mappings between subject and state properties.
     *
     * @return ArrayCollection<int, MappingDto>
     */
    public function getMapping(): ArrayCollection
    {
        return $this->mapping;
    }

    /**
     * @throws AttributeException
     * @throws Invalid\SubjectSetterException
     * @throws StateClassArgumentException
     * @throws StateClassException
     * @throws SubjectSetterException
     */
    /**
     * Processes and validates the AggregateRoot attribute on the subject class.
     * Sets up the state class and subject setter configurations.
     *
     * @throws AttributeException             If the AggregateRoot attribute is missing
     * @throws Invalid\SubjectSetterException If the subject setter is invalid
     * @throws StateClassArgumentException    If the state class argument is missing
     * @throws StateClassException            If the state class is invalid
     * @throws SubjectSetterException         If the subject setter is missing
     */
    protected function setAggregateRoot(): void
    {
        $attributes = $this->getSubjectReflectionClass()->getAttributes(Mapping\AggregateRoot::class);

        if (0 === count($attributes)) {
            throw new AttributeException($this->getSubjectClass());
        }

        $attribute = $attributes[0];

        if (false === isset($attribute->getArguments()[Mapping\AggregateRoot::ARGUMENT_STATE_CLASS])) {
            throw new StateClassArgumentException($this->getSubjectClass());
        }
        /** @var string $stateClass */
        $stateClass = $attribute->getArguments()[Mapping\AggregateRoot::ARGUMENT_STATE_CLASS];

        if (false === class_exists($stateClass)) {
            throw new StateClassException($this->getSubjectClass(), $stateClass);
        }

        $this->stateClass = $stateClass;

        if (false === isset($attribute->getArguments()[Mapping\AggregateRoot::ARGUMENT_SUBJECT_SETTER])) {
            throw new SubjectSetterException($this->getSubjectClass(), $stateClass);
        }
        /** @var string $subjectSetter */
        $subjectSetter = $attribute->getArguments()[Mapping\AggregateRoot::ARGUMENT_SUBJECT_SETTER];

        try {
            $this->subjectSetter = $this->getMutator($this->stateClass, $subjectSetter);
        } catch (\ReflectionException $e) {
            throw new Invalid\SubjectSetterException($this->getSubjectClass(), $stateClass, $subjectSetter);
        }
    }

    /**
     * Processes and validates AggregateSource attributes on subject class properties.
     * Sets up mappings between subject and state properties, including their getters and setters.
     *
     * Iterates through all properties of the subject class, looking for those annotated with #[AggregateSource].
     * For each annotated property, it validates and creates mappings for the getter and setter methods
     * in both the subject and state classes.
     *
     * @throws GetterArgumentException If the getter method name is missing in the attribute
     * @throws SetterArgumentException If the setter method name is missing in the attribute
     * @throws GetterException         If the getter method doesn't exist in either subject or state class
     * @throws SetterException         If the setter method doesn't exist in either subject or state class
     */
    public function setAggregateSource(): void
    {
        foreach ($this->getSubjectReflectionClass()->getProperties() as $property) {
            $attributes = $property->getAttributes(Mapping\AggregateSource::class);
            if (isset($attributes[0])) {
                $attribute = $attributes[0];

                if (false === isset($attribute->getArguments()[Mapping\AggregateSource::GETTER])) {
                    throw new GetterArgumentException($this->getStateClass(), $property->getName());
                }
                /** @var string $getterMethodName */
                $getterMethodName = $attribute->getArguments()[Mapping\AggregateSource::GETTER];

                if (false === isset($attribute->getArguments()[Mapping\AggregateSource::SETTER])) {
                    throw new SetterArgumentException($this->getStateClass(), $property->getName());
                }
                /** @var string $setterMethodName */
                $setterMethodName = $attribute->getArguments()[Mapping\AggregateSource::SETTER];

                try {
                    $subjectGetterMethod = $this->getMutator($this->getSubjectClass(), $getterMethodName);
                } catch (\ReflectionException $e) {
                    throw new GetterException($this->getSubjectClass(), $property->getName(), $getterMethodName, $setterMethodName);
                }
                try {
                    $stateGetterMethod = $this->getMutator($this->getStateClass(), $getterMethodName);
                } catch (\ReflectionException $e) {
                    throw new GetterException($this->getStateClass(), $property->getName(), $getterMethodName, $setterMethodName);
                }
                try {
                    $subjectSetterMethod = $this->getMutator($this->getSubjectClass(), $setterMethodName);
                } catch (\ReflectionException $e) {
                    throw new SetterException($this->getSubjectClass(), $property->getName(), $getterMethodName, $setterMethodName);
                }
                try {
                    $stateSetterMethod = $this->getMutator($this->getStateClass(), $setterMethodName);
                } catch (\ReflectionException $e) {
                    throw new SetterException($this->getStateClass(), $property->getName(), $getterMethodName, $setterMethodName);
                }

                $this->mapping->add(
                    new MappingDto(
                        $property,
                        $subjectGetterMethod,
                        $subjectSetterMethod,
                        $stateGetterMethod,
                        $stateSetterMethod,
                    )
                );
            }
        }
    }

    /**
     * Creates a ReflectionMethod instance for the specified class and method.
     *
     * @throws \ReflectionException If the method doesn't exist
     */
    protected function getMutator(string $fqcnClass, string $methodName): \ReflectionMethod
    {
        return new \ReflectionMethod($fqcnClass, $methodName);
    }
}
