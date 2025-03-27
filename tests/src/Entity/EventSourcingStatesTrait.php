<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\src\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait EventSourcingStatesTrait
{
    private ArrayCollection $states;

    /**
     * @return ArrayCollection
     */
    public function getEventSourcingStates(): Collection
    {
        return $this->states;
    }
}
