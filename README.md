SlmQueue
========

[![Build Status](https://travis-ci.org/juriansluiman/SlmQueue.png?branch=master)](https://travis-ci.org/juriansluiman/SlmQueue)
[![Latest Stable Version](https://poser.pugx.org/slm/queue/v/stable.png)](https://packagist.org/packages/juriansluiman/slm-queue)
[![Latest Unstable Version](https://poser.pugx.org/slm/queue/v/unstable.png)](https://packagist.org/packages/juriansluiman/slm-queue)

Created by Jurian Sluiman and Michaël Gallego

Introduction
------------

SlmQueue is a job queue abstraction layer for Zend Framework 2 applications. It supports various job queue systems and makes your
application independent from the underlying system you use. The currently supported systems have each their own adapter-module and
are the following:

* Beanstalk: use [SlmQueueBeanstalkd](https://github.com/juriansluiman/SlmQueueBeanstalkd)
* Amazon SQS: use [SlmQueueSqs](https://github.com/juriansluiman/SlmQueueSqs)
* Doctrine ORM: use [SlmQueueDoctrine](https://github.com/juriansluiman/SlmQueueDoctrine)

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
    "slm/queue": "0.3.*"
}
```

Then, enable the module by adding `SlmQueue` in your `application.config.php` file. You may also want to configure
the module: just copy the `slm_queue.global.php.dist` (you can find this file in the `config` folder of SlmQueue) into
your `config/autoload` folder, and override what you want.

> SlmQueue is pretty useless by itself, as it is mainly interfaces and abstract classes. To make it really powerful,
you'll likely add [SlmQueueBeanstalkd](https://github.com/juriansluiman/SlmQueueBeanstalkd), [SlmQueueSqs](https://github.com/juriansluiman/SlmQueueSqs)
or [SlmQueueDoctrine](https://github.com/juriansluiman/SlmQueueDoctrine)

Requirements
------------
* [Zend Framework >= 2.2](https://github.com/zendframework/zf2)

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

    $queue->push($job);
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

    public function __construct(Encoder $encoder)
    {
        $this->encoder = $encoder;
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
        'job_manager' => array(
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

> Note: if you don't have any dependencies for your jobs, you DO NOT need to add all your jobs to the `invokables`
> list, because the JobPluginManager is configured in a way that it automatically adds any unknown classes to the
> `invokables` list.

#### Having access to the queue that execute the job

When inside the `execute` method of your job, you may need to have access to the queue from which the job was
extracted (for example to create another job as a result). You can do so by implementing the `QueueAwareInterface`
interface in your job:

```php
namespace Application\Job;

use SlmQueue\Job\AbstractJob;
use SlmQueue\Queue\QueueAwareInterface;
use SlmQueue\Queue\QueueInterface;

class EncodingJob extends AbstractJob implements QueueAwareInterface
{
    protected $queue;

    public function getQueue()
    {
        return $this->queue;
    }

    public function setQueue(QueueInterface $queue)
    {
        $this->queue = $queue;
    }

    public function execute()
    {
        // You can use the queue here!
    }
}
```

If you want to avoid the boilerplate code, you can use the QueueAwareTrait trait (only for PHP >=5.4):

```php
namespace Application\Job;

use SlmQueue\Job\AbstractJob;
use SlmQueue\Queue\QueueAwareTrait;
use SlmQueue\Queue\QueueAwareInterface;

class EncodingJob extends AbstractJob implements QueueAwareInterface
{
    use QueueAwareTrait;

    public function execute()
    {
        // You can use the queue here!
    }
}
```

### Adding queues

The Job thing is pretty agnostic to any queue management systems. However, the queues are not. SlmQueue provides
a QueueInterface that guarantees that each queue must at least implement the following methods:

* getName(): get the name of the queue
* getJobPluginManager(): get the job plugin manager, from where every job is pulled
* push(JobInterface $job, array $options = array()): add a new job to the queue
* pop(JobInterface $job, array $options = array()): pop a new job to the queue
* delete(JobInterface $job): delete a job from the queue

In order to have concrete queues, you must either install `SlmQueueBeanstalkd`, `SlmQueueSqs` or `SlmQueueDoctrine` modules. For more
information, please refer to the [SlmQueueBeanstalkd documentation](https://github.com/juriansluiman/SlmQueueBeanstalkd), to the [SlmQueueSqs documentation](https://github.com/juriansluiman/SlmQueueSqs)
or to the [SlmQueueDoctrine documentation](https://github.com/juriansluiman/SlmQueueDoctrine)

In both cases, adding a new queue is as simple as adding a new line in your `module.config.php` file:

```php
return array(
    'slm_queue' => array(
        'queue_manager' => array(
            'factories' => array(
                'encodingQueue' => 'SlmQueueSqs\Factory\SqsQueueFactory' // This is the factory provided by
                                                                         // SlmQueueSqs module
            )
        )
    )
);
```

### Executing jobs

Once again, executing jobs is dependant on the queue system used. Therefore, please refer to either SlmQueueBeanstalkd,
SlmQueueSqs or SlmQueueDoctrine documentation.

### Events

#### WorkerEvent

Via events it becomes trivial to perform some actions before (or after) a queue or job is processed. To make this possible the worker implements the EventManagerAwareInterface and its EventManager triggers four kind of events;

* `WorkerEvent::EVENT_PROCESS_QUEUE_PRE` just before a Queue will be processed
* `WorkerEvent::EVENT_PROCESS_QUEUE_POST` just after a Queue has been processed
* `WorkerEvent::EVENT_PROCESS_JOB_PRE` just before a Job will be processed
* `WorkerEvent::EVENT_PROCESS_JOB_POST` just after a Job has been processed

A listener will recieve a WorkerEvent which contains a reference to the queue. The processJob.pre and processJob.post events will also contain the job that is the queue is processing.

```php
function(WorkerEvent $e) {
    $queue = $e->getQueue();
    $job   = $e->getJob();
});
```

##### Example SharedEventManager

Create a working directory before a queue is processed and remove it when the queue has finished processing its jobs.

```php
    public function onBootstrap(MvcEvent $e)
    {
        /** @var $sm \Zend\ServiceManager\ServiceManager */
        $sm = $e->getApplication()->getServiceManager();

        $sharedEventManager = $e->getApplication()->getEventManager()->getSharedManager();

        $sharedEventManager->attach('SlmQueue\Worker\AbstractWorker', WorkerEvent::EVENT_PROCESS_QUEUE_PRE, function(WorkerEvent $e) {
            $queueName = $e->getQueue()->getName();
            // mkdir ./data/queues/queueNamename
        });

        $sharedEventManager->attach('SlmQueue\Worker\AbstractWorker', WorkerEvent::EVENT_PROCESS_QUEUE_POST, function(WorkerEvent $e) {
            $queueName = $e->getQueue()->getName();
            // rm -Rf ./data/queues/$queueName
        });
    }
```

##### More complex example with an AggregateListener

The MvcTranslator will be configured to whatever the default locale is the first time it is used. That means that without some additional work the first job that the the worker processes will dictate the used locale. Rendering emails in multiple languages is problematic this way.

Jobs that need to be localized should implement LocaleAwareJobInterface and whenever the job is created the developer should set the locale the Job is created in.

```php
interface LocaleAwareJobInterface
{
    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
}
```

We create an aggregate listener that configures the Translator before a job is executed and reverts the configuration to whatever it was when the job is finished;

```php
class BootstrapTranslatorJobListener extends AbstractListenerAggregate {

    /**
     * @var Stores original locale while processing a Job
     */
    protected $locale;

    /**
     * @var Instance of Translator to manipulate
     */
    protected $translator;

    /**
     * @param Translator $translator to manipulate
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->handlers[] = $events->attach(WorkerEvent::EVENT_PROCESS_JOB_PRE, array($this, 'onPreJobProcessing'));
        $this->handlers[] = $events->attach(WorkerEvent::EVENT_PROCESS_JOB_POST, array($this, 'onPostJobProcessing'));
    }

    protected function onPreJobProcessing(WorkerEvent $e) {
        /** @var \SlmQueue\Job\JobInterface */
        $job = $e->getJob();

        if (!$job implements LocaleAwareJobInterface) {
            return;
        }

        $this->locale = $this->translator->getLocale();
        $this->translator->setLocale($job->getLocale());
    }

    protected function onPostJobProcessing(WorkerEvent $e) {
        $job = $e->getJob();

        if (!$job implements LocaleAwareJobInterface) {
            return;
        }

        $this->translator->setLocale($this->locale);
    }
}
```
Finally we consume this as follows;

```php
    public function onBootstrap(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();

        /** @var $sm \Zend\Mvc\I18n\Translator */
        $translator = $sm->get('MvcTranslator');

        /** @var $worker \SlmQueueDoctrine\Worker\DoctrineWorker */
        $worker = $sm->get('SlmQueueDoctrine\Worker\DoctrineWorker');

        $aggregateListener = new BootstrapTranslatorJobListener($translator);

        $worker->getEventManager()->attachAggregate(aggregateListener);
    }
```


