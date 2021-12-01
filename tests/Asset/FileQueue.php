<?php

namespace SlmQueueTest\Asset;

use SlmQueue\Job\JobInterface;
use SlmQueue\Job\JobPluginManager;
use SlmQueue\Queue\AbstractQueue;
use SlmQueue\Worker\WorkerPluginManager;

class FileQueue extends AbstractQueue
{
    protected static $defaultWorkerName = FileWorker::class;

    protected string $filename;
    protected array $jobs = [];

    public function __construct(string $filename, string $name, JobPluginManager $jobPluginManager, WorkerPluginManager $workerPluginManager)
    {
        parent::__construct($name, $jobPluginManager, $workerPluginManager);

        $this->filename = $filename;
        if (file_exists($filename)) {
            $this->jobs = unserialize(file_get_contents($filename));
        }
    }

    public function push(JobInterface $job, array $options = []): void
    {
        $this->jobs[] = $this->serializeJob($job);
        $this->persist();
    }

    public function pop(array $options = []): ?JobInterface
    {
        $payload = array_pop($this->jobs);
        if (! $payload) {
            return null;
        }

        $this->persist();

        return $this->unserializeJob($payload);
    }

    public function delete(JobInterface $job): void
    {
        foreach ($this->jobs as $key => $value) {
            if ($value->getId() === $job->getId()) {
                unset($this->jobs[$key]);
            }
        }

        $this->persist();
    }

    protected function persist(): void
    {
        file_put_contents($this->filename, serialize($this->jobs));
    }
}
