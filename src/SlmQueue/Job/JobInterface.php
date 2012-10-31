<?php

namespace SlmQueue\Job;

interface JobInterface
{
    public function __invoke();
    public function setOptions (array $options);
    public function getOptions ();
    public function getId();
}
