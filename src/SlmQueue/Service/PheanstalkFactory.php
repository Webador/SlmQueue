<?php

namespace SlmQueue\Service;

use Pheanstalk;

class PheanstalkFactory
{
    public static function create (array $options)
    {
        $host = $options['host'];
        $port = Pheanstalk::DEFAULT_PORT;
        $connectTimeout = null;
        
        if (isset($options['port'])) {
            $port = $options['port'];
        }
        
        if (isset($options['connect_timeout'])) {
            $connectTimeout = $options['connect_timeout'];
        }
        
        $pheanstalk = new Pheanstalk($host, $port, $connectTimeout);
        
        if (isset($options['ignore'])) {
            $ignore = $options['ignore'];
            if (!is_array($ignore)) {
                $pheanstalk->ignore($ignore);
            } else {
                foreach ($ignore as $tube) {
                    $pheanstalk->ignore($tube);
                }
            }
        }
        
        if (isset($options['use'])) {
            $pheanstalk->use($options['use']);
        }
        
        if (isset($options['watch'])) {
            $watch = $options['watch'];
            if (!is_array($watch)) {
                $pheanstalk->watch($watch);
            } else {
                foreach ($watch as $tube) {
                    $pheanstalk->watch($tube);
                }
            }
        }
        
        return $pheanstalk;
    }
}
