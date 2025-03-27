<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Exception;

use Inwebo\DoctrineEventSourcing\Model\MappingFactory;

class MissingHasStatesInterfaceException extends \Exception
{
    public function __construct(string $subjectClass)
    {
        $message = sprintf('%s class MUST implements %s interface.', $subjectClass, MappingFactory::HAS_STATES_INTERFACE);

        parent::__construct($message);
    }
}
