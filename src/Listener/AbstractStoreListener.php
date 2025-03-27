<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Listener;

use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Inwebo\DoctrineEventSourcing\Model\EventSourcing;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Model\Interface\StoreListenerInterface;

/**
 * A Doctrine Entity listener.
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/reference/events.html#entity-listeners
 */
abstract class AbstractStoreListener implements StoreListenerInterface
{
    private static bool $hasBeenUpdated = false;

    public static function hasBeenUpdated(): bool
    {
        return self::$hasBeenUpdated;
    }

    public function setUpdated(bool $updated): void
    {
        self::$hasBeenUpdated = $updated;
    }

    public function prePersist(HasStatesInterface $subject, PrePersistEventArgs $args): void
    {
        $factory = EventSourcing::new($subject);
        $state = $factory->createState($subject);
        $subject->getEventSourcingStates()->add($state);

        $args->getObjectManager()->persist($state);
        $args->getObjectManager()->flush();
    }

    public function preUpdate(HasStatesInterface $subject, PreUpdateEventArgs $args): void
    {
        if (true === static::hasBeenUpdated()) {
            return;
        }

        $factory = EventSourcing::new($subject);
        $state = $factory->createFromChange($args);
        $subject->getEventSourcingStates()->add($state);

        $args->getObjectManager()->persist($state);
        $args->getObjectManager()->flush();

        $this->setUpdated(true);
    }
}
