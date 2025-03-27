<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Listener;

use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Inwebo\DoctrineEventSourcing\Model\Aggregator;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Model\Interface\StoreListenerInterface;

/**
 * A Doctrine Entity listener.
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/reference/events.html#entity-listeners
 */
abstract class AbstractStoreListener implements StoreListenerInterface
{
    private bool $hasBeenUpdated = false;

    public function prePersist(HasStatesInterface $subject, PrePersistEventArgs $args): void
    {
        if (true === $this->hasBeenUpdated) {
            return;
        }
        $this->hasBeenUpdated = true;
        $aggregator = Aggregator::new(get_class($subject));
        $state = $aggregator->createState($subject);

        $subject->getEventSourcingStates()->add($state);
    }

    public function preUpdate(HasStatesInterface $subject, PreUpdateEventArgs $args): void
    {
        if (true === $this->hasBeenUpdated) {
            return;
        }
        $this->hasBeenUpdated = true;
        $aggregator = Aggregator::new(get_class($subject));
        $state = $aggregator->createStateFromChange($args);

        $subject->getEventSourcingStates()->add($state);
        $args->getObjectManager()->persist($state);
        $args->getObjectManager()->flush();
    }
}
