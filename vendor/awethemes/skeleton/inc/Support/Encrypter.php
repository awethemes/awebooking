<?php
namespace Skeleton\Support;

class Encrypter {
	/**
	 * Encrypt the given value.
	 *
	 * @param  string $value Encrypt value.
	 * @return string
	 */
	public static function encrypt( $value ) {
		return rtrim( strtr( base64_encode( addslashes( gzcompress( serialize( $value ), 9 ) ) ), '+/', '-_' ), '=' );
	}

	/**
	 * Decrypt the given value.
	 *
	 * @param  string $payload Payload to decrypt.
	 * @return string
	 */
	public static function decrypt( $payload ) {
		return unserialize( gzuncompress( stripslashes( base64_decode( rtrim( strtr( $payload, '-_', '+/' ), '=' ) ) ) ) );
	}
}
