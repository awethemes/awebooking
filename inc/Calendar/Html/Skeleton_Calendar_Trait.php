<?php
namespace AweBooking\Calendar\Html;

use AweBooking\Support\Carbonate;

trait Skeleton_Calendar_Trait {
	/**
	 * Get the Calendar option.
	 *
	 * @param  string $option  Option key name.
	 * @param  mixed  $default Default value.
	 * @return mixed
	 */
	protected function get_option( $option, $default = null ) {
		return isset( $this->options[ $option ] ) ? $this->options[ $option ] : $default;
	}

	/**
	 * Get html base class or build new class.
	 *
	 * Uses "&" to represent to "base_class" like SCSS, eg: &__heading.
	 *
	 * @param  string $class Optional, extra classes.
	 * @return string
	 */
	protected function html_class( $class = null ) {
		$base_class = $this->get_option( 'base_class' );

		if ( is_null( $class ) ) {
			return $base_class;
		}

		return str_replace( '&', $base_class, $class );
	}

	/**
	 * Get classess for date.
	 *
	 * @param  Carbonate $date Date instance.
	 * @return array
	 */
	protected function get_date_classes( Carbonate $date ) {
		$classes[] = $this->html_class( '&__day' );

		// Is current day is today, future or past.
		if ( $date->isToday() ) {
			$classes[] = $this->html_class( '&__day--today' );
		} elseif ( $date->isPast() ) {
			$classes[] = $this->html_class( '&__day--past' );
		} elseif ( $date->isFuture() ) {
			$classes[] = $this->html_class( '&__day--future' );
		}

		if ( $date->isWeekend() ) {
			$classes[] = $this->html_class( '&__day--weekend' );
		}

		return $classes;
	}

	/**
	 * Retrieve month label by month number depend "month_label" option.
	 *
	 * @param  string|int $month Month number from '01' through '12'.
	 * @param  string     $type  Optional, if null given using value from "month_label" option.
	 * @return string
	 */
	protected function get_month_name( $month, $type = null ) {
		global $wp_locale;

		$type = is_null( $type ) ? $this->get_option( 'month_label' ) : $type;
		$month_name = $wp_locale->get_month( $month );

		if ( 'abbrev' === $type ) {
			return $wp_locale->get_month_abbrev( $month_name );
		}

		return $month_name;
	}

	/**
	 * Retrieve weekday label depend "month_label" option.
	 *
	 * @param  int    $weekday Weekday number, 0 for Sunday through 6 Saturday.
	 * @param  string $type    Optional, if null given using value from "weekday_label" option.
	 * @return string
	 */
	protected function get_weekday_name( $weekday, $type = null ) {
		global $wp_locale;

		$type = is_null( $type ) ? $this->get_option( 'weekday_label' ) : $type;
		$weekday_name = $wp_locale->get_weekday( $weekday );

		switch ( $type ) {
			case 'initial':
				return $wp_locale->get_weekday_initial( $weekday_name );
			case 'abbrev':
				return $wp_locale->get_weekday_abbrev( $weekday_name );
			default:
				return $weekday_name;
		}
	}
}
