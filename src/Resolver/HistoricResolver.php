<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Resolver;

use Inwebo\DoctrineEventSourcing\Model\Aggregator;
use Inwebo\DoctrineEventSourcing\Model\Dto\ChangeDto;
use Inwebo\DoctrineEventSourcing\Model\Dto\ChangeSetDto;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Model\Interface\StateInterface;

class HistoricResolver
{
    public function __construct(protected Aggregator $aggregator)
    {
    }

    /**
     * @return array<ChangeDto>
     */
    protected function getDiff(StateInterface $current, ?StateInterface $previous = null): array
    {
        $changes = [];

        foreach ($this->aggregator->getMappingFactory()->getMapping() as $mapping) {
            $currentValue = $mapping->invokeStateGetter($current);
            if (is_null($previous)) {
                $previousValue = null;
            } else {
                $previousValue = $mapping->invokeStateGetter($previous);
            }

            if ($currentValue !== $previousValue) {
                $changes[$mapping->getProperty()->getName()] = new ChangeDto($mapping->getProperty()->getName(), $currentValue, $previousValue);
            }
        }

        return $changes;
    }

    public function resolve(HasStatesInterface $subject): ChangeSetDto
    {
        $changeSetDto = new ChangeSetDto();
        $states = $subject->getEventSourcingStates();

        $previous = null;
        foreach ($states as $state) {
            if (null !== $previous) {
                $changeSetDto->addChange($this->getDiff($state, $previous));
            } else {
                $changeSetDto->addChange($this->getDiff($state));
            }
            $previous = $state;
        }

        $actualState = $this->aggregator->createState($subject);
        $changeSetDto->addChange($this->getDiff($actualState, $previous));

        return $changeSetDto;
    }
}
