<?php

namespace SlmQueueTest\Listener\Strategy;

use PHPUnit_Framework_TestCase;
use SlmQueue\Listener\Strategy\FileWatchStrategy;
use SlmQueue\Worker\WorkerEvent;
use SlmQueueTest\Asset\SimpleJob;

class FileWatchStrategyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var FileWatchStrategy
     */
    protected $listener;

    public function setUp()
    {
        $queue = $this->getMockBuilder('SlmQueue\Queue\AbstractQueue')
            ->disableOriginalConstructor()
            ->getMock();

        $ev    = new WorkerEvent($queue);
        $job   = new SimpleJob();

        $ev->setJob($job);

        $this->listener = new FileWatchStrategy();
        $this->event    = $ev;
    }

    public function testListenerInstanceOfAbstractStrategy()
    {
        $this->assertInstanceOf('SlmQueue\Listener\Strategy\AbstractStrategy', $this->listener);
    }

    public function testListensToCorrectEvents()
    {
        $evm = $this->getMock('Zend\EventManager\EventManagerInterface');

        $evm->expects($this->at(0))->method('attach')
            ->with(WorkerEvent::EVENT_PROCESS_IDLE, array($this->listener, 'onStopConditionCheck'));
        $evm->expects($this->at(1))->method('attach')
            ->with(WorkerEvent::EVENT_PROCESS_JOB_POST, array($this->listener, 'onStopConditionCheck'));

        $this->listener->attach($evm);
    }

    public function testPatternDefault()
    {
        // standard zf2 application php and phtml files
        $this->assertTrue($this->listener->getPattern() == '/^\.\/(config|module).*\.(php|phtml)$/');
    }

    public function testFilesGetterReturnEmptyArrayByDefault()
    {
        // standard zf2 application php and phtml files
        $this->assertEmpty($this->listener->getFiles());
    }

    public function testSettingAPatternWillResetFilesToEmpty()
    {
        $this->listener->setPattern('/^anything$/');
        $this->assertEmpty($this->listener->getFiles());
    }

    public function testSettingPatternNullifiesCurrentListOfFilesToWatch()
    {
        // builds a file list
        $this->listener->onStopConditionCheck($this->event);
        $this->assertNotEmpty($this->listener->getFiles());

        $this->listener->setPattern('/^$/');

        $this->assertTrue($this->listener->getPattern() == '/^$/');
        $this->assertCount(0, $this->listener->getFiles());
    }

    public function testCanFileFilesByPattern()
    {
        // builds a file list
        if (!is_dir('tests/build')) {
            mkdir('tests/build', 0755, true);
        }
        file_put_contents('tests/build/filewatch.txt', 'hi');

        $this->listener->setPattern('/^\.\/(tests\/build).*\.(txt)$/');
        $this->listener->onStopConditionCheck($this->event);

        $this->assertCount(1, $this->listener->getFiles());
    }

    public function testWatchedFileChangeStopsPropagation()
    {
        // builds a file list
        if (!is_dir('tests/build')) {
            mkdir('tests/build', 0755, true);
        }
        file_put_contents('tests/build/filewatch.txt', 'hi');

        $this->listener->setPattern('/^\.\/(tests\/build).*\.(txt)$/');
        $this->listener->onStopConditionCheck($this->event);

        $this->assertCount(1, $this->listener->getFiles());

        file_put_contents('tests/build/filewatch.txt', 'hello');

        $this->listener->onStopConditionCheck($this->event);
        $this->assertContains('file modification detected for', $this->listener->getExitState());
        $this->assertTrue($this->event->propagationIsStopped());
    }

    public function testWatchedFileRemovedStopsPropagation()
    {
        // builds a file list
        if (!is_dir('tests/build')) {
            mkdir('tests/build', 0755, true);
        }
        file_put_contents('tests/build/filewatch.txt', 'hi');

        $this->listener->setPattern('/^\.\/(tests\/build).*\.(txt)$/');
        $this->listener->onStopConditionCheck($this->event);

        unlink('tests/build/filewatch.txt');

        $this->listener->onStopConditionCheck($this->event);

        $this->assertContains('file modification detected for', $this->listener->getExitState());
        $this->assertTrue($this->event->propagationIsStopped());
    }
}
