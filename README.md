SlmQueue
===
Version 0.1.0 Created by Jurian Sluiman

Introduction
---
SlmQueue is a Zend Framework 2 module that integrates Beanstalk ("beanstalkd") as a job queue. SlmQueue helps to offload long or memory-intensive processes from the HTTP requests users sent to the Zend Framework 2 application. There are many use cases for asynchronous jobs and the most common will be:

1. Send an email
2. Create a PDF file
3. Connect to a third party server

In all cases you want to serve the response as soon as possible to your visitor, without letting them wait for this long process. With SlmQueue you are able to do this, with some other neat features.

Features
---
SlmQueue provides all features Beanstalk has. Most note worthy are:

### Multiple tubes
A "tube" can be set in your application. If you run multiple ZF2 applications on one server, you can run one beanstalkd process. The tubes are job queue lanes, where every application can use its own tube. For small servers this ease the use of async jobs a lot.

### Delayed jobs
If you do not want to execute a job immediately, you can put a delay in front of it. The delay is in seconds and is set when you put the job in the queue. If you want to process some data in the near future, delays are a good choice for doing that.

### Release jobs
If a worker has executed a job, but an exception occurred (for exampe, a timeout on the 3rd party service) you want to retry the job. Together with the delay (you can release a job with a delay) this helps you to try a job a couple of times before you give it up.

### Bury jobs
The buried jobs are a list of jobs that failed but are not gone yet. You can use the bury list 


Requirements
---
* [Zend Framework 2](https://github.com/zendframework/zf2)
* [Beanstalk](http://kr.github.com/beanstalkd/)

Installation
---
SlmQueue works with composer. Require "slm/queue" and enable it in your `application.config.php`.

Usage
---
TBD

A small usage example:

```php
namespace MyModule\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class MyController extends AbstactActionController
{
    public function contactAction()
    {
        // A contact form has been sent

        $service = $this->getServiceLocator()->get('SlmQueue\Service\PheanstalkBridge');
        $service->put('MyModule\Job\SendEmail', array(
            'address' => $emailAddress,
            'name'    => $name,
            'message' => $message,
        ));

        // Return view model
    }
}
```

A job might look like this then:

```php
namespace MyModule\Job;

use SlmQueue\Job\JobInterface;
use Zend\Mail;

class SendEmail implements JobInterface
{
  protected $options;
  public function setOptions(array $options)
  {
    $this->options = $options;
  }

  public function getOptions()
  {
    return $this->options;
  }

  public function __invoke()
  {
    $options = $this->getOptions();

    $message = new Mail\Message;
    $message->addTo($options['address'], $options['name']);
    $message->setSubject('Hello there');
    $message->setBody($options['message']);

    $transport = new Mail\Transport\Sendmail;
    $transport->send($message);
  }
}
```

Development
---
The module is in heavy development and it is not recommended to use it in production. There are no tests and no guarantee that everything works as expected. If you have questions, feel free to get in touch via jurian@soflomo.com.
