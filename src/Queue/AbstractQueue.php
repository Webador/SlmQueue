<?php

namespace SlmQueue\Queue;

use SlmQueue\Job\JobInterface;
use SlmQueue\Job\JobPluginManager;

abstract class AbstractQueue implements QueueInterface
{
    protected static $defaultWorkerName;
    protected string $name;

    protected JobPluginManager $jobPluginManager;

    public function __construct(
        string $name,
        JobPluginManager $jobPluginManager
    ) {
        $this->name = $name;
        $this->jobPluginManager = $jobPluginManager;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getWorkerName(): string
    {
        return static::$defaultWorkerName;
    }

    public function getJobPluginManager(): JobPluginManager
    {
        return $this->jobPluginManager;
    }

    /**
     * Create a job instance based on serialized input
     *
     * Instantiate a job based on a serialized data string. The string
     * is a JSON string containing job name, content and metadata. Use
     * the decoded JSON value to create a job instance, configure it
     * and return it.
     */
    public function unserializeJob($string, array $metadata = []): JobInterface
    {
        $data = json_decode($string, true);
        $name = $data['metadata']['__name__'];
        $metadata += $data['metadata'];
        $content = $data['content'];

        /** @var $job JobInterface */
        $job = $this->getJobPluginManager()->get($name);

        if ($job instanceof BinaryMessageInterface) {
            $content = base64_decode($content);
        }

        $content = unserialize($content);

        $job->setContent($content);
        $job->setMetadata($metadata);

        if ($job instanceof QueueAwareInterface) {
            $job->setQueue($this);
        }

        return $job;
    }

    /**
     * Serialize job to allow persistence
     *
     * The serialization format is a JSON object with keys "content",
     * "metadata" and "__name__". When a job is fetched from the SL, a job name
     * will be set and be available as metadata. An invokable job has no service
     * name and therefore the FQCN will be used.
     */
    public function serializeJob(JobInterface $job): string
    {
        $job->setMetadata('__name__', $job->getMetadata('__name__', get_class($job)));

        $data = [
            'content' => serialize($job->getContent()),
            'metadata' => $job->getMetadata(),
        ];

        if ($job instanceof BinaryMessageInterface) {
            $data['content'] = base64_encode($data['content']);
        }

        return json_encode($data);
    }
}
