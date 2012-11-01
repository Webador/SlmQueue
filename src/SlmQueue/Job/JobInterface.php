<?php

namespace SlmQueue\Job;

interface JobInterface
{
    public function __invoke();
    public function setOptions (array $options);
    public function getOptions ();
    public function setId($id);
    public function getId();
}
