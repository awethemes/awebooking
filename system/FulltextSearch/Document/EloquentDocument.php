<?php

namespace AweBooking\System\FulltextSearch\Document;

use AweBooking\System\Database\Model;

class EloquentDocument extends Document
{
    public function setup()
    {
        $model = $this->model;

        Model::observe(new ModelObserver($this));
    }
}
