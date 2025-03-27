<?php

declare(strict_types=1);

namespace Inwebo\DoctrineEventSourcing\Model\Dto;

/**
 * Represents a change in a field between two states.
 */
readonly class ChangeDto
{
    public function __construct(
        /**
         * Which field name has changed.
         */
        private string $fieldName,
        private mixed $newValue,
        private mixed $oldValue)
    {
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getOldValue(): mixed
    {
        return $this->oldValue;
    }

    public function getNewValue(): mixed
    {
        return $this->newValue;
    }
}
