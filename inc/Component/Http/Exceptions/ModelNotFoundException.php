<?php
namespace AweBooking\Component\Http\Exceptions;

class ModelNotFoundException extends \RuntimeException {
	/**
	 * Name of the affected model.
	 *
	 * @var string
	 */
	protected $model;

	/**
	 * Set the affected model.
	 *
	 * @param  string $model The model.
	 * @return $this
	 */
	public function set_model( $model ) {
		$this->model = $model;

		$this->message = "No query results for model [{$model}]";

		return $this;
	}
}
