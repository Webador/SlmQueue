<?php

namespace SlmQueue\Controller;

use Zend\Mvc\Controller\ActionController;

use Pheanstalk;
use Pheanstalk_Job;
use Zend\Json\Json;
use SlmQueue\Job\Job;
use SlmQueue\Job\LocatorAware;
use SlmQueue\Job\Producer;

use SlmQueue\Exception\ReleasableException;
use SlmQueue\Exception\BuryableException;
use SlmQueue\Exception\RuntimeException;

class WorkerController extends ActionController
{
    /**
     * @var Pheanstalk
     */
    protected $pheanstalk;

    /**
     * Constructor
     *
     * @param Pheanstalk $pheanstalk
     */
    public function __construct (Pheanstalk $pheanstalk)
    {
        $this->pheanstalk = $pheanstalk;
    }

    /**
     * Reserve jobs from the queue
     */
    public function reserveAction ()
    {
        while (true) {
            $job = $this->pheanstalk->reserve();
            $job = $this->loadJob($job);
            
            $this->executeJob($job);
            $this->sleep();
        }
    }
    
    /**
     * Get job from queue data
     * 
     * @param string $data
     * @return Job 
     */
    protected function loadJob (Pheanstalk_Job $job)
    {
        $data   = Json::decode($job->getData());
        $params = array('id' => $job->getId());
        if (isset($data->params)) {
            $params += (array) $data->params;
        }
        
        if (!class_exists($data->name, true)) {
            throw new RuntimeException(sprintf(
                'Class %s is not a valid class name',
                $data->name
            ));
        }

        $job = new $data->name;
        if (!$job instanceof Job) {
            throw new RuntimeException(sprintf(
                'Job %s is not an instance of SlmQueue\Job\Job',
                $data->name
            ));
        }
        $job->setParams($params);
        
        if ($job instanceof LocatorAware) {
            $job->setLocator($this->getLocator());
        }
        
        if ($job instanceof Producer) {
            $job->setPheanstalk($this->pheanstalk);
        }
        
        return $job;
    }

    /**
     * Execute the job
     *
     * @param Job $job
     */
    protected function executeJob (Job $job)
    {
        try {
            $job();
            $this->pheanstalk->delete($job);
            
        } catch (ReleasableException $e) {
            /**
             * @todo Set default delay time
             */
            $this->pheanstalk->release($job);
            
        } catch (BuryableException $e) {
            $this->pheanstalk->bury($job);
            
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    protected function sleep ()
    {
        $sleep = (int) $this->getRequest()->params()->sleep;
        
        if ($sleep) {
            usleep($sleep);
        }
    }
}
