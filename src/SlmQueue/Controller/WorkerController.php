<?php

namespace SlmQueue\Controller;

use Zend\Mvc\Controller\ActionController;
        
use Zend\Json\Json,
    SlmQueue\Job\Job,
    SlmQueue\Job\LocatorAware,
    SlmQueue\Job\Producer;

use SlmQueue\Exception\ReleasableException,
    SlmQueue\Exception\BuryableException,
    Exception;

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
        $params = $this->getRequest()->params();

        if (isset($params->watch)) {
            $this->pheanstalk->watch($params->watch);
        }
        
        if (isset($params->ignore)) {
            $this->pheanstalk->ignore($params->ignore);
        }
        
        while (true) {
            $data = $this->pheanstalk->reserve();
            $job  = $this->load($data);
            
            $this->execute($job);
            $this->sleep();
        }
    }
    
    /**
     * Get job from queue data
     * 
     * @param string $data
     * @return Job 
     */
    protected function load ($data)
    {
        /**
         * @todo $data is a Pheanstalk_Job
         */
        $job = Json::decode($data);
        
        $job = $this->getLocator()->get($job->name, $job->params);
        
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
    protected function execute (Job $job)
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
