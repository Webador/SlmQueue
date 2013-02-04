<?php

namespace SlmQueue\Producer;

use SlmQueue\Service\BeanstalkInterface;

interface ProducerInterface
{
    /**
     * @param  BeanstalkInterface $beanstalk
     * @return ProducerInterface
     */
    public function setBeanstalk(BeanstalkInterface $beanstalk);
}
