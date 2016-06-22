<?php

namespace SlmQueueTest\Worker\Result;

use PHPUnit_Framework_TestCase as TestCase;
use SlmQueue\Worker\Result\ProcessStateResult;

class ProcessStateResultTest extends TestCase
{
    public function testGetsState()
    {
        $state  = 'some state';
        $result = ProcessStateResult::withState($state);

        static::assertEquals($state, $result->getState());
    }
}
