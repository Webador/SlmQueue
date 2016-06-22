<?php

namespace SlmQueue\Strategy;

use SlmQueue\Worker\Event\WorkerEventInterface;
use SlmQueue\Worker\Event\ProcessJobEvent;
use Zend\Console\Adapter\AdapterInterface;
use Zend\EventManager\EventManagerInterface;

class LogJobStrategy extends AbstractStrategy
{
    /**
     * @var AdapterInterface
     */
    protected $console;

    /**
     * @param AdapterInterface $console
     * @param array            $options
     */
    public function __construct(AdapterInterface $console, array $options = null)
    {
        $this->console = $console;

        parent::__construct($options);
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
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

    /**
     * @param ProcessJobEvent $processJobEvent
     */
    public function onLogJobProcessStart(ProcessJobEvent $processJobEvent)
    {
        $job  = $processJobEvent->getJob();
        $name = $job->getMetadata('name');
        if (null === $name) {
            $name = get_class($job);
        }

        $this->console->write(sprintf('Processing job %s...', $name));
    }

    /**
     * @param ProcessJobEvent $processJobEvent
     */
    public function onLogJobProcessDone(ProcessJobEvent $processJobEvent)
    {
        $this->console->writeLine('Done!');
    }
}
