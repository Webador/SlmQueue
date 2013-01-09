<?php

namespace SlmQueue\Producer;

use SlmQueue\Service\BeanstalkInterface;

interface ProducerInterface
{
    public function setBeanstalk (BeanstalkInterface $beanstalk);
}
