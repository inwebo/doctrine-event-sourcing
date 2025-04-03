<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid;

use Inwebo\DoctrineEventSourcing\Model\Interface\StateInterface;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\SubjectTrait;

class InvalidSubjectState implements StateInterface
{
    use SubjectTrait;

    public function getName(): string
    {
        return '';
    }
}
