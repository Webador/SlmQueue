<?php

namespace SlmQueue\Service;

use SlmQueue\Job\JobInterface;

class PheanstalkBridge implements BeanstalkInterface
{
    public function __construct(Pheanstalk $pheanstalk)
    {
        $this->pheanstalk = $pheanstalk;
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
            $this->delete($job);

        } catch (ReleasableException $e) {
            $priority = method_exists($e, 'getPriority') ? $e->getPriority() : Pheanstalk::DEFAULT_PRIORITY;
            $delay    = method_exists($e, 'getDelay')    ? $e->getDelay()    : Pheanstalk::DEFAULT_DELAY;

            $this->logException($job, $e);
            $this->release($job, $priority, $delay, true);

        } catch (BuryableException $e) {
            $priority = method_exists($e, 'getPriority') ? $e->getPriority() : Pheanstalk::DEFAULT_PRIORITY;

            $this->logException($job, $e);
            $this->bury($job, $priority, true);

        } catch (Exception $e) {
            $this->logException($job, $e);
            $this->bury($job, 0, true);
        }
    }

    public function put(JobInterface $job, $priority = null, $delay = null, $ttr = null)
    {
        $data = Json::encode(array(
            'name'    => get_class($job),
            'options' => $job->getOptions(),
        ));

        $result = $this->pheanstalk->put($data, $priority, $delay, $ttr);
        $job->setId($result);
    }

    public function delete(JobInterface $job)
    {
        $this->pheanstalk->delete($job);
    }

    public function release(JobInterface $job, $priority = null, $delay = null, $usePut = false)
    {
        if ($usePut) {
            $this->delete($job);
            $this->put($job, $priority, $delay)
        } else {
            $this->pheanstalk->release($job, $priority, $delay);
        }
    }

    public function bury(JobInterface $job, $priority = null, $usePut = false)
    {
        if ($usePut) {
            $this->delete($job);
            $this->put($job);
            $this->bury($job, $priority);
        } else {
            $this->pheanstalk->bury($job, $priority);
        }
    }

    public function kick(JobInterface $job)
    {
        $this->pheanstalk->kick($job);
    }

    protected function load($data)
    {
        $data    = Json::decode($data->getData());
        $name    = $data->name;
        $options = $data->options;

        return $this->getJobManager()->get($name, $options);
    }

    protected function logException(JobInterface $job, Exception $e)
    {
        $options = $job->getOptions();

        if (array_key_exists('loop_count', $options)) {
            $options['loop_count']++;
            $job->setOptions($options);

            return $job;
        }

        /**
         * @todo Store all exceptions, even for a second exception thrown in the cycle
         */
        $options['exception'] = array(
            'type'    => get_class($e),
            'message' => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
        );
        /**
         * @todo Log previous exceptions $e->getPrevious() and add the complete list
         */
        $options['loop_count'] = 1;
        $job->setOptions($options);

        return $job;
    }
}