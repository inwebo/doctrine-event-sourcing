<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\src\Listener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Inwebo\DoctrineEventSourcing\Model\EventSourcing;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Model\MetaDataFactory;

class PersonStoreListener
{
    public function prePersist(HasStatesInterface $subject, LifecycleEventArgs $args): void
    {
        $factory = new EventSourcing(new MetaDataFactory($subject));
        $state = $factory->createState($subject);
        $subject->getEventSourcingStates()->add($state);
    }

    public function preUpdate(HasStatesInterface $subject, PreUpdateEventArgs $args): void
    {
        $factory = new EventSourcing(new MetaDataFactory($subject));
        $state = $factory->createFromChange($args);
        $subject->getEventSourcingStates()->add($state);
    }
}
