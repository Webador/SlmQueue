<?php

namespace SlmQueue\Service;

use SlmQueue\Job\JobInterface;

interface BeanstalkInterface
{
    public function reserve();
    public function execute(JobInterface $job);
    public function put(JobInterface $job, $priority = null, $delay = null, $ttr = null);
    public function delete(JobInterface $job);
    public function release(JobInterface $job);
    public function buty(JobInterface $job);
    public function kick(JobInterface $job);
}