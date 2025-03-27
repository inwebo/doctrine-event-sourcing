<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\Mapping;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Inwebo\DoctrineEventSourcing\Model\EventSourcing;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Model\MetaDataFactory;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Person\Person;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Person\PersonState;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EventSourcing::class)]
class EventSourcingTest extends TestCase
{
    public function getFactory(HasStatesInterface $subject): EventSourcing
    {
        return new EventSourcing(new MetaDataFactory($subject));
    }

    public function testCreateFromEventSourceAggregate(): void
    {
        $person = new Person('foo', 'bar');
        $state = $this->getFactory($person)->createState($person);

        $this->assertInstanceOf(PersonState::class, $state);
        $this->assertEquals($person->getFirstName(), $state->getFirstName());
        $this->assertEquals($person->getLastName(), $state->getLastName());
    }

    public function testCreateFromEventArgs(): void
    {
        $person = new Person('foo', 'bar');
        $factory = $this->getFactory($person);
        $preUpdateEvent = self::createMock(PreUpdateEventArgs::class);
        $preUpdateEvent->method('getEntityChangeSet')->willReturn([
            'firstName' => [
                'foo',
                'oof',
            ],
            'lastName' => [
                'bar',
                'baz',
            ],
        ]);

        $preUpdateEvent->method('getObject')->willReturn($person);

        $state = $factory->createFromChange($preUpdateEvent);

        $this->assertInstanceOf(PersonState::class, $state);
        $this->assertEquals('oof', $state->getFirstName());
        $this->assertEquals('baz', $state->getLastName());
    }
}
