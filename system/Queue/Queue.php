<?php

namespace AweBooking\System\Queue;

use Resque;
use AweBooking\Vendor\Illuminate\Contracts\Queue\Queue as QueueContract;

class Queue implements QueueContract
{
    /**
     * @var static
     */
    protected static $instance;

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * {@inheritdoc}
     */
    public function size($queue = null)
    {
        return Resque::size($queue ?? 'default');
    }

    /**
     * {@inheritdoc}
     */
    public function push($job, $data = [], $queue = null)
    {
        if (is_object($job)) {
            $job = get_class($job);
        }

        return Resque::enqueue($queue ?? 'default', $job, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function pushOn($queue, $job, $data = [])
    {
        return $this->push($job, $data, $queue);
    }

    /**
     * {@inheritdoc}
     */
    public function later($delay, $job, $data = [], $queue = null)
    {
        throw new \Exception('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function laterOn($queue, $delay, $job, $data = [])
    {
        return $this->later($delay, $job, $data, $queue);
    }

    /**
     * {@inheritdoc}
     */
    public function bulk($jobs, $data = [], $queue = null)
    {
        foreach ((array) $jobs as $job) {
            $this->push($job, $data, $queue);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function pop($queue = null)
    {
        return Resque::pop($queue ?? 'default');
    }

    /**
     * {@inheritdoc}
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectionName()
    {
        return 'resque';
    }

    /**
     * {@inheritdoc}
     */
    public function setConnectionName($name)
    {
        // TODO: Implement setConnectionName() method.
    }
}
