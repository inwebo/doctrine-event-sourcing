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
use Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateSource\Missing\GetterArgumentException;
use Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateSource\Missing\SetterArgumentException;
use Inwebo\DoctrineEventSourcing\Exception\MissingHasStatesInterfaceException;
use Inwebo\DoctrineEventSourcing\Mapping;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Model\Interface\StateInterface;
use Inwebo\DoctrineEventSourcing\Model\Mapping as MappingDto;

/**
 * This class ensure that a subject's mapping is correctly configured.
 * The Mapping configuration is represented by a collection of Mapping objects.
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
     * @param class-string $subjectClass
     *
     * @throws \ErrorException                    If subject class is Unknown
     * @throws AttributeException                 If subject is not annotated with #[AggregateRoot]
     * @throws SetterArgumentException            If #[AggregateRoot] stateClass is missing
     * @throws GetterException                    If a getter is missing in subjectClass or stateClass
     * @throws GetterArgumentException            If #[AggregateSource] getter argument is missing
     * @throws StateClassArgumentException        If #[AggregateRoot] stateClass is missing
     * @throws SubjectSetterException             If #[AggregateRoot] subjectSetter argument is missing
     * @throws StateClassException                If #[AggregateRoot] stateClass argument is invalid
     * @throws Invalid\SubjectSetterException     If #[AggregateRoot] subjectSetter argument is invalid
     * @throws MissingHasStatesInterfaceException If subject class doesn't implement HasStatesInterface
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
     * @param class-string $subjectClass
     *
     * @throws MissingHasStatesInterfaceException|\ErrorException
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

    public function getSubjectSetter(): \ReflectionMethod
    {
        return $this->subjectSetter;
    }

    /**
     * @return class-string
     */
    public function getSubjectClass(): string
    {
        return $this->subjectClass;
    }

    public function getStateClass(): string
    {
        return $this->stateClass;
    }

    public function newStateClass(): StateInterface
    {
        /** @var StateInterface $stateClass */
        $stateClass = new ($this->getStateClass());

        return $stateClass;
    }

    /**
     * @return \ReflectionClass<object>
     */
    public function getSubjectReflectionClass(): \ReflectionClass
    {
        return $this->subjectReflectionClass;
    }

    /**
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
                    throw new GetterException($this->getSubjectClass(), $property->getName(), $getterMethodName, $setterMethodName);
                }
                try {
                    $stateSetterMethod = $this->getMutator($this->getStateClass(), $setterMethodName);
                } catch (\ReflectionException $e) {
                    throw new GetterException($this->getStateClass(), $property->getName(), $getterMethodName, $setterMethodName);
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
     * @throws \ReflectionException
     */
    protected function getMutator(string $fqcnClass, string $methodName): \ReflectionMethod
    {
        return new \ReflectionMethod($fqcnClass, $methodName);
    }
}
