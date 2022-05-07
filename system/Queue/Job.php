<?php

namespace AweBooking\System\Queue;

use AweBooking\System\Container;
use Resque_Job_DontPerform;
use Resque_JobInterface as JobInterface;

abstract class Job implements JobInterface
{
    /**
     * The jobs arguments.
     *
     * @var array
     */
    public $args;

    /**
     * The name of the queue the job belongs to.
     *
     * @var string
     */
    public $queue;

    /**
     * The current job instance.
     *
     * @var \Resque_Job
     */
    public $job;

    /**
     * Dispatch the job with the given arguments.
     *
     * @param mixed ...$arguments
     * @return QueueJobDispatch
     */
    public static function dispatch(...$arguments)
    {
        return new PendingJobDispatch(static::class, $arguments);
    }

    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * @param mixed ...$arguments
     * @return mixed
     */
    public static function dispatchSync(...$arguments)
    {
        return Container::getInstance()->get(Dispatcher::class)
            ->dispatchNow(static::class, $arguments);
    }

    /**
     * Job constructor without any arguments.
     *
     * @return void
     */
    final public function __construct()
    {
        if (method_exists($this, 'initialize')) {
            $this->initialize();
        }
    }

    /**
     * Perform the job.
     *
     * @return mixed|void
     */
    abstract public function perform();

    /**
     * Set up environment for this job
     *
     * @return void
     */
    public function setUp()
    {
        // ...
    }

    /**
     * Remove environment for this job
     *
     * @return void
     */
    public function tearDown()
    {
        // ...
    }

    /**
     * Abort execution of this job.
     *
     * @return void
     */
    public function abort(): void
    {
        throw new Resque_Job_DontPerform();
    }
}
