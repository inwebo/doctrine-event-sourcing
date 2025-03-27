<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Model\Dto;

use Doctrine\Common\Collections\ArrayCollection;

class ChangeSetDto
{
    public function __construct(
        /** @var ArrayCollection<int, ChangeDto[]> */
        protected ArrayCollection $changes = new ArrayCollection(),
    ) {
    }

    /**
     * @return ArrayCollection<int, ChangeDto[]>
     */
    public function get(): ArrayCollection
    {
        return $this->changes;
    }

    /**
     * @param ChangeDto[] $changes
     */
    public function addChange(array $changes): void
    {
        $this->changes->add($changes);
    }
}
