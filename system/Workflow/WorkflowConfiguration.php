<?php

namespace AweBooking\System\Workflow;

use AweBooking\System\Bridge\SymfonyEventDispatcher;
use AweBooking\Vendor\Symfony\Component\Workflow\DefinitionBuilder;
use AweBooking\Vendor\Symfony\Component\Workflow\Transition;
use AweBooking\Vendor\Symfony\Component\Workflow\Workflow;

class WorkflowConfiguration extends DefinitionBuilder
{
    /**
     * @var string
     */
    public $stateProperty = 'status';

    /**
     * @var bool
     */
    public $isSingleState = true;

    /**
     * @var SymfonyEventDispatcher
     */
    private $dispatcher;

    public function __construct(SymfonyEventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function stateProperty(string $stateProperty)
    {
        $this->stateProperty = $stateProperty;

        return $this;
    }

    public function isSingleState(bool $isSingleState)
    {
        $this->isSingleState = $isSingleState;

        return $this;
    }

    public function defineTransition(string $name, $froms, $tos)
    {
        foreach ((array) $froms as $from) {
            $this->addTransition(new Transition($name, $from, $tos));
        }

        return $this;
    }

    public function createWorkflow($name)
    {
        return new Workflow(
            $this->build(),
            new EloquentMarkingStore($this->isSingleState, $this->stateProperty),
            $this->dispatcher,
            $name
        );
    }
}
