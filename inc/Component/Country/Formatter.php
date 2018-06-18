<?php
namespace AweBooking\Component\Country;

class Formatter {
	/**
	 * Format the given address.
	 *
	 * @param  array $args An array of address.
	 * @return string
	 */
	public function format( $args = [] ) {
		$args = array_map( 'trim', wp_parse_args( $args, [
			'address_1'  => '',
			'address_2'  => '',
			'city'       => '',
			'state'      => '',
			'postcode'   => '',
			'country'    => '',
		]));

		// Get all formats.
		$formats = static::get_address_formats();

		// Get format for the address' country.
		$country = $args['country'];
		$format  = ( $country && isset( $formats[ $country ] ) ) ? $formats[ $country ] : $formats['default'];

		try {
			$country_name = ISO3166::get_instance()->find( $country )['name'];
		} catch ( \Exception $e ) {
			$country_name = $country;
		}

		// Substitute address parts into the string.
		$replaces = array_map( 'esc_html', apply_filters( 'abrs_formatted_address_replacements', [
			'{address_1}'        => $args['address_1'],
			'{address_2}'        => $args['address_2'],
			'{city}'             => $args['city'],
			'{state}'            => $args['state'],
			'{postcode}'         => $args['postcode'],
			'{country}'          => $country_name,
			'{address_1_upper}'  => strtoupper( $args['address_1'] ),
			'{address_2_upper}'  => strtoupper( $args['address_2'] ),
			'{city_upper}'       => strtoupper( $args['city'] ),
			'{state_upper}'      => strtoupper( $args['state'] ),
			'{state_code}'       => strtoupper( $args['state'] ),
			'{postcode_upper}'   => strtoupper( $args['postcode'] ),
			'{country_upper}'    => strtoupper( $country_name ),
		], $args ));

		$formatted_address = str_replace( array_keys( $replaces ), array_values( $replaces ), $format );

		// Clean up white space.
		$formatted_address = preg_replace( '/  +/', ' ', trim( $formatted_address ) );
		$formatted_address = preg_replace( '/\n\n+/', "\n", $formatted_address );

		// Break newlines apart and remove empty lines/trim commas and white space.
		$formatted_address = array_filter( array_map( [ $this, 'trim_formatted_address_line' ], explode( "\n", $formatted_address ) ) );

		// Add html breaks.
		$formatted_address = implode( '<br/>', $formatted_address );

		// We're done!
		return $formatted_address;
	}

	/**
	 * Trim white space and commas off a line.
	 *
	 * @param  string $line Line.
	 * @return string
	 */
	protected function trim_formatted_address_line( $line ) {
		return trim( $line, ', ' );
	}

	/**
	 * Get country address formats.
	 *
	 * These define how addresses are formatted for display in various countries.
	 *
	 * @return array
	 */
	public static function get_address_formats() {
		return apply_filters( 'abrs_localisation_address_formats', [
			'default' => "{address_1}\n{address_2}\n{city}\n{state}\n{postcode}\n{country}",
			'AU'      => "{address_1}\n{address_2}\n{city} {state} {postcode}\n{country}",
			'AT'      => "{address_1}\n{address_2}\n{postcode} {city}\n{country}",
			'BE'      => "{address_1}\n{address_2}\n{postcode} {city}\n{country}",
			'CA'      => "{address_1}\n{address_2}\n{city} {state} {postcode}\n{country}",
			'CH'      => "{address_1}\n{address_2}\n{postcode} {city}\n{country}",
			'CL'      => "{address_1}\n{address_2}\n{state}\n{postcode} {city}\n{country}",
			'CN'      => "{country} {postcode}\n{state}, {city}, {address_2}, {address_1}",
			'CZ'      => "{address_1}\n{address_2}\n{postcode} {city}\n{country}",
			'DE'      => "{address_1}\n{address_2}\n{postcode} {city}\n{country}",
			'EE'      => "{address_1}\n{address_2}\n{postcode} {city}\n{country}",
			'FI'      => "{address_1}\n{address_2}\n{postcode} {city}\n{country}",
			'DK'      => "{address_1}\n{address_2}\n{postcode} {city}\n{country}",
			'FR'      => "{address_1}\n{address_2}\n{postcode} {city_upper}\n{country}",
			'HK'      => "{address_1}\n{address_2}\n{city_upper}\n{state_upper}\n{country}",
			'HU'      => "{city}\n{address_1}\n{address_2}\n{postcode}\n{country}",
			'IN'      => "{address_1}\n{address_2}\n{city} - {postcode}\n{state}, {country}",
			'IS'      => "{address_1}\n{address_2}\n{postcode} {city}\n{country}",
			'IT'      => "{address_1}\n{address_2}\n{postcode}\n{city}\n{state_upper}\n{country}",
			'JP'      => "{postcode}\n{state} {city} {address_1}\n{address_2}\n{country}",
			'TW'      => "{address_1}\n{address_2}\n{state}, {city} {postcode}\n{country}",
			'LI'      => "{address_1}\n{address_2}\n{postcode} {city}\n{country}",
			'NL'      => "{address_1}\n{address_2}\n{postcode} {city}\n{country}",
			'NZ'      => "{address_1}\n{address_2}\n{city} {postcode}\n{country}",
			'NO'      => "{address_1}\n{address_2}\n{postcode} {city}\n{country}",
			'PL'      => "{address_1}\n{address_2}\n{postcode} {city}\n{country}",
			'PT'      => "{address_1}\n{address_2}\n{postcode} {city}\n{country}",
			'SK'      => "{address_1}\n{address_2}\n{postcode} {city}\n{country}",
			'SI'      => "{address_1}\n{address_2}\n{postcode} {city}\n{country}",
			'ES'      => "{address_1}\n{address_2}\n{postcode} {city}\n{state}\n{country}",
			'SE'      => "{address_1}\n{address_2}\n{postcode} {city}\n{country}",
			'TR'      => "{address_1}\n{address_2}\n{postcode} {city} {state}\n{country}",
			'US'      => "{address_1}\n{address_2}\n{city}, {state_code} {postcode}\n{country}",
			'VN'      => "{address_1}\n{city}\n{country}",
		]);
	}
}
