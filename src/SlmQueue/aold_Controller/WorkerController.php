<?php

namespace SlmQueue\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use SlmQueue\Service\BeanstalkInterface;
use SlmQueue\Options\ModuleOptions;

class WorkerController extends AbstractActionController
{
    /**
     * @var BeanstalkInterface
     */
    protected $beanstalk;

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var bool
     */
    protected $stopped;

    /**
     * Constructor
     *
     * @param BeanstalkInterface $beanstalk
     * @param ModuleOptions      $options
     */
    public function __construct (BeanstalkInterface $beanstalk, ModuleOptions $options)
    {
        $this->beanstalk = $beanstalk;
        $this->options   = $options;
    }

    /**
     * @return BeanstalkInterface
     */
    public function getBeanstalk()
    {
        return $this->beanstalk;
    }

    /**
     * @return ModuleOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Reserve jobs from the queue
     */
    public function reserveAction()
    {
        $this->prepare();

        $beanstalk = $this->getBeanstalk();
        $options   = $this->getOptions();

        $i = 1;
        while (true) {
            $job = $beanstalk->reserve();
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
    }

    /**
     * Prepare the reserve action
     */
    protected function prepare()
    {
        declare(ticks = 1);
        pcntl_signal(SIGTERM, array($this, 'signal'));
        pcntl_signal(SIGINT,  array($this, 'signal'));
    }

    /**
     * @param $signo
     */
    public function signal($signo)
    {
        switch($signo) {
            case SIGTERM:
            case SIGINT:
                $this->stopped(true);
                break;
        }
    }

    /**
     * @param  null $flag
     * @return bool
     */
    protected function stopped($flag = null)
    {
        if (null !== $flag) {
            $this->stopped = (bool) $flag;
        }

        return $this->stopped;
    }
}
