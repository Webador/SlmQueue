<?php

namespace SlmQueue\Listener\Strategy;

use SlmQueue\Worker\WorkerEvent;
use Zend\Console\Console;
use Zend\EventManager\EventManagerInterface;

class LogJobStrategy extends AbstractStrategy
{
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(WorkerEvent::EVENT_PROCESS_JOB_PRE, array($this, 'onLogJobProcessStart'), $priority);
        $this->listeners[] = $events->attach(WorkerEvent::EVENT_PROCESS_JOB_POST, array($this, 'onLogJobProcessDone'), $priority);
    }

    /**
     * @param WorkerEvent $e
     */
    public function logJobProcessStart(WorkerEvent $e)
    {
        $job  = $e->getJob();
        $name = $job->getMetadata('name');
        if (null === $name) $name = get_class($job);

        $console = Console::getInstance();
        $console->write(sprintf('Processing job %s...', $name));
    }

    /**
     * @param WorkerEvent $e
     */
    public function logJobProcessDone(WorkerEvent $e)
    {
        $console = Console::getInstance();
        $console->writeLine('Done!');
    }

}
