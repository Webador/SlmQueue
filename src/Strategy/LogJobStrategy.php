<?php

namespace SlmQueue\Strategy;

use Laminas\Console\Adapter\AdapterInterface;
use Laminas\EventManager\EventManagerInterface;
use SlmQueue\Worker\Event\ProcessJobEvent;
use SlmQueue\Worker\Event\WorkerEventInterface;

class LogJobStrategy extends AbstractStrategy
{
    /**
     * @var AdapterInterface
     */
    protected $console;

    public function __construct(AdapterInterface $console, array $options = null)
    {
        $this->console = $console;

        parent::__construct($options);
    }

    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(
            WorkerEventInterface::EVENT_PROCESS_JOB,
            [$this, 'onLogJobProcessStart'],
            1000
        );
        $this->listeners[] = $events->attach(
            WorkerEventInterface::EVENT_PROCESS_JOB,
            [$this, 'onLogJobProcessDone'],
            -1000
        );
    }

    public function onLogJobProcessStart(ProcessJobEvent $processJobEvent): void
    {
        $job = $processJobEvent->getJob();
        $name = $job->getMetadata('name');
        if (null === $name) {
            $name = get_class($job);
        }

        $this->console->write(sprintf('Processing job %s...', $name));
    }

    public function onLogJobProcessDone(ProcessJobEvent $processJobEvent): void
    {
        $this->console->writeLine('Done!');
    }
}
