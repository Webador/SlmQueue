# 0.2.1

- Fix the default memory limit of the worker (from 1KB, which was obviously wrong, to 100MB)

# 0.2.0

- This version is a complete rewrite of SlmQueue. It is now splitted in several modules and support both
Beanstalkd and Amazon SQS queue systems through SlmQueueBeanstalkd and SlmQueueSqs modules.
