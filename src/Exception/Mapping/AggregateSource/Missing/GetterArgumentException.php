<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Exception\Mapping\AggregateSource\Missing;

use Inwebo\DoctrineEventSourcing\Mapping\AggregateSource;

class GetterArgumentException extends \Exception
{
    public function __construct(string $stateClass, string $property)
    {
        $message = sprintf('%s::%s is annotated with #[AggregateSource], but `%s:` is missing, try to add it', $stateClass, $property, AggregateSource::GETTER);
        parent::__construct($message);
    }
}
