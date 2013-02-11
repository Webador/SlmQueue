<?php

namespace SlmQueue\Job;

use Zend\Stdlib\SplPriorityQueue;
use SlmQueue\Task\TaskInterface;

/**
 * A taskable job is a job that execute a specific set of tasks. The tasks are stored using a priority queue,
 * so that the user can specified in which order the tasks need to be executed
 */
class TaskableJob extends AbstractJob
{
    /**
     * @var SplPriorityQueue
     */
    protected $tasks;


    /**
     * {@inheritDoc}
     */
    public function __construct($content = null, array $metadata = array())
    {
        $this->tasks = new SplPriorityQueue();
        parent::__construct($content, $metadata);
    }

    /**
     * Insert a new task into the job
     *
     * @param TaskInterface $task
     * @param mixed         $priority
     */
    public function addTask(TaskInterface $task, $priority)
    {
        $this->tasks->insert($task, $priority);
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        /** @var $task TaskInterface */
        foreach ($this->tasks as $task) {
            $task->execute($this);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        $this->tasks->setExtractFlags(\SplPriorityQueue::EXTR_BOTH);

        $data = array(
            'class'   => get_called_class(),
            'content' => $this->getContent(),
            'tasks'   => array_map(function($task) {
                return array(
                    'priority' => $task['priority'],
                    'class'    => get_class($task['priority'])
                );
            }, iterator_to_array($this->tasks))
        );

        return json_encode($data);
    }
}
