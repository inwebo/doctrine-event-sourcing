<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Model\Interface;

use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

interface StoreListenerInterface
{
    public function prePersist(HasStatesInterface $subject, PrePersistEventArgs $args): void;

    public function preUpdate(HasStatesInterface $subject, PreUpdateEventArgs $args): void;
}
