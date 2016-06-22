<?php

namespace SlmQueue\Worker\Result;

final class ExitWorkerLoopResult
{
    /**
     * @var string
     */
    private $reason;

    /**
     * @param $reason
     */
    private function __construct($reason)
    {
        $this->reason = $reason;
    }

    /**
     * @param $reason
     * @return ExitWorkerLoopResult
     */
    public static function withReason($reason)
    {
        return new static($reason);
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->reason;
    }
}
