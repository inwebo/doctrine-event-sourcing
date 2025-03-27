<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\Mapping;

use Inwebo\DoctrineEventSourcing\Exception\Mapping\EventSource;
use Inwebo\DoctrineEventSourcing\Exception\Mapping\EventSourcingAggregate;
use Inwebo\DoctrineEventSourcing\Exception\Mapping\EventSourcingAggregate\UndefinedStateClassException;
use Inwebo\DoctrineEventSourcing\Model\MetaDataFactory;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid\InvalidStateClass;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid\InvalidStateMethod;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid\InvalidSubjectMethod;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid\InvalidSubjectPropertyArgument;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid\MissingClassAttribute;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Invalid\MissingEventSourceAttribute;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Person\Person;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MetaDataFactory::class)]
class MetaDataFactoryTest extends TestCase
{
    public function testInvalidClassAttributeMapping(): void
    {
        $subject = new MissingClassAttribute();
        $this->expectException(EventSourcingAggregate\MissingAttributeException::class);
        new MetaDataFactory($subject);
    }

    public function testValidClassAttributeMapping(): void
    {
        $subject = new Person('foo', 'bar');
        $factory = new MetaDataFactory($subject);
        $reflection = $factory->getReflection();
        $this->assertInstanceOf(\ReflectionClass::class, $reflection);
    }

    public function testInvalidMethodsMapping(): void
    {
        $subject = new InvalidStateClass();
        $this->expectException(UndefinedStateClassException::class);
        new MetaDataFactory($subject);
    }

    public function testValidEventSourceAttributeMapping(): void
    {
        $subject = new Person('foo', 'bar');
        $metaDataFactory = new MetaDataFactory($subject);
        $attributes = $metaDataFactory->getMetaData();

        $this->assertCount(2, $attributes);
    }

    public function testInValidEventSourceAttributeMapping(): void
    {
        $subject = new MissingEventSourceAttribute();
        $this->expectException(EventSource\MissingAttributeException::class);
        new MetaDataFactory($subject);
    }

    public function testInvalidStateMethod(): void
    {
        $subject = new InvalidStateMethod();
        $this->expectException(EventSource\MethodArgumentException::class);
        new MetaDataFactory($subject);
    }

    public function testInvalidSubjectMethod(): void
    {
        $subject = new InvalidSubjectMethod();
        $this->expectException(EventSource\MethodArgumentException::class);
        new MetaDataFactory($subject);
    }

    public function testInvalidSubjectPropertyArgument(): void
    {
        $subject = new InvalidSubjectPropertyArgument();
        $this->expectException(EventSource\PropertyArgumentException::class);
        new MetaDataFactory($subject);
    }
}
