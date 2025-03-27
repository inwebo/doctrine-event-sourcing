<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid;

use Inwebo\DoctrineEventSourcing\Model\Interface\StateInterface;

class InvalidSubjectState implements StateInterface
{
    public function getName(): string
    {
        return '';
    }
}
