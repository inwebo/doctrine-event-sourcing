<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid;

use Inwebo\DoctrineEventSourcing\Model\Interface\StateInterface;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\SubjectTrait;

class InvalidSubjectPropertyArgumentState implements StateInterface
{
    use SubjectTrait;

    public function getFoo(): void
    {
    }
}
