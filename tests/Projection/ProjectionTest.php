<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Tests\Projection;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Order;
use Inwebo\DoctrineEventSourcing\Model\EventSourcing;
use Inwebo\DoctrineEventSourcing\Model\MetaDataFactory;
use Inwebo\DoctrineEventSourcing\Resolver\ProjectionResolver;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Product\Product;
use Inwebo\DoctrineEventSourcing\Tests\src\Entity\Product\ProductState;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

#[CoversClass(ProjectionResolver::class)]
class ProjectionTest extends TestCase
{
    private ?ProjectionResolver $projection;
    private ?\DateTimeImmutable $today;

    public function setUp(): void
    {
        // Actual subject clock
        $this->today = $today = \DateTimeImmutable::createFromFormat('Y-m-d', '2025-01-01');
        // Actual State
        $subject = new Product('Foo', 100.00, \DateTime::createFromImmutable($today));
        $states = [
            // Created at
            new ProductState('Bar', 150.00, \DateTime::createFromImmutable($today)->modify('-1 year')),
            // Past edit and ended discount
            new ProductState('Discounted', 90.00, \DateTime::createFromImmutable($today)->modify('-1 mont'), \DateTime::createFromImmutable($today)->modify('-6 month +7 days'), \DateTime::createFromImmutable($today)->modify('-6 month +15 days')),
            // Past edit and futur discount
            new ProductState('Baz Will be discounted', 75.00, \DateTime::createFromImmutable($today)->modify('-2 days'), \DateTime::createFromImmutable($today)->modify('+7 days'), \DateTime::createFromImmutable($today)->modify('+15 days')),
        ];
        $subject->setEventSourceStates($states);

        $this->projection = new ProjectionResolver(EventSourcing::new($subject));
    }

    public function tearDown(): void
    {
        $this->projection = null;
        $this->today = null;
    }

    public function testFindFuturDiscount(): void
    {
        // Trouver le plus lointain dans le futur jusqu'à.
        $criteria = new Criteria();
        $criteria->orderBy(['discountEnd' => Order::Ascending]);
        /** @var Product $subject */
        $subject = $this->projection->matching($criteria);
        $this->assertEquals('Foo summer last discount', $subject->getName());
    }
}
