<?php

namespace SlmQueueTest\Worker\Result;

use PHPUnit\Framework\TestCase;
use SlmQueue\Worker\Result\ProcessStateResult;

class ProcessStateResultTest extends TestCase
{
    public function testGetsState(): void
    {
        $state = 'some state';
        $result = ProcessStateResult::withState($state);

        static::assertEquals($state, $result->getState());
    }
}
