<?php

namespace AweBooking\System\Workflow;

use AweBooking\Vendor\Symfony\Component\Workflow\Marking;
use AweBooking\Vendor\Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;

class EloquentMarkingStore implements MarkingStoreInterface
{
    /**
     * @var bool
     */
    private $singleState;

    /**
     * @var string
     */
    private $property;

    /**
     * @param bool $singleState Used to determine Single/Multi place marking
     * @param string $property Used to determine methods to call
     */
    public function __construct(bool $singleState = false, string $property = 'marking')
    {
        $this->singleState = $singleState;
        $this->property    = $property;
    }

    /**
     * {@inheritdoc}
     */
    public function getMarking(object $subject): Marking
    {
        $marking = $subject->{$this->property};

        if (null === $marking) {
            return new Marking();
        }

        if ($this->singleState) {
            $marking = [(string) $marking => 1];
        }

        return new Marking($marking);
    }

    /**
     * {@inheritdoc}
     */
    public function setMarking(object $subject, Marking $marking, array $context = [])
    {
        $marking = $marking->getPlaces();

        if ($this->singleState) {
            $marking = key($marking);
        }

        // We'll check for the mutator first, and use that with the context.
        $method = 'set' . ucfirst($this->property) . 'Attribute';
        if (method_exists($subject, $method)) {
            $subject->{$method}($marking, $context);

            return;
        }

        $subject->{$this->property} = $marking;
    }
}
