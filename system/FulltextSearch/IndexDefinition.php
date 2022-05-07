<?php

namespace AweBooking\System\FulltextSearch;

use AweBooking\Vendor\Illuminate\Database\Eloquent\SoftDeletes;
use AweBooking\System\Database\Model;

use function AweBooking\System\class_uses_recursive;

class IndexDefinition
{
    /**
     * @var string
     */
    public string $name;

    /**
     * @var class-string
     */
    public string $model;

    /*
     * @var string
     */
    protected string $modelKeyName;

    /**
     * @param string $name
     * @param string $model
     */
    public function __construct(string $name, string $model)
    {
        $this->name = $name;
        $this->model = $model;
    }

    /**
     * @return string
     */
    public function getIndexName(): string
    {
        return $this->name;
    }

    /**
     * @param string $modelKeyName
     * @return $this
     */
    public function modelKeyName(string $modelKeyName)
    {
        $this->modelKeyName = $modelKeyName;

        return $this;
    }

    /**
     * @return string
     */
    public function getModelKeyName(): string
    {
        if ($this->modelKeyName) {
            return $this->modelKeyName;
        }

        if (is_subclass_of($this->model, Model::class)) {
            return $this->modelKeyName = (new $this->model)->getKeyName();
        }

        return $this->modelKeyName = 'id';
    }

    /**
     * @param mixed $model
     * @return int|string
     */
    public function getModelKey($model)
    {
        if ($model instanceof Model) {
            return $model->getAttribute($this->getModelKeyName());
        }

        return $model->{$this->getModelKeyName()};
    }

    /**
     * Determine if the given model uses soft deletes.
     *
     * @param mixed $model
     * @return bool
     */
    public function usesSoftDelete($model)
    {
        if (!$model instanceof Model) {
            return false;
        }

        return in_array(SoftDeletes::class, class_uses_recursive($model), true);
    }
}
