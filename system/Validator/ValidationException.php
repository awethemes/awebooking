<?php

namespace AweBooking\System\Validator;

use Exception;

class ValidationException extends Exception
{
    /**
     * @var array
     */
    protected $errors;

    /**
     * @param array $errors
     * @param string|null $message
     */
    public function __construct(array $errors, $message = null)
    {
        parent::__construct($message ?: __('The given data was invalid', 'awepointment'));

        $this->errors = $errors;
    }

    /**
     * Get all the validation error messages.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
