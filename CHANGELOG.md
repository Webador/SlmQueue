# 0.4.0-beta3

- BC: to avoid name clashes, the internal `id` and `name` metadata have been renamed `__id__` and `__name__`,
respectively. As a consequence, existing jobs won't be able to be executed correctly. If you need to upgrade to SlmQueue 0.4,
you should create a new queue with the new version (and keeping the old one with old SlmQueue) until all the jobs on the
old system are finished.

# 0.4.0-beta2

- Segregate the `WorkerEvent::PROCESS` event into two different events for more granular control (`WorkerEvent::PROCESS_QUEUE`)
and (`WorkerEvent::PROCESS_JOB`).
- Show more precise memory consumption usage.
- Fix a bug when default queue listener was not attached in some circumstances

# 0.4.0-beta1

- Refactoring of dependency injection to use queue instead of queue name in worker
- Add job status codes so listeners can act on the result of a job's outcome
- Add controller plugin to ease push of jobs into queues
- BC: job's jsonSerialize() removed in favour of the queue's serializeJob() method
- BC: job's metadata field "name" is now reserved for SlmQueue and should not be used by end users

# 0.3.0

- BC: raised dependency to ZF 2.2
- BC: composer package has been changed from "juriansluiman/slm-queue" to "slm/queue". Remember to update
your `composer.json` file!
- BC: AbstractJob constructor is now gone. It simplifies injecting dependencies as you do not need to remember
to call parent constructor.
- BC: keys for configuring queues was previously "queues", it is now "queue_manager". The key "queues" is still used
but it's now for specifying options for a specific queue.
- BC: remove Version class
- BC: ProvidesQueue trait has been renamed to QueueAwareTrait, to provide PSR compliance
- Job metadata is now serialized
- You can make your jobs implement the interface 'SlmQueue\Queue\QueueAwareInterface'. Therefore, you will have
access to the queue in the `execute` method of the job.
- All exceptions now implement `SlmQueue\Exception\ExceptionInterface`, so you can easily filter exceptions.

# 0.2.5

- Change the visibility of the handleSignal function in the worker as it caused a problem
- Fix a bug that may occur on Windows machines

# 0.2.4

- Add support for signals to stop worker properly

# 0.2.3

- Fix compatibilities problems with PHP 5.3

# 0.2.2

- Fix compatibilities problems with PHP 5.3

# 0.2.1

- Fix the default memory limit of the worker (from 1KB, which was obviously wrong, to 100MB)

# 0.2.0

- This version is a complete rewrite of SlmQueue. It is now splitted in several modules and support both
Beanstalkd and Amazon SQS queue systems through SlmQueueBeanstalkd and SlmQueueSqs modules.
