<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\Mapping;

use Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateSource\Invalid\GetterException;
use Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateSource\Invalid\MutatorException;
use Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateSource\Missing\GetterArgumentException;
use Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateSource\Missing\SetterArgumentException;
use Inwebo\DoctrineEventSourcing\Model\MappingFactory;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid\InvalidGetterArgument;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid\MissingGetterArgument;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid\MissingSetterArgument;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[CoversClass(MappingFactory::class)]
#[Group('Mapping')]
class AggregateSourceTest extends TestCase
{
    public function testMissingGetterArgument(): void
    {
        $this->expectException(GetterArgumentException::class);
        new MappingFactory(MissingGetterArgument::class);
    }

    public function testInvalidGetterArgument(): void
    {
        $this->expectException(GetterException::class);
        new MappingFactory(InvalidGetterArgument::class);
    }

    public function testMissingSetterArgument(): void
    {
        $this->expectException(SetterArgumentException::class);
        new MappingFactory(MissingSetterArgument::class);
    }

    public function testInvalidSetterArgument(): void
    {
        $this->expectException(MutatorException::class);
        new MappingFactory(InvalidGetterArgument::class);
    }
}
