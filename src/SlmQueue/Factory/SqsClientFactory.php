<?php

namespace SlmQueue\Factory;

use Aws\Common\Aws;
use Aws\Sqs\SqsClient;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * SqsClientFactory
 */
class SqsClientFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config')['sqs'];

        // Connection settings for SQS can be set either using the global aws_config.php file,
        // or by specifically setting the connection array
        if (isset($config['connection'])) {
            return SqsClient::factory(array(
                $config['key'],
                $config['secret'],
                $config['region']
            ));
        }

        // Otherwise, we create the Amazon SQS client using the config file
        $config = $config['aws_config'];
        if (!empty($config)) {
            return Aws::factory($config)->get('sqs');
        }

        throw new Exception\RuntimeException(
            'Impossible to connect to Amazon SQS because neither the connection array nor the AWS config
             file seem to be valid. Be sure to check the SlmQueue documentation !'
        );
    }
}
