<?php

namespace SlmQueue\Listener\Strategy;

use SlmQueue\Worker\WorkerEvent;
use Zend\EventManager\EventManagerInterface;

class FileWatchStrategy extends AbstractStrategy
{
    /**
     * @var string
     */
    protected $pattern = '/^\.\/(config|module).*\.(php|phtml)$/';

    /**
     * Watching these files
     *
     * @var array | null
     */
    protected $files;

    /**
     * @param string $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;

        $this->files   = null;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(WorkerEvent::EVENT_PROCESS_IDLE, array($this, 'onStopConditionCheck'), $priority);
        $this->listeners[] = $events->attach(WorkerEvent::EVENT_PROCESS_JOB_POST, array($this, 'onStopConditionCheck'), $priority);
    }

    public function onStopConditionCheck(WorkerEvent $event)
    {
        if (!$this->files) {
            $this->constructFileList();
        }

        foreach($this->files as $checksum=>$file) {
            if (!file_exists($file) || !is_readable($file) || $checksum != hash_file('crc32', $file)) {
                $event->stopPropagation();

                $this->exitState = sprintf("file modification detected for '%s'", $file);
            }
        }
    }

    protected function constructFileList()
    {
        $iterator   = new \RecursiveDirectoryIterator('.', \RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
        $files      = new \RecursiveIteratorIterator($iterator);

        /** @var $fileinfo \SplFileInfo  */
        foreach($files as $file) {
            if ($file->isDir()) {
                continue;
            }

            if (!preg_match($this->pattern, $file)) {
                continue;
            }

            $this->files[hash_file('crc32', $file)] = (string) $file;
        }
    }

}
