<?php
namespace AweBooking\Model\Exceptions;

class Model_Not_Found_Exception extends \RuntimeException {
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
		$this->model   = $model;
		$this->message = "No query results for model [{$model}]";

		return $this;
	}
}
