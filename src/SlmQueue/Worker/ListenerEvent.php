<?php

namespace SlmQueue\Worker;

/**
 * ListenerEvent
 */
class ListenerEvent extends WorkerEvent
{
    /**
     * Events reserved for internal use
     */
    const EVENT_PROCESS_PRE        = 'process.pre';
    const EVENT_PROCESS_POST       = 'process.post';
}
