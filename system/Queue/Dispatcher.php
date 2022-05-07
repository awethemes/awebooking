<?php

namespace AweBooking\System\Queue;

class Dispatcher
{
    /**
     * Dispatch a job.
     *
     * @param class-string<Job>|QueueJobDispatch $job
     * @return mixed
     */
    public function dispatch($job)
    {
        return $job instanceof QueueJobDispatch
            ? $this->dispatchToQueue($job)
            : $this->dispatchSync($job);
    }

    /**
     * @param class-string<Job> $job
     * @return mixed
     * @throws \Exception
     */
    public function dispatchSync($job)
    {
        throw new \Exception('Not implemented');
    }

    /**
     * @param QueueJobDispatch $command
     * @return mixed
     */
    public function dispatchToQueue(QueueJobDispatch $command)
    {
        $queue = $this->resolveQueue();

        if (isset($command->queue, $command->delay)) {
            return $queue->laterOn(
                $command->queue,
                $command->delay,
                $command->job,
                $command->args
            );
        }

        if (isset($command->delay)) {
            return $queue->later($command->delay, $command->job, $command->args);
        }

        if (isset($command->queue)) {
            return $queue->pushOn($command->queue, $command->job, $command->args);
        }

        return $queue->push($command->job, $command->args);
    }

    /**
     * @return Queue
     */
    protected function resolveQueue()
    {
        return Queue::getInstance();
    }
}
