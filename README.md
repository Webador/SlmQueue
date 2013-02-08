SlmQueue
========
Version 1.0.0 Created by Jurian Sluiman

> Please note that this is a complete rewrite of SlmQueue. The previous version was tagged as version 0.5.0, so if you still
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
	"juriansluiman/slm-queue": "1.*"
}
```

Then, enable the module by adding `SlmQueue` in your `application.config.php` file. You may also want to configure
the module: just copy the `slm_queue.local.php.dist` (you can find this file in the `config` folder of SlmQueue) into
your `config/autoload` folder, and override what you want.

> SlmQueue is pretty useless by itself, as it is mainly interfaces and abstract classes. To make it really powerful,
you'll likely add [SlmQueueBeanstalkd](https://github.com/juriansluiman/SlmQueueBeanstalkd) and/or [SlmQueueSqs](https://github.com/juriansluiman/SlmQueueSqs).
