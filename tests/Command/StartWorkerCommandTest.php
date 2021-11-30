<?php

namespace SlmQueueTest\Command;

use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SlmQueue\Command\StartWorkerCommand;
use SlmQueue\Controller\Exception\WorkerProcessException;
use SlmQueue\Queue\QueuePluginManager;
use SlmQueue\Strategy\MaxRunsStrategy;
use SlmQueue\Strategy\ProcessQueueStrategy;
use SlmQueueTest\Asset\FailingJob;
use SlmQueueTest\Asset\SimpleJob;
use SlmQueueTest\Asset\SimpleWorker;
use SlmQueueTest\Util\ServiceManagerFactory;
use Symfony\Component\Console\Exception\RuntimeException as ConsoleRuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class StartWorkerCommandTest extends TestCase
{
    /** @var OutputInterface&MockObject */
    private OutputInterface $output;

    private QueuePluginManager $queuePluginManager;
    private StartWorkerCommand $command;

    public function setUp(): void
    {
        $serviceManager = ServiceManagerFactory::getServiceManager();
        $this->queuePluginManager = $serviceManager->get(QueuePluginManager::class);

        /** @var SimpleWorker */
        $worker = $this->queuePluginManager->get('basic-queue')->getWorker();
        $eventManager = $worker->getEventManager(); 

        (new ProcessQueueStrategy())->attach($eventManager);
        (new MaxRunsStrategy(['max_runs' => 1]))->attach($eventManager);

        $this->queuePluginManager = $serviceManager->get(QueuePluginManager::class);
        $this->output = $this->createMock(OutputInterface::class);

        $this->command = new StartWorkerCommand($this->queuePluginManager);
    }

    public function testThrowExceptionIfQueueIsUnknown(): void
    {
        $input = new ArrayInput([
            'queue' => 'unknown',
        ]);

        $this->expectException(ServiceNotFoundException::class);

        $this->command->run($input, $this->output);
    }

    public function testThrowExceptionIfNoQueue(): void
    {
        $input = new ArrayInput([]);

        $this->expectException(ConsoleRuntimeException::class);

        $this->command->run($input, $this->output);
    }

    public function testSimpleJob(): void
    {
        $input = new ArrayInput([
            'queue' => 'basic-queue',
        ]);

        $queue = $this->queuePluginManager->get('basic-queue');
        $queue->push(new SimpleJob());

        $this->output
             ->expects($this->once())
             ->method('writeLn')
             ->with(
                 $this->logicalAnd(
                     $this->stringContains("Finished worker for queue 'basic-queue'"),
                     $this->stringContains("maximum of 1 jobs processed")
                 )
             );

        $result = $this->command->run($input, $this->output);

        $this->assertSame(0, $result);
    }

    public function testFailingJobThrowException(): void
    {
        $input = new ArrayInput([
            'queue' => 'basic-queue',
        ]);

        $queue = $this->queuePluginManager->get('basic-queue');
        $queue->push(new FailingJob());

        $this->expectException(WorkerProcessException::class);

        $this->command->run($input, $this->output);
    }
}
