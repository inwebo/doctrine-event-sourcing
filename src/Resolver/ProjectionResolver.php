<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Resolver;

use Doctrine\Common\Collections\Criteria;
use Inwebo\DoctrineEventSourcing\Model\EventSourcing;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Model\Interface\StateInterface;

class ProjectionResolver
{
    public function __construct(protected EventSourcing $factory)
    {
    }

    public function resolve(HasStatesInterface $subject, StateInterface $state): HasStatesInterface
    {
        return $this->factory->applyState($subject, $state);
    }

    public function matching(Criteria $criteria): HasStatesInterface
    {
        $matches = $this->factory->getMetaDataFactory()->getSubject()->getEventSourcingStates()->matching($criteria);

        foreach ($matches as $state) {
            $this->factory->applyState($this->factory->getMetaDataFactory()->getSubject(), $state);
        }

        return $this->factory->getMetaDataFactory()->getSubject();
    }
}
