<?php

namespace SlmQueue\Service;

use Pheanstalk;
use Pheanstalk_Job;
use SlmQueue\Exception\BuryableException;
use SlmQueue\Exception\ReleasableException;
use SlmQueue\Job\JobInterface;
use SlmQueue\Job\JobPluginManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Json\Json;
use Zend\Log\Logger;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PheanstalkBridge implements
    BeanstalkInterface,
    EventManagerAwareInterface,
    LoggerAwareInterface,
    ServiceLocatorAwareInterface
{
    /**
     * @var Pheanstalk
     */
    protected $pheanstalk;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var JobPluginManager
     */
    protected $jobPluginManager;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Constructor
     *
     * @param Pheanstalk $pheanstalk
     */
    public function __construct(Pheanstalk $pheanstalk)
    {
        $this->pheanstalk = $pheanstalk;
    }

    /**
     * {@inheritDoc}
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $this->events = $events;
    }

    /**
     * {@inheritDoc}
     */
    public function getEventManager()
    {
        return $this->events;
    }

    /**
     * {@inheritDoc}
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Get the logger
     *
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Set the job manager
     *
     * @param JobPluginManager $jobPluginManager
     */
    public function setJobManager(JobPluginManager $jobPluginManager)
    {
        $this->jobPluginManager = $jobPluginManager;
    }

    /**
     * Get the job manager
     *
     * @return JobPluginManager
     */
    public function getJobManager()
    {
        if (!$this->jobPluginManager instanceof JobPluginManager) {
            $this->jobPluginManager = new JobPluginManager();
        }

        return $this->jobPluginManager;
    }

    /**
     * {@inheritDoc}
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * {@inheritDoc}
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * {@inheritDoc}
     */
    public function reserve()
    {
        $this->log(Logger::DEBUG, 'Reserve job');
        $data = $this->pheanstalk->reserve();

        if ($data instanceof Pheanstalk_Job) {
            return $this->load($data);
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(JobInterface $job)
    {
        try {
            $job->__invoke();
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

        } catch (\Exception $e) {
            $this->log(Logger::ERR,
                       sprintf('Caught unknown exception for job #%s (%s): %s',
                               $job->getId(),
                               get_class($job),
                               get_class($e)
                       ));
            $this->bury($job, 0);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function put(JobInterface $job, $priority = 1024, $delay = 0, $ttr = 60)
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

    /**
     * {@inheritDoc}
     */
    public function delete(JobInterface $job)
    {
        $this->log(Logger::DEBUG, sprintf('Job #%s delete (%s)', $job->getId(), get_class($job)));
        $this->trigger('delete', array('job' => $job));

        $this->pheanstalk->delete($job);
    }

    /**
     * {@inheritDoc}
     */
    public function release(JobInterface $job, $priority = 1024, $delay = 0)
    {
        $this->log(Logger::DEBUG, sprintf('Job #%s released (%s)', $job->getId(), get_class($job)));
        $this->trigger('release', array('job' => $job));

        $this->pheanstalk->release($job, $priority, $delay);
    }

    /**
     * {@inheritDoc}
     */
    public function bury(JobInterface $job, $priority = 1024)
    {
        $this->log(Logger::DEBUG, sprintf('Job #%s buried (%s)', $job->getId(), get_class($job)));
        $this->trigger('bury', array('job' => $job));

        $this->pheanstalk->bury($job, $priority);
    }

    /**
     * {@inheritDoc}
     */
    public function kick(JobInterface $job)
    {
        $this->log(Logger::DEBUG, sprintf('Job #%s kicked (%s)', $job->getId(), get_class($job)));
        $this->trigger('kick', array('job' => $job));

        $this->pheanstalk->kick($job);
    }

    /**
     * Convert a Pheanstalk_Job native object to an object implementing JobInterface. It can be used
     * to handle dependencies in your JobInterface objects by adding them to the JobManager
     *
     * @param  Pheanstalk_Job $data
     * @return JobInterface
     */
    protected function load(Pheanstalk_Job $data)
    {
        $id      = $data->getId();
        $data    = Json::decode($data->getData());
        $name    = $data->name;
        $options = $data->options;

        /** @var $job JobInterface */
        $job = $this->getJobManager()->get($name);
        $job->setId($id)
            ->setOptions($options);

        return $job;
    }

    /**
     * Trigger a new event
     *
     * @param       $name
     * @param array $options
     */
    protected function trigger($name, array $options)
    {
        if ($this->getEventManager() instanceof EventManagerInterface) {
            $this->getEventManager()->trigger($name, $this, $options);
        }
    }

    /**
     * Log a new message
     *
     * @param int    $priority
     * @param string $message
     */
    protected function log($priority, $message)
    {
        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->log($priority, $message);
        }
    }
}