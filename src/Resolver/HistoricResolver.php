<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Resolver;

use Inwebo\DoctrineEventSourcing\Model\Dto\ChangeDto;
use Inwebo\DoctrineEventSourcing\Model\Dto\ChangeSetDto;
use Inwebo\DoctrineEventSourcing\Model\EventSourcing;
use Inwebo\DoctrineEventSourcing\Model\Interface\StateInterface;

class HistoricResolver
{
    public function __construct(protected EventSourcing $eventSourcing)
    {
    }

    /**
     * @return array<string, ChangeDto>
     *
     * @throws \ReflectionException
     */
    public function getDiff(StateInterface $current, ?StateInterface $previous = null): array
    {
        $changes = [];

        foreach ($this->eventSourcing->getMetaDataFactory()->getMetaData() as $metaData) {
            $currentValue = $metaData->invokeStateGetter($current);
            if (is_null($previous)) {
                $previousValue = null;
            } else {
                $previousValue = $metaData->invokeStateGetter($previous);
            }

            if ($currentValue !== $previousValue) {
                $changes[$metaData->getSubjectProperty()->getName()] = new ChangeDto($metaData->getSubjectProperty()->getName(), $currentValue, $previousValue);
            }
        }

        return $changes;
    }

    public function resolve(): ChangeSetDto
    {
        $changeSetDto = new ChangeSetDto();
        $states = $this->eventSourcing->getMetaDataFactory()->getSubject()->getEventSourcingStates();

        $previous = null;
        foreach ($states as $state) {
            if (null !== $previous) {
                $changeSetDto->addChange($this->getDiff($state, $previous));
            } else {
                $changeSetDto->addChange($this->getDiff($state));
            }
            $previous = $state;
        }

        $actualState = $this->eventSourcing->createState($this->eventSourcing->getMetaDataFactory()->getSubject());
        $changeSetDto->addChange($this->getDiff($actualState, $previous));

        return $changeSetDto;
    }
}
