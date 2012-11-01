<?php

namespace SlmQueue\Service;

use Pheanstalk;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PheanstalkFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config  = $serviceLocator->get('config');
        $config  = $config['pheanstalk'];

        $host    = $config['host'];
        $port    = $config['port'] ?: Pheanstalk::DEFAULT_PORT;
        $timeout = $config['connection_timeout'] ?: null;

        $pheanstalk = new Pheanstalk($host, $port, $timeout);

        if (isset($config['ignore'])) {
            $ignore = $config['ignore'];
            if (!is_array($ignore)) {
                $ignore = (array) $ignore;
            }

            foreach ($ignore as $tube) {
                $pheanstalk->ignore($tube);
            }
        }

        if (isset($config['watch'])) {
            $watch = $config['watch'];
            if (!is_array($watch)) {
                $watch = (array) $watch;
            }

            foreach ($watch as $tube) {
                $pheanstalk->watch($tube);
            }
        }

        if (isset($config['use'])) {
            $pheanstalk->use($config['use']);
        }

        return $pheanstalk;
    }
}
