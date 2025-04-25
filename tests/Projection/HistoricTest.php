<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\Projection;

use Inwebo\DoctrineEventSourcing\Model\Aggregator;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Resolver\DiffResolver;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Foo\Foo;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Foo\FooState;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DiffResolver::class)]
class HistoricTest extends TestCase
{
    private DiffResolver $historic;
    private HasStatesInterface $subject;

    public function setUp(): void
    {
        // Actual State
        $subject = new Foo('Georges', 'Pompidou', new \DateTime());

        $subject->getEventSourcingStates()->add(new FooState('Albert', 'Lebrun'));
        $subject->getEventSourcingStates()->add(new FooState('Vincent', 'Auriol'));
        $subject->getEventSourcingStates()->add(new FooState('René', 'Cotty'));
        $subject->getEventSourcingStates()->add(new FooState('Charles', 'De Gaulle'));
        $this->subject = $subject;

        $this->historic = new DiffResolver(Aggregator::new(get_class($subject)));
    }

    public function testHistoric(): void
    {
        $changeSet = $this->historic->resolve($this->subject);

        $this->assertNotEmpty($changeSet);
        $this->assertCount(5, $changeSet->get());

        $this->assertEquals(null, $changeSet->get()->offsetGet(0)['firstName']->getOldValue());
        $this->assertEquals(null, $changeSet->get()->offsetGet(0)['lastName']->getOldValue());
        $this->assertEquals('Albert', $changeSet->get()->offsetGet(0)['firstName']->getNewValue());
        $this->assertEquals('Lebrun', $changeSet->get()->offsetGet(0)['lastName']->getNewValue());
        $this->assertEquals('firstName', $changeSet->get()->offsetGet(0)['firstName']->getFieldName());
        $this->assertEquals('lastName', $changeSet->get()->offsetGet(0)['lastName']->getFieldName());

        $this->assertEquals('Charles', $changeSet->get()->offsetGet(4)['firstName']->getOldValue());
        $this->assertEquals('De Gaulle', $changeSet->get()->offsetGet(4)['lastName']->getOldValue());
        $this->assertEquals('Georges', $changeSet->get()->offsetGet(4)['firstName']->getNewValue());
        $this->assertEquals('Pompidou', $changeSet->get()->offsetGet(4)['lastName']->getNewValue());
        $this->assertEquals('firstName', $changeSet->get()->offsetGet(4)['firstName']->getFieldName());
        $this->assertEquals('lastName', $changeSet->get()->offsetGet(4)['lastName']->getFieldName());
    }
}
