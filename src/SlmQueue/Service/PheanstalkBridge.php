<?php

namespace SlmQueue\Service;

use Pheanstalk;

use SlmQueue\Job\JobInterface;
use SlmQueue\Job\JobManager;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

use Zend\Json\Json;

use Zend\Log\Logger;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerInterface;

class PheanstalkBridge implements
    BeanstalkInterface,
    EventManagerAwareInterface,
    LoggerAwareInterface
{
    protected $pheanstalk;
    protected $events;
    protected $logger;
    protected $manager;

    public function __construct(Pheanstalk $pheanstalk)
    {
        $this->pheanstalk = $pheanstalk;
    }

    public function setEventManager(EventManagerInterface $events)
    {
        $this->events = $events;
    }

    public function getEventManager()
    {
        return $this->events;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function setJobManager(JobManager $manager)
    {
        $this->manager = $manager;
    }

    public function getJobManager()
    {
        if (!$this->manager instanceof JobManager) {
            $this->manager = new JobManager;
        }
        return $this->manager;
    }

    /**
     * Reserve a job and return a Job instance
     *
     * The Pheanstalk service returns a data blob so convert that into
     * a class which implements the SlmQueue\Job\JobInterface.
     *
     * @return JobInterface Job instance
     */
    public function reserve()
    {
        $this->log(Logger::DEBUG, 'Reserve job');
        $data = $this->pheanstalk->reserve();
        $job  = $this->load($data);

        return $job;
    }

    /**
     * Execute a give job
     *
     * @param  JobInterface $job Job to be executed
     * @throws Exception Rethrow exception from job
     * @return void
     */
    public function execute(JobInterface $job)
    {
        try {
            $job();
            $this->log(Logger::DEBUG, sprintf('Job #%s executed (%s)', $job->getId(), get_class($job)));
            $this->delete($job);

        } catch (ReleasableException $e) {
            $priority = method_exists($e, 'getPriority') ? $e->getPriority() : Pheanstalk::DEFAULT_PRIORITY;
            $delay    = method_exists($e, 'getDelay')    ? $e->getDelay()    : Pheanstalk::DEFAULT_DELAY;

            $this->log(Logger::WARN,
                       sprintf('Caught ReleasableException for job #%s (%s): %s',
                               $job->getId(),
                               get_class($job),
                               get_class($e)
                       ));
            $this->release($job, $priority, $delay);

        } catch (BuryableException $e) {
            $priority = method_exists($e, 'getPriority') ? $e->getPriority() : Pheanstalk::DEFAULT_PRIORITY;

            $this->log(Logger::WARN,
                       sprintf('Caught BuryableException for job #%s (%s): %s',
                               $job->getId(),
                               get_class($job),
                               get_class($e)
                       ));
            $this->bury($job, $priority);

        } catch (Exception $e) {
            $this->log(Logger::ERR,
                       sprintf('Caught unknown exception for job #%s (%s): %s',
                               $job->getId(),
                               get_class($job),
                               get_class($e)
                       ));
            $this->bury($job, 0);
        }
    }

    public function put(JobInterface $job, $priority = null, $delay = null, $ttr = null)
    {
        $this->log(Logger::DEBUG, sprintf('Job #%s put (%s)', $job->getId(), get_class($job)));
        $this->trigger('put', array('job' => $job));

        $data = Json::encode(array(
            'name'    => get_class($job),
            'options' => $job->getOptions(),
        ));

        $result = $this->pheanstalk->put($data, $priority, $delay, $ttr);
        $job->setId($result);
    }

    public function delete(JobInterface $job)
    {
        $this->log(Logger::DEBUG, sprintf('Job #%s delete (%s)', $job->getId(), get_class($job)));
        $this->trigger('delete', array('job' => $job));

        $this->pheanstalk->delete($job);
    }

    public function release(JobInterface $job, $priority = null, $delay = null)
    {
        $this->log(Logger::DEBUG, sprintf('Job #%s released (%s)', $job->getId(), get_class($job)));
        $this->trigger('release', array('job' => $job));

        $this->pheanstalk->release($job, $priority, $delay);
    }

    public function bury(JobInterface $job, $priority = null)
    {
        $this->log(Logger::DEBUG, sprintf('Job #%s buried (%s)', $job->getId(), get_class($job)));
        $this->trigger('bury', array('job' => $job));

        $this->pheanstalk->bury($job, $priority);
    }

    public function kick(JobInterface $job)
    {
        $this->log(Logger::DEBUG, sprintf('Job #%s kicked (%s)', $job->getId(), get_class($job)));
        $this->trigger('kick', array('job' => $job));

        $this->pheanstalk->kick($job);
    }

    protected function load($data)
    {
        $id      = $data->getId();
        $data    = Json::decode($data->getData());
        $name    = $data->name;
        $options = $data->options;

        $job = $this->getJobManager()->get($name, $options);
        $job->setId($id);

        return $job;
    }

    protected function trigger($name, array $options)
    {
        if ($this->getEventManager() instanceof EventManagerInterface) {
            $this->getEventManager()->trigger($name, $this, $options);
        }
    }

    protected function log($priority, $message)
    {
        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->log($priority, $message);
        }
    }
}