<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid;

use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\EventSourcingStatesTrait;

/**
 * Test missing class attribute.
 */
class MissingClassAttribute implements HasStatesInterface
{
    use EventSourcingStatesTrait;
}
