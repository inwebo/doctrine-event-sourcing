<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Inwebo\DoctrineEventSourcing\Model\Dto\MetaDataDto;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Model\Interface\StateInterface;

readonly class EventSourcing
{
    public function __construct(private MetaDataFactory $metaDataFactory)
    {
    }

    public function getMetaDataFactory(): MetaDataFactory
    {
        return $this->metaDataFactory;
    }

    /**
     * Apply State's values to a subject.
     *
     * @throws \ReflectionException
     */
    public function applyState(HasStatesInterface $subject, StateInterface $state): HasStatesInterface
    {
        foreach ($this->getMetaDataFactory()->getMetadata() as $metaData) {
            $value = $metaData->invokeStateGetter($state);
            $metaData->invokeSubjectSetter($subject, $value);
        }

        return $subject;
    }

    /**
     * Create a new state from a subject's mapping current values.
     */
    public function createState(HasStatesInterface $subject): StateInterface
    {
        $state = $this->getMetaDataFactory()->newStateClass();

        foreach ($this->getMetaDataFactory()->getMetadata() as $metaData) {
            $value = $metaData->invokeSubjectGetter($subject);
            $metaData->invokeStateSetter($state, $value);
        }

        $state->setSubject($subject);

        return $state;
    }

    /**
     * Create a new state value from the doctrine pre update hook with its PreUpdateEventArgs argument changeSet values.
     */
    public function createFromChange(PreUpdateEventArgs $args): StateInterface
    {
        /** @var HasStatesInterface $subject */
        $subject = $args->getObject();
        $state = $this->createState($subject);
        $changeSet = $args->getEntityChangeSet();
        foreach ($changeSet as $fieldName => $value) {
            $criteria = (new Criteria())->where(new Comparison('subjectProperty.name', '=', $fieldName));

            /** @var ArrayCollection<int, MetaDataDto> $matches */
            $matches = $this->getMetaDataFactory()->getMetaData()->matching($criteria);

            if ($matches->count() > 0) {
                if (false !== $matches->first()) {
                    $matches->first()->invokeStateSetter($state, $value[1]);
                }
            }
        }

        return $state;
    }

    public static function new(HasStatesInterface $subject): self
    {
        return new self(new MetaDataFactory($subject));
    }
}
