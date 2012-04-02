<?php

namespace SlmQueue\Job;

use Pheanstalk;

interface Producer extends Job
{
    public function setPheanstalk (Pheanstalk $pheanstalk);
}
