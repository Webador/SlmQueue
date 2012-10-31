<?php

namespace SlmQueue\Job;

use SlmQueue\Service\BeanstalkInterface;

interface ProducerInterface
{
    public function setBeanstalk (BeanstalkInterface $beanstalk);
}
