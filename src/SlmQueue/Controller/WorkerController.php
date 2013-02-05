<?php

namespace SlmQueue\Controller;

use SlmQueue\Worker\Sqs\Worker as SqsWorker;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * WorkerController
 */
class WorkerController extends AbstractActionController
{
    /**
     * Process the jobs until the queues are empty OR that some criterias specified in config are met,
     * like max runs or memory consumption
     *
     * @return string
     */
    public function processAction()
    {
        $queuingSystem = $this->params()->fromRoute('system', null);
        $queueName     = $this->params()->fromRoute('queueName', '');

        switch($queuingSystem) {
            case 'sqs':
                return $this->processSqsQueue($queueName);
                break;
            case 'beanstalk':
                return $this->processBeanstalkQueue($queueName);
                break;
        }

        return sprintf(
            "\n\nNo worker found. %s queuing system given, but only sqs and beanstalk are supported currently\n",
            $queuingSystem
        );

        // Now do the work!
        while (true) {
            $beanstalk->execute($job);

            if ($i === $options->getMaxRuns()) {
                break;
            }
            if (memory_get_usage() > $options->getMaxMemory() * 1024 * 1024) {
                break;
            }
            if ($this->stopped()) {
                break;
            }

            $i++;
        }

        return "\n\nDone!\n";
    }

    /**
     * Process a SQS queue
     *
     * @param  string $queueName
     * @return string
     */
    protected function processSqsQueue($queueName = '')
    {
        $worker = $this->serviceLocator->get('SlmQueue\Worker\Sqs\Worker');
    }
}
