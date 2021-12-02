<?php

namespace App;

use SlmQueue\Job\AbstractJob;

class TestJob extends AbstractJob
{
    public function execute()
    {
        file_put_contents('temp/succesful', 'YES');
    }
}
