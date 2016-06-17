<?php

namespace SlmQueue\Worker\Result;

final class ProcessStateResult
{
    /**
     * @var string
     */
    private $state;

    /**
     * @param $state
     */
    private function __construct($state)
    {
        $this->state = $state;
    }

    /**
     * @param $state
     * @return ProcessStateResult
     */
    public static function withState($state)
    {
        return new static($state);
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->state;
    }
}
