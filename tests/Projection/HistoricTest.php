<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\Projection;

use Inwebo\DoctrineEventSourcing\Model\EventSourcing;
use Inwebo\DoctrineEventSourcing\Model\MetaDataFactory;
use Inwebo\DoctrineEventSourcing\Resolver\HistoricResolver;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Person\Person;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Person\PersonState;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(HistoricResolver::class)]
class HistoricTest extends TestCase
{
    private HistoricResolver $historic;

    public function setUp(): void
    {
        // Actual State
        $subject = new Person('Georges', 'Pompidou');
        $states = [
            // Created
            new PersonState('Albert', 'Lebrun'),
            new PersonState('Vincent', 'Auriol'),
            new PersonState('René', 'Cotty'),
            new PersonState('Charles', 'De Gaulle'),
        ];
        $subject->setEventSourcingStates($states);

        $this->historic = new HistoricResolver(EventSourcing::new($subject));
    }

    public function testHistoric(): void
    {
        $changeSet = $this->historic->resolve();

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
