<?php

namespace SlmQueueTest\Asset;

use SlmQueue\Job\AbstractJob;

class JobWithDependencies extends AbstractJob
{
    public static function testInternalFactoryMethod()
    {
        return self::createEmptyJob(['a' => 123]);
    }

    private $someDependency;

    public function __construct($someDependency)
    {
        $this->someDependency = $someDependency;
    }

    public function execute(): ?int
    {
        // Just set some stupid metadata
        $this->setMetadata('foo', 'bar');

        return 999;
    }
}
