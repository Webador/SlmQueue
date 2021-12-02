# Ugrade to 3.0

This release adds support for PHP 8.0.

## BC BREAK: `laminas-cli` replaces `laminas-mvc-console`

The CLI can now be invoked with the following command:

```sh
vendor/bin/laminas slm-queue:start
```

## BC BREAK: Dropped support for PHP 7.3

SlmQueue now requires at least PHP 7.4.

## BC BREAK: Added `QueueInterface::getWorkerName()`

Classes implementing `QueueInterface` are now required to indicate which worker should be used to process the queue. This change will likely only affect you if you use a custom delegator for your queue.

## BC BREAK: Workers are now managed by `WorkerPluginManager`

Workers (and their aliases and delegators) should now be configured under `slm_queue.worker_manager` instead of `service_manager` (Laminas) or `dependencies` (Mezzio).
