<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Model\Interface;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * A subject MUST implement this interface or an MissingAttributeException will be thrown.
 */
interface HasStatesInterface
{
    /**
     * @return ArrayCollection<int, StateInterface>
     */
    public function getEventSourcingStates(): Collection;
}
