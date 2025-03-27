<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\Store;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Person\Person;
use Inwebo\DoctrineEventSourcing\Tests\src\Listener\PersonStoreListener;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PersonStoreListener::class)]
class ListenerTest extends TestCase
{
    private ?PersonStoreListener $listener = null;

    public function setUp(): void
    {
        $this->listener = new PersonStoreListener();
    }

    public function tearDown(): void
    {
        $this->listener = null;
    }

    public function testPrePersist(): void
    {
        $person = new Person('foo', 'bar');
        $event = self::createStub(LifecycleEventArgs::class);
        $event->method('getObject')->willReturn($person);

        $this->listener->prePersist($person, $event);

        $this->assertEquals(1, $person->getEventSourcingStates()->count());
        $this->assertEquals('foo', $person->getEventSourcingStates()->first()->getFirstName());
        $this->assertEquals('bar', $person->getEventSourcingStates()->first()->getLastName());
    }

    public function testPreUpdate(): void
    {
        $person = new Person('foo', 'bar');

        $preUpdateEvent = self::createMock(PreUpdateEventArgs::class);
        $preUpdateEvent->method('getObject')->willReturn($person);

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

        $this->listener->preUpdate($person, $preUpdateEvent);

        $this->assertEquals(1, $person->getEventSourcingStates()->count());
        $this->assertEquals('oof', $person->getEventSourcingStates()->last()->getFirstName());
        $this->assertEquals('baz', $person->getEventSourcingStates()->last()->getLastName());
    }
}
