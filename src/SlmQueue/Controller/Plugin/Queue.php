<?php

namespace SlmQueue\Controller\Plugin;

class Queue
{
    protected $pheanstalk;

    public function __construct (Pheanstalk $pheanstalk)
    {
        $this->pheanstalk = $pheanstalk;
    }

    /**
     * Add job to the beanstalk queue
     *
     * @param string $name FQCN of the job
     * @param array  $params
     */
    public function add ($name, array $params = null, $priority = null, $delay = null, $ttr = null)
    {
        $data = Json::encode(compact('name', 'params'));
        return $this->pheanstalk->put($data, $priority, $delay, $ttr);
    }
}
