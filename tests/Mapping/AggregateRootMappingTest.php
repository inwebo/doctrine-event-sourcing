<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\Mapping;

use Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateRoot\Invalid;
use Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateRoot\Missing;
use Inwebo\DoctrineEventSourcing\Exception\MissingHasStatesInterfaceException;
use Inwebo\DoctrineEventSourcing\Model\MappingFactory;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Foo\Foo;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid\InvalidStateClass;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid\InvalidSubjectSetter;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid\MissingAggregateRootStateClassArgument;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid\MissingAggregateRootSubjectSetterArgument;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid\MissingClassAttribute;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid\MissingHasStatesInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[CoversClass(MappingFactory::class)]
#[Group('Mapping')]
class AggregateRootMappingTest extends TestCase
{
    public function testUnknownSubjectClass(): void
    {
        $this->expectException(\Exception::class);
        new MappingFactory(Baz::class);
    }

    public function testMissingHasStatesInterface(): void
    {
        $this->expectException(MissingHasStatesInterfaceException::class);
        new MappingFactory(MissingHasStatesInterface::class);
    }

    public function testMissingAggregateRootAttribute(): void
    {
        $this->expectException(Missing\AttributeException::class);
        new MappingFactory(MissingClassAttribute::class);
    }

    public function testMissingStateClassArgument(): void
    {
        $this->expectException(Missing\StateClassArgumentException::class);
        new MappingFactory(MissingAggregateRootStateClassArgument::class);
    }

    public function testInvalidStateClassArgument(): void
    {
        $this->expectException(Invalid\StateClassException::class);
        new MappingFactory(InvalidStateClass::class);
    }

    public function testMissingSubjectSetterArgument(): void
    {
        $this->expectException(Missing\SubjectSetterException::class);
        new MappingFactory(MissingAggregateRootSubjectSetterArgument::class);
    }

    public function testInvalidSubjectSetterArgument(): void
    {
        $this->expectException(Invalid\SubjectSetterException::class);
        new MappingFactory(InvalidSubjectSetter::class);
    }

    public function testValidMapping(): void
    {
        $mappingFactory = new MappingFactory(Foo::class);

        $this->assertEquals('getFirstName', $mappingFactory->getMapping()->first()->getSubjectGetter()->getName());
        $this->assertEquals('setFirstName', $mappingFactory->getMapping()->first()->getSubjectSetter()->getName());
        $this->assertEquals('getFirstName', $mappingFactory->getMapping()->first()->getStateGetter()->getName());
        $this->assertEquals('setFirstName', $mappingFactory->getMapping()->first()->getStateSetter()->getName());
    }
}
