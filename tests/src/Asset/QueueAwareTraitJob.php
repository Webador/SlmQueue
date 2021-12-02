<?php

namespace SlmQueueTest\Asset;

use SlmQueue\Queue\QueueAwareInterface;
use SlmQueue\Queue\QueueAwareTrait;

class QueueAwareTraitJob extends SimpleJob implements QueueAwareInterface
{
    use QueueAwareTrait;
}
