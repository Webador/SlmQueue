SlmQueue
========

[![Build Status](https://travis-ci.org/juriansluiman/SlmQueue.png?branch=master)](https://travis-ci.org/juriansluiman/SlmQueue)
[![Code Coverage](https://scrutinizer-ci.com/g/juriansluiman/SlmQueue/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/juriansluiman/SlmQueue/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/slm/queue/v/stable.png)](https://packagist.org/packages/juriansluiman/slm-queue)
[![Latest Unstable Version](https://poser.pugx.org/slm/queue/v/unstable.png)](https://packagist.org/packages/juriansluiman/slm-queue)
[![Dependency Status](https://www.versioneye.com/user/projects/53d0f4b9ead8b3e94400000c/badge.svg?style=flat)](https://www.versioneye.com/user/projects/53d0f4b9ead8b3e94400000c)

Created by Jurian Sluiman and Michaël Gallego

Introduction
------------

SlmQueue is a job queue abstraction layer for Zend Framework 2 applications. It supports various job queue systems and
makes your application independent from the underlying system you use. The currently supported systems have each their
own adapter-module and are the following:

* Beanstalk: use [SlmQueueBeanstalkd](https://github.com/juriansluiman/SlmQueueBeanstalkd)
* Amazon SQS: use [SlmQueueSqs](https://github.com/juriansluiman/SlmQueueSqs)
* Doctrine ORM: use [SlmQueueDoctrine](https://github.com/juriansluiman/SlmQueueDoctrine)

A job queue helps to offload long or memory-intensive processes from the HTTP requests clients sent to the Zend
Framework 2 application. This will make your response times shorter and your visitors happier. There are many use cases
for asynchronous jobs and a few examples are:

1. Send an email
2. Create a PDF file
3. Connect to a third party server over HTTP

In all cases you want to serve the response as soon as possible to your visitor, without letting them wait for this
long process. SlmQueue enables you to implement a job queue system very easily within your existing application.

Installation
------------

SlmQueue works with [Composer](http://getcomposer.org). Make sure you have the composer.phar downloaded and you have a
`composer.json` file at the root of your project. To install it, add the following line into your `composer.json` file:

```json
"require": {
    "slm/queue": "0.4.*"
}
```

After installation of the package, you need to complete the following steps to use SlmQueue:

 1. Enable the module by adding `SlmQueue` in your `application.config.php` file.
 2. Copy the `slm_queue.global.php.dist` (you can find this file in the `config` folder of SlmQueue) into
your `config/autoload` folder and apply any setting you want.

NB. SlmQueue is a skeleton and therefore useless by itself. Enable an adapter to give you the implementation details
you need to push jobs into the queue. Choose one of the available adapters
[SlmQueueBeanstalkd](https://github.com/juriansluiman/SlmQueueBeanstalkd),
[SlmQueueSqs](https://github.com/juriansluiman/SlmQueueSqs)
or [SlmQueueDoctrine](https://github.com/juriansluiman/SlmQueueDoctrine)

Requirements
------------
* [Zend Framework >= 2.2](https://github.com/zendframework/zf2)

Code samples
------------
Below are a few snippets which show the power of SlmQueue in your application. The full documentation is available in
[docs/](/docs) directory.

A sample job to send an email with php's `mail()` might look like this:

```php
namespace MyModule\Job;

use SlmQueue\Job\AbstractJob;

class EmailJob extends AbstractJob
{
    public function execute()
    {
        $payload = $this->getContent();

        $to      = $payload['to'];
        $subject = $payload['subject'];
        $message = $payload['message'];

        mail($to, $subject, $message);
    }
}
```

If you want to inject this job into a queue, you can do this for instance in your controller:

```php
namespace MyModule\Controller;

use MyModule\Job\Email as EmailJob;
use SlmQueue\Queue\QueueInterface;
use Zend\Mvc\Controller\AbstractActionController;

class MyController extends AbstractActionController
{
    protected $queue;

    public function __construct(QueueInterface $queue)
    {
        $this->queue = $queue;
    }

    public function fooAction()
    {
        // Do some work

        $job = new EmailJob;
        $job->setContent(array(
            'to'      => 'john@doe.com',
            'subject' => 'Just hi',
            'message' => 'Hi, I want to say hi!'
        ));

        $this->queue->push($job);
    }
}
```

Now the above code lets you insert jobs in a queue, but then you need to spin up a worker which can process these jobs.
Giving an example with beanstalkd and a queue which you called "default", you can start a worker with this command:

    php public/index.php queue beanstalkd default

Contributing
------------

SlmQueue is developed by various fanatic Zend Framework 2 users. The code is written to be as generic as possible for
Zend Framework 2 applications. If you want to contribute to SlmQueue, fork this repository and start hacking!

Any bugs can be reported as an [issue](https://github.com/juriansluiman/SlmQueue/issues) at GitHub. If you want to
contribute, please be aware of the following guidelines:

 1. Fork the project to your own repository
 2. Use branches to work on your own part
 3. Create a Pull Request at the canonical SlmQueue repository
 4. Make sure to cover changes with the right amount of unit tests
 5. If you add a new feature, please work on some documentation as well

For long-term contributors, push access to this repository is granted.
