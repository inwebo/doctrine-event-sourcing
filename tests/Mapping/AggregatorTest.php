<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\Mapping;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Inwebo\DoctrineEventSourcing\Model\Aggregator;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Foo\Foo;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Foo\FooState;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[CoversClass(Aggregator::class)]
#[Group('Aggregator')]
class AggregatorTest extends TestCase
{
    private ?Foo $subject;
    private ?Aggregator $aggregator;

    public function setUp(): void
    {
        $this->aggregator = Aggregator::new(Foo::class);
        $this->subject = new Foo('foo', 'bar', new \DateTime());
    }

    public function tearDown(): void
    {
        $this->subject = null;
        $this->aggregator = null;
    }

    public function testCreateState(): void
    {
        $state = $this->aggregator->createState($this->subject);

        $this->assertInstanceOf(FooState::class, $state);
        $this->assertEquals($this->subject->getFirstName(), $state->getFirstName());
        $this->assertEquals($this->subject->getLastName(), $state->getLastName());
        $this->assertEquals($this->subject->getBirthDate(), $state->getBirthDate());
    }

    public function testCreateStateFromChange(): void
    {
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

        $preUpdateEvent->method('getObject')->willReturn($this->subject);

        $state = $this->aggregator->createStateFromChange($preUpdateEvent);

        $this->assertInstanceOf(FooState::class, $state);
        $this->assertEquals('oof', $state->getFirstName());
        $this->assertEquals('baz', $state->getLastName());
    }

    public function testApplyStates(): void
    {
        $birthDate = new \DateTime();

        $state1 = new FooState();
        $state1->setFirstName('oof');
        $state1->setLastName('rab');
        $state1->setBirthDate($birthDate);

        $state2 = new FooState();
        $state2->setFirstName('Jacques');
        $state2->setLastName('Chirac');
        $state2->setBirthDate($birthDate);

        /** @var Foo $appliedStates */
        $appliedStates = $this->aggregator->applyState($this->subject, $state1);
        $this->assertEquals($state1->getFirstName(), $appliedStates->getFirstName());
        $this->assertEquals($state1->getLastName(), $appliedStates->getLastName());

        /** @var Foo $appliedStates */
        $appliedStates = $this->aggregator->applyState($this->subject, $state2);
        $this->assertEquals($state2->getFirstName(), $appliedStates->getFirstName());
        $this->assertEquals($state2->getLastName(), $appliedStates->getLastName());
    }

    public function testHistoric(): void
    {
        $birthDate = new \DateTime(); /** php-stan */
        $state1 = new FooState();
        $state1->setFirstName('oof');
        $state1->setLastName('rab');
        $state1->setBirthDate($birthDate);
        $this->subject->getEventSourcingStates()->add($state1);
        // oof, bar

        $state2 = new FooState();
        $state2->setFirstName(null);
        $state2->setLastName('Chirac');
        $state2->setBirthDate($birthDate);
        $this->subject->getEventSourcingStates()->add($state2);
        // oof, Chirac

        $state2 = new FooState();
        $state2->setFirstName('foo');
        $state2->setLastName('bar');
        $state2->setBirthDate($birthDate);
        $this->subject->getEventSourcingStates()->add($state2);
        // foo, bar

        $historic = $this->aggregator->historic($this->subject);

        $this->assertCount(3, $historic);

        $this->assertEquals('oof', $historic[0]->getFirstName());
        $this->assertEquals('rab', $historic[0]->getLastName());
        $this->assertCount(0, $historic[0]->getEventSourcingStates());

        $this->assertEquals('oof', $historic[1]->getFirstName());
        $this->assertEquals('Chirac', $historic[1]->getLastName());
        $this->assertCount(1, $historic[1]->getEventSourcingStates());

        $this->assertEquals('foo', $historic[2]->getFirstName());
        $this->assertEquals('bar', $historic[2]->getLastName());
        $this->assertCount(2, $historic[2]->getEventSourcingStates());
    }
}
