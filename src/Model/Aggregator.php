<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Model;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Model\Interface\StateInterface;

/**
 * This class use a valid mapping from a subject to manipulate state.
 */
readonly class Aggregator
{
    public function __construct(private MappingFactory $factory)
    {
    }

    public function getMappingFactory(): MappingFactory
    {
        return $this->factory;
    }

    public function applyState(HasStatesInterface $subject, StateInterface $state): HasStatesInterface
    {
        foreach ($this->getMappingFactory()->getMapping() as $mapping) {
            $value = $mapping->invokeStateGetter($state);
            $mapping->invokeSubjectSetter($subject, $value);
        }

        return $subject;
    }

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
     * @param class-string $subject
     */
    public static function new(string $subject): self
    {
        return new self(new MappingFactory($subject));
    }
}
