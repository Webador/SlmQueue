<?php

namespace SlmQueue\Queue;

interface QueueAwareInterface
{
    public function getQueue(): QueueInterface;

    public function setQueue(QueueInterface $queue): void;
}
