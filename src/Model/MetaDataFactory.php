<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Inwebo\DoctrineEventSourcing\Exception\Mapping\EventSource;
use Inwebo\DoctrineEventSourcing\Exception\Mapping\EventSourcingAggregate;
use Inwebo\DoctrineEventSourcing\Mapping;
use Inwebo\DoctrineEventSourcing\Model\Dto\MetaDataDto;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Model\Interface\StateInterface;

/**
 * This class ensure that a subject's mapping is correctly configured.
 * The Mapping configuration is represented by a collection of MetaDataDto object.
 */
class MetaDataFactory
{
    /**
     * @var \ReflectionClass<HasStatesInterface>
     */
    protected \ReflectionClass $reflection;
    /**
     * @var ArrayCollection<int, MetaDataDto>
     */
    protected ArrayCollection $metaData;
    /**
     * @var string FQCN state class name
     */
    protected string $stateClass;

    /**
     * @throws EventSourcingAggregate\MissingAttributeException    If subject is not annotated with the class attribute : #[Mapping\EventSourcingAggregate]
     * @throws EventSourcingAggregate\UndefinedStateClassException If subject is annotated with #[Mapping\EventSourcingAggregate(stateClass: Foo::class)] but have an unknown stateClass argument
     * @throws EventSource\MissingAttributeException               If subject methods are not annotated with the method attribute #[Mapping\EventSource]
     * @throws EventSource\PropertyArgumentException               If subject property defined in #[Mapping\EventSource] doesn't exist in subject
     * @throws EventSource\MethodArgumentException                 If subject method defined in #[Mapping\EventSource] doesn't exist in subject
     * @throws EventSource\MethodArgumentException                 If state method defined in #[Mapping\EventSource] doesn't exist in state
     */
    public function __construct(protected HasStatesInterface $subject)
    {
        $this->reflection = new \ReflectionClass($this->getSubject());
        $this->hasClassAttribute();
        $this->stateClassExists();
        $this->hasValidMapping();
    }

    public function getSubject(): HasStatesInterface
    {
        return $this->subject;
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
     * @return \ReflectionClass<HasStatesInterface>
     */
    public function getReflection(): \ReflectionClass
    {
        return $this->reflection;
    }

    /**
     * @return ArrayCollection<int, MetaDataDto>
     */
    public function getMetaData(): ArrayCollection
    {
        return $this->metaData;
    }

    /**
     * A subject class MUST be annotated with Mapping\EventSourcingAggregate attribute.
     *
     * @throws EventSourcingAggregate\MissingAttributeException If the attribute is missing
     */
    protected function hasClassAttribute(): void
    {
        if (0 === count($this->getReflection()->getAttributes(Mapping\EventSourcingAggregate::class))) {
            throw new EventSourcingAggregate\MissingAttributeException($this->subject);
        }
    }

    /**
     * Mapping\EventSourcingAggregate attribute's argument stateClass MUST be a defined class.
     *
     * @throws EventSourcingAggregate\UndefinedStateClassException If stateClass argument doesn't exist
     */
    protected function stateClassExists(): void
    {
        $attributes = $this->getReflection()->getAttributes(Mapping\EventSourcingAggregate::class);
        /** @var string $stateClass */
        $stateClass = $attributes[0]->getArguments()[Mapping\EventSourcingAggregate::ARGUMENT_STATE_CLASS];

        if (false === class_exists($stateClass)) {
            throw new EventSourcingAggregate\UndefinedStateClassException($stateClass);
        }

        $this->stateClass = $stateClass;
    }

    /**
     * @throws EventSource\MissingAttributeException If subject methods are not annotated with method attribute #[Mapping\EventSource]
     * @throws EventSource\PropertyArgumentException If subject property defined in #[Mapping\EventSource] doesn't exist in subject
     * @throws EventSource\MethodArgumentException   If subject method defined in #[Mapping\EventSource] doesn't exist in subject
     * @throws EventSource\MethodArgumentException   If state method defined in #[Mapping\EventSource] doesn't exist in state
     */
    protected function hasValidMapping(): void
    {
        $metaData = new ArrayCollection();
        $methods = $this->getReflection()->getMethods();
        foreach ($methods as $method) {
            $attributes = $method->getAttributes(Mapping\EventSource::class);

            foreach ($attributes as $attribute) {
                $methodArgument = $attribute->getArguments()[Mapping\EventSource::ARGUMENT_METHOD_NAME];
                $propertyArgument = $attribute->getArguments()[Mapping\EventSource::ARGUMENT_PROPERTY_NAME];

                /*
                 * A valid mapping :
                 * A subject MUST have a class property defined in EventSource's argument : property
                 * A subject MUST have a method defined in EventSource's argument : method
                 * A State MUST have a method defined in EventSource's argument : method
                 */
                try {
                    $subjectProperty = $this->getReflection()->getProperty($propertyArgument); // @phpstan-ignore argument.type
                } catch (\ReflectionException $e) {
                    throw new EventSource\PropertyArgumentException($this->getSubject(), $method->getName(), $propertyArgument); // @phpstan-ignore argument.type
                }

                try {
                    $stateGetter = new \ReflectionMethod($this->getStateClass(), $methodArgument); // @phpstan-ignore argument.type
                } catch (\ReflectionException $e) {
                    throw new EventSource\MethodArgumentException($this->getStateClass(), $method->getName(), $methodArgument); // @phpstan-ignore argument.type
                }
                try {
                    $subjectGetter = new \ReflectionMethod($this->getSubject(), $methodArgument); // @phpstan-ignore argument.type
                } catch (\ReflectionException $e) {
                    throw new EventSource\MethodArgumentException($this->getSubject()::class, $method->getName(), $methodArgument); // @phpstan-ignore argument.type
                }

                try {
                    $stateSetter = new \ReflectionMethod($this->getStateClass(), $method->getName());
                } catch (\ReflectionException $e) {
                    throw new EventSource\MethodArgumentException($this->getStateClass(), $method->getName(), $methodArgument); // @phpstan-ignore argument.type
                }

                $metaData->add(new MetaDataDto(
                    $method,
                    $subjectGetter,
                    $stateGetter,
                    $stateSetter,
                    $subjectProperty,
                ));
            }
        }

        if (0 === $metaData->count()) {
            throw new EventSource\MissingAttributeException($this->getSubject());
        }

        $this->metaData = $metaData;
    }
}
