<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Exception\Mapping\EventSource;

class MethodArgumentException extends \RuntimeException
{
    protected string $invalidMessage = '%s::%s() has an invalid annotation #[EventSource]'."\n\t".
        'Argument method : %s, but  but this method doesn\'t exists %s::%s()'
    ;

    public function __construct(string $fqcn, string $methodName, string $methodArgument, int $code = 0, ?\Exception $previous = null)
    {
        $message = sprintf(
            $this->invalidMessage,
            $fqcn,
            $methodName,
            $methodArgument,
            $fqcn,
            $methodArgument);

        parent::__construct($message, $code, $previous);
    }
}
