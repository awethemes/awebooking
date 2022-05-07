<?php

namespace AweBooking\System\Queue;

class QueueJobDispatch
{
    /**
     * The job class name.
     *
     * @var class-string<Job>
     */
    public $job;

    /**
     * The job arguments.
     *
     * @var array<string,mixed>
     */
    public $args;

    /**
     * The number of seconds before the job should be made available.
     *
     * @var \DateTimeInterface|int|null
     */
    public $delay;

    /**
     * The name of the queue the job belongs to.
     *
     * @var string
     */
    public $queue;

    /**
     * Constructor.
     *
     * @param class-string<Job> $job
     * @param array $args
     */
    public function __construct(string $job, array $args)
    {
        $this->job = $job;
        $this->args = $args;
    }

    /**
     * Set the desired delay in seconds for the job.
     *
     * @param \DateTimeInterface|\DateInterval|int|null $delay
     * @return $this
     */
    public function delay($delay)
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * Set the desired queue for the job.
     *
     * @param string|null $queue
     * @return $this
     */
    public function onQueue($queue)
    {
        $this->queue = $queue;

        return $this;
    }
}
