<?php

namespace SlmQueue\Worker\Result;

final class ExitWorkerLoopResult
{
    /**
     * @var string
     */
    private $reason;

    private function __construct(string $reason)
    {
        $this->reason = $reason;
    }

    public static function withReason(string $reason): ExitWorkerLoopResult
    {
        return new static($reason);
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function __toString(): string
    {
        return (string) $this->reason;
    }
}
