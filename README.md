SlmQueue
========

Master [![Build Status](https://travis-ci.org/juriansluiman/SlmQueue.png?branch=master)](https://travis-ci.org/juriansluiman/SlmQueue)

Version 0.2.2 Created by Jurian Sluiman and Michaël Gallego

> Please note that this is a complete rewrite of SlmQueue. The previous version was tagged as version 0.1.0, so if you still
> want to use it, please update your composer.json file.


Requirements
------------
* [Zend Framework 2](https://github.com/zendframework/zf2)


Introduction
------------

SlmQueue is a Zend Framework 2 module that integrates with various queuing systems. SlmQueue is only a base module that
contains interfaces and abstract classes. Here are the current supported systems:

* Beanstalk: use [SlmQueueBeanstalkd](https://github.com/juriansluiman/SlmQueueBeanstalkd)
* Amazon SQS: use [SlmQueueSqs](https://github.com/juriansluiman/SlmQueueSqs)

A job queue helps to offload long or memory-intensive processes from the HTTP requests users sent to the Zend Framework 2
application. There are many use cases for asynchronous jobs and the most common will be:

1. Send an email
2. Create a PDF file
3. Connect to a third party server

In all cases you want to serve the response as soon as possible to your visitor, without letting them wait for this
long process. With SlmQueue you are able to do this, with some other neat features.


Installation
------------

SlmQueue works with Composer. To install it, just add the following line into your `composer.json` file:

```json
"require": {
	"juriansluiman/slm-queue": ">=0.2"
}
```

Then, enable the module by adding `SlmQueue` in your `application.config.php` file. You may also want to configure
the module: just copy the `slm_queue.local.php.dist` (you can find this file in the `config` folder of SlmQueue) into
your `config/autoload` folder, and override what you want.

> SlmQueue is pretty useless by itself, as it is mainly interfaces and abstract classes. To make it really powerful,
you'll likely add [SlmQueueBeanstalkd](https://github.com/juriansluiman/SlmQueueBeanstalkd) and/or [SlmQueueSqs](https://github.com/juriansluiman/SlmQueueSqs).


Documentation
-------------

### Why a queue management system?

Let's say that our task is to encode a video to a specific format. Of course, we don't want to block the user on the
page until the whole encoding is done (this can take minutes!). Instead, we would like the user to upload the video,
and warn him once the job is done (by sending him an email, for instance).

To answer to this problem, queue management systems are a great solutions. They allow us to save a job, and execute
it later (for instance from another server whose task is only doing encoding).

### Creating a job

Before digging to the code, let's see what happen. When a job is pushed to a queue, its content is serialized
(by default, to Json) as well as the class name of the job (so that it can be pulled from the JobPluginManager). On
the other hand, when a job is popped from a queue, the content is deserialized and set back to the job by calling
`setContent` method of the job.

The first thing to do is to create a new class that represents the task to do. In this case, the task is an
encoding task. It **must** implements `SlmQueue\Job\JobInterface`. For convenience purpose, SlmQueue provides an abstract
class, `SlmQueue\Job\AbstractClass`. The only method you must provides is the `execute` method:

```php
namespace Application\Job;

use SlmQueue\Job\AbstractJob;

class EncodingJob extends AbstractJob
{
    /**
     * Encode the video!
     */
    public function execute()
    {
        $originalUrl       = $this->content['originalUrl'];
        $destinationFormat = $this->content['destinationFormat'];

        // Do some heavy stuff here!
        // ...
    }
}
```

Then, you can create a job (for instance, in a controller or in a service) and filling the content array of the
job (either using the constructor or the `setContent` method):

```php
public function encodeAction()
{
    // Here the parameters come from simple GET parameters
    $job = new EncodingJob(array(
        'originalUrl'       => $this->params()->fromQuery('originalUrl'),
        'destinationFormat' => $this->params()->fromQuery('destinationFormat')
    ));

    // Get the queue plugin manager
    $queueManager = $this->serviceLocator->get('SlmQueue\Queue\QueuePluginManager');
    $queue        = $queueManager->get('encodingQueue');

    $queue->push($queue);
}
```

Here, we simply specify the data of the job, then we get the queue manager (more on that later), get the queue
that will store those jobs (in most queuing systems you can create as much queues as you want), and then we push
it so that it can pe popped later.


### Handling dependencies for jobs

Often, your job will have dependencies. For instance, the EncodingJob may need an Encoder object to help encode
the videos. Hopefully, SlmQueue makes this easy. Just modify the constructor of your job:

```php
namespace Application\Job;

use SlmQueue\Job\AbstractJob;

class EncodingJob extends AbstractJob
{
    protected $encoder;

    public function __construct(Encoder $encoder, $content = null, array $metadata = array())
    {
        $this->encoder = $encoder;
        parent::__construct($content, $metadata);
    }

    /**
     * Encode the video!
     */
    public function execute()
    {
        $originalUrl       = $this->content['originalUrl'];
        $destinationFormat = $this->content['destinationFormat'];

        // Do some heavy stuff here! BUT WITH OUR ENCODER!
        $encoder->encode($originalUrl, $destinationFormat);
    }
}
```

Then, adds the following lines in your module.config.php file:

```php
return array(
    'slm_queue' => array(
        'jobs' => array(
            'factories' => array(
                'Application\Job\EncodingJob' => function($locator) {
                    $encoder = new Encoder();
                    return new \Application\Job\EncodingJob($encoder);
                }
            )
        )
    )
);
```

> Note: if you don't have any dependencies for your jobs, you DO NOT need to add all your jobs to the invokables`
> list, because the JobPluginManager is configured in a way that it automatically adds any unknown classes to the
> `invokables` list.


### Adding queues

The Job thing is pretty agnostic to any queue management systems. However, the queues are not. SlmQueue provides
a QueueInterface that guarantees that each queue must at least implement the following methods:

* getName(): get the name of the queue
* getJobPluginManager(): get the job plugin manager, from where every job is pulled
* push(JobInterface $job, array $options = array()): add a new job to the queue
* pop(JobInterface $job, array $options = array()): pop a new job to the queue
* delete(JobInterface $job): delete a job from the queue

In order to have concrete queues, you must either install `SlmQueueBeanstalkd` or `SlmQueueSqs` modules. For more
information, please refer to the [SlmQueueBeanstalkd documentation](https://github.com/juriansluiman/SlmQueueBeanstalkd) or to the [SlmQueueSqs documentation](https://github.com/juriansluiman/SlmQueueSqs).

In both cases, adding a new queue is as simple as adding a new line in your `module.config.php` file:

```php
return array(
    'slm_queue' => array(
        'queues' => array(
            'factories' => array(
                'encodingQueue' => 'SlmQueueBeanstalkd\Factory\TubeFactory' // This is the factory provided by
                                                                            // SlmQueueBeanstalkd module
            )
        )
    )
);
```


### Executing jobs

Once again, executing jobs is dependant on the queue system used. Therefore, please refer to either SlmQueueBeanstalkd
or SlmQueueSqs documentation.
