<?php
namespace AweBooking\Component\Form;

use Illuminate\Support\Arr;
use AweBooking\Support\Fluent;

class Values extends Fluent {
	/**
	 * {@inheritdoc}
	 */
	public function get( $key, $default = null ) {
		if ( false !== strpos( $key, '[' ) ) {
			$key = str_replace( [ '[]', '[', ']' ], [ '', '.', '' ], $key );

			return Arr::get( $this->attributes, $key, $default );
		}

		return parent::get( $key, $default );
	}
}
