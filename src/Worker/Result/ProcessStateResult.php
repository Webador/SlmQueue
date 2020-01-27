<?php

namespace SlmQueue\Worker\Result;

final class ProcessStateResult
{
    /**
     * @var string
     */
    private $state;

    private function __construct(string $state)
    {
        $this->state = $state;
    }

    public static function withState(string $state): ProcessStateResult
    {
        return new static($state);
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function __toString(): string
    {
        return (string) $this->state;
    }
}
