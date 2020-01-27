<?php

namespace SlmQueueTest\Listener\Strategy;

use PHPUnit_Framework_TestCase;
use SlmQueue\Strategy\FileWatchStrategy;
use SlmQueue\Worker\Event\WorkerEventInterface;
use SlmQueue\Worker\Event\ProcessIdleEvent;
use SlmQueue\Worker\Event\ProcessJobEvent;
use SlmQueue\Worker\Result\ExitWorkerLoopResult;
use SlmQueueTest\Asset\SimpleJob;
use SlmQueueTest\Asset\SimpleWorker;

class FileWatchStrategyTest extends PHPUnit_Framework_TestCase
{
    protected $queue;
    protected $worker;
    /** @var FileWatchStrategy */
    protected $listener;

    public function setUp()
    {
        $this->queue    = $this->getMock(\SlmQueue\Queue\QueueInterface::class);
        $this->worker   = new SimpleWorker();
        $this->listener = new FileWatchStrategy();
    }

    public function testListenerInstanceOfAbstractStrategy()
    {
        static::assertInstanceOf(\SlmQueue\Strategy\AbstractStrategy::class, $this->listener);
    }

    public function testListensToCorrectEventAtCorrectPriority()
    {
        $evm = $this->getMock(\Laminas\EventManager\EventManagerInterface::class);
        $priority = 1;

        $evm->expects($this->at(0))->method('attach')
            ->with(WorkerEventInterface::EVENT_PROCESS_IDLE, [$this->listener, 'onStopConditionCheck'], $priority);
        $evm->expects($this->at(1))->method('attach')
            ->with(WorkerEventInterface::EVENT_PROCESS_QUEUE, [$this->listener, 'onStopConditionCheck'], 1000);
        $evm->expects($this->at(2))->method('attach')
            ->with(WorkerEventInterface::EVENT_PROCESS_STATE, [$this->listener, 'onReportQueueState'], $priority);

        $this->listener->attach($evm, $priority);
    }

    public function testPatternDefault()
    {
        // standard zf2 application php and phtml files
        static::assertEquals('/^\.\/(config|module).*\.(php|phtml)$/', $this->listener->getPattern());
    }

    public function testFilesGetterReturnEmptyArrayByDefault()
    {
        // standard zf2 application php and phtml files
        static::assertEmpty($this->listener->getFiles());
    }

    public function testSettingAPatternWillResetFilesToEmpty()
    {
        $this->listener->setPattern('/^anything$/');
        static::assertEmpty($this->listener->getFiles());
    }

    public function testSettingPatternNullifiesCurrentListOfFilesToWatch()
    {
        // builds a file list
        $this->listener->onStopConditionCheck(new ProcessIdleEvent($this->worker, $this->queue));
        static::assertNotEmpty($this->listener->getFiles());

        $this->listener->setPattern('/^$/');

        static::assertEquals('/^$/', $this->listener->getPattern());
        static::assertCount(0, $this->listener->getFiles());
    }

    public function testCanFileFilesByPattern()
    {
        // builds a file list
        if (!is_dir('tests/build')) {
            mkdir('tests/build', 0755, true);
        }
        file_put_contents('tests/build/filewatch.txt', 'hi');

        $this->listener->setPattern('/^\.\/(tests\/build).*\.(txt)$/');
        $this->listener->onStopConditionCheck(new ProcessIdleEvent($this->worker, $this->queue));

        static::assertCount(1, $this->listener->getFiles());
    }

    public function testWatchedFileChangeStopsPropagation()
    {
        // builds a file list
        if (!is_dir('tests/build')) {
            mkdir('tests/build', 0755, true);
        }
        file_put_contents('tests/build/filewatch.txt', 'hi');

        $this->listener->setPattern('/^\.\/(tests\/build).*\.(txt)$/');
        $result = $this->listener->onStopConditionCheck(new ProcessJobEvent(new SimpleJob(), $this->worker,
            $this->queue));
        static::assertNull($result);

        static::assertCount(1, $this->listener->getFiles());

        // change the file
        file_put_contents('tests/build/filewatch.txt', 'hello');

        $result = $this->listener->onStopConditionCheck(new ProcessJobEvent(new SimpleJob(), $this->worker,
            $this->queue));
        static::assertNotNull($result);
        static::assertInstanceOf(ExitWorkerLoopResult::class, $result);
        static::assertContains('file modification detected for', $result->getReason());
    }

    public function testWatchedFileRemovedStopsPropagation()
    {
        // builds a file list
        if (!is_dir('tests/build')) {
            mkdir('tests/build', 0755, true);
        }
        file_put_contents('tests/build/filewatch.txt', 'hi');

        $this->listener->setPattern('/^\.\/(tests\/build).*\.(txt)$/');
        $result = $this->listener->onStopConditionCheck(new ProcessJobEvent(new SimpleJob(), $this->worker,
            $this->queue));
        static::assertNull($result);

        static::assertCount(1, $this->listener->getFiles());

        // remove the file
        unlink('tests/build/filewatch.txt');

        $result = $this->listener->onStopConditionCheck(new ProcessJobEvent(new SimpleJob(), $this->worker,
            $this->queue));
        static::assertNotNull($result);
        static::assertInstanceOf(ExitWorkerLoopResult::class, $result);
        static::assertContains('file modification detected for', $result->getReason());
    }

    public function testStopConditionCheckIdlingThrottling()
    {
        // builds a file list
        if (!is_dir('tests/build')) {
            mkdir('tests/build', 0755, true);
        }
        file_put_contents('tests/build/filewatch.txt', 'hi');

        $this->listener->setPattern('/^\.\/(tests\/build).*\.(txt)$/');
        $this->listener->setIdleThrottleTime(1);

        // records last time based when idle event
        $this->listener->onStopConditionCheck(new ProcessIdleEvent($this->worker, $this->queue));

        // file has changed
        file_put_contents('tests/build/filewatch.txt', 'hello');

        $result = $this->listener->onStopConditionCheck(new ProcessIdleEvent($this->worker, $this->queue));
        static::assertNull($result);

        sleep(1);

        $result = $this->listener->onStopConditionCheck(new ProcessIdleEvent($this->worker, $this->queue));
        static::assertInstanceOf(ExitWorkerLoopResult::class, $result);
    }
}
