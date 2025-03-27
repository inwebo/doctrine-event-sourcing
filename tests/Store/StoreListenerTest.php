<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\Store;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Inwebo\DoctrineEventSourcing\Listener\StoreListener;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Foo\Foo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[CoversClass(StoreListener::class)]
#[Group('StoreListener')]
class StoreListenerTest extends TestCase
{
    private ?StoreListener $listener;
    private ?Foo $subject;

    public function setUp(): void
    {
        $this->listener = new StoreListener();
        $this->subject = new Foo('foo', 'bar', new \DateTime());
    }

    public function tearDown(): void
    {
        $this->listener = null;
        $this->subject = null;
    }

    public function testPrePersist(): void
    {
        $subject = new Foo('foo', 'bar', new \DateTime());
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $event = new PrePersistEventArgs($subject, $entityManager);
        $this->listener->prePersist($subject, $event);

        $this->assertEquals(1, $subject->getEventSourcingStates()->count());
        $this->assertEquals('foo', $subject->getEventSourcingStates()->first()->getFirstName());
        $this->assertEquals('bar', $subject->getEventSourcingStates()->first()->getLastName());
    }

    public function testPreUpdate(): void
    {
        $subject = new Foo('foo', 'bar', new \DateTime());
        $preUpdateEvent = self::createStub(PreUpdateEventArgs::class);
        $preUpdateEvent->method('getObject')->willReturn($subject);

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

        $this->listener->preUpdate($subject, $preUpdateEvent);

        $this->assertEquals(1, $subject->getEventSourcingStates()->count());
        $this->assertEquals('oof', $subject->getEventSourcingStates()->last()->getFirstName());
        $this->assertEquals('baz', $subject->getEventSourcingStates()->last()->getLastName());
    }
}
