<?php
namespace AweBooking\Support;

use Carbon\Carbon;

abstract class Abstract_Calendar {
	/**
	 * Carbon instance of today.
	 *
	 * @var Carbon
	 */
	protected $today;

	/**
	 * Week begins, 0 stands for Sunday.
	 *
	 * @var int
	 */
	protected $week_begins;

	/**
	 * Calendar data.
	 *
	 * @var mixed
	 */
	protected $data;

	/**
	 * The Calendar options.
	 *
	 * @var array
	 */
	protected $options = [];

	/**
	 * The Calendar default options.
	 *
	 * @var array
	 */
	protected $defaults = [
		'date_title'       => 'l, M j, Y',
		'month_label'      => 'abbrev',  // 'abbrev', 'full'.
		'weekday_label'    => 'abbrev',  // 'initial', 'abbrev', 'full'.
		'base_class'       => 'awebookingcal',

		// For year-calendar only.
		'hide_prev_months' => true,
	];

	/**
	 * Create the Calendar.
	 *
	 * @param array $options The calendar options.
	 */
	public function __construct( array $options = [] ) {
		$this->today = Carbon::today();

		$this->setup_options( $options );

		$this->week_begins = (int) get_option( 'start_of_week' );
	}

	/**
	 * Display the Calendar.
	 *
	 * @return void
	 */
	abstract public function display();

	/**
	 * Prepare setup the data.
	 *
	 * @return mixed
	 */
	abstract protected function prepare_data();

	/**
	 * Setup date data before prints.
	 *
	 * @param  Carbon $date Current day instance.
	 * @return void
	 */
	abstract protected function setup_date( Carbon $date );

	/**
	 * Generate HTML Calendar in a year.
	 *
	 * @param  int $year Year to generate.
	 * @return string
	 */
	protected function generate_year_calendar( $year ) {
		$year = ( $year instanceof Carbon ) ? $year->year : $year;

		$output  = '<table class="' . esc_attr( $this->get_html_class( '& &--month' ) ) . '">';
		$output .= "\n<thead>\n\t<tr>";

		$year_heading = $year;
		if ( method_exists( $this, 'custom_year_heading' ) ) {
			$year_heading = $this->custom_year_heading( $year );
		}

		$output .= "\n\t\t" . '<th class="' . esc_attr( $this->get_html_class( '&__year-heading' ) ) . '">' . $year_heading . '</th>';
		for ( $i = 1; $i <= 31; $i++ ) {
			$output .= "\n\t\t" . '<th class="' . esc_attr( $this->get_html_class( '&__day-heading' ) ) . '" data-day="' . esc_attr( $i ) . '">' . esc_html( $i ) . '</th>';
		}

		$output .= "\n\t</tr>\n</thead>";
		$output .= "\n<tbody>";

		for ( $m = 1; $m <= 12; $m++ ) {
			$month = Carbon::createFromDate( $year, $m, 1 );

			// Don't show previous months if necessary.
			if ( $this->get_option( 'hide_prev_months' ) &&
				$month->year === $this->today->year && $month->month < $this->today->month ) {
				continue;
			}

			$output .= "\n\t<tr>";
			$output .= "\n\t\t" . '<th class="' . esc_attr( $this->get_html_class( '&__month-heading' ) ) . '" data-month="' . esc_attr( $m ) . '">' . esc_html( $this->get_month_label( $m ) ) . '</th>';

			for ( $d = 1; $d <= 31; $d++ ) {
				// @codingStandardsIgnoreLine
				if ( $d > $month->daysInMonth ) {
					$output .= "\n\t\t" . $this->generate_cell_pad( 1, false );
					continue;
				}

				$day = $month->copy()->day( $d );
				$this->setup_date( $day );

				$output .= "\n\t\t" . $this->generate_cell_date( $day, 'year' );
			}

			$output .= "\n\t</tr>\n";
		} // End for().

		$output .= "\n</tbody>\n</table>";

		return $output;
	}

	/**
	 * Generate HTML Calendar in a month.
	 *
	 * @param  Carbon $month Month to generate.
	 * @return string
	 */
	protected function generate_month_calendar( Carbon $month ) {
		$month->startOfMonth();

		$output  = '<table class="' . esc_attr( $this->get_html_class( '& &--month' ) ) . '">';
		$output .= "\n<thead>\n\t<tr>";

		for ( $i = 0; $i <= 6; $i++ ) {
			$wd = (int) ( $i + $this->week_begins ) % 7;
			$wd_class = ( Carbon::SUNDAY == $wd || Carbon::SATURDAY == $wd ) ? '&--weekend' : '&--weekday';

			$output .= "\n\t\t" . '<th class="' . esc_attr( $this->get_html_class( $wd_class ) ) . '">' . esc_html( $this->get_weekday_label( $wd ) ) . '</th>';
		}

		$output .= "\n\t</tr>\n</thead>";
		$output .= "\n<tbody>\n\t<tr>";

		// See how much we should pad in the beginning.
		$pad = calendar_week_mod( $month->dayOfWeek - $this->week_begins ); // @codingStandardsIgnoreLine
		if ( 0 != $pad ) {
			$output .= "\n\t\t" . $this->generate_cell_pad( $pad );
		}

		$newrow = false;
		for ( $d = 1; $d <= $month->daysInMonth; ++$d ) { // @codingStandardsIgnoreLine
			if ( true === $newrow ) {
				$output .= "\n\t</tr>\n\t<tr>";
			}

			$newrow = false;
			$day = $month->copy()->day( $d );

			$this->setup_date( $day );
			$output .= "\n\t\t" . $this->generate_cell_date( $day, 'month' );

			// @codingStandardsIgnoreLine
			if ( 6 == calendar_week_mod( $day->dayOfWeek - $this->week_begins ) ) {
				$newrow = true;
			}
		}

		// Pad in the ending.
		$pad = ( 7 - calendar_week_mod( $day->addDay()->dayOfWeek - $this->week_begins ) );
		if ( 0 != $pad && 7 != $pad ) {
			$output .= "\n\t\t" . $this->generate_cell_pad( $pad );
		}

		$output .= "\n\t</tr>";
		$output .= "\n</tbody>\n</table>";

		return $output;
	}

	/**
	 * Generate HTML cell of a day.
	 *
	 * @param  Carbon $date    Current day instance.
	 * @param  string $context Optional, context from Calendar.
	 * @return string
	 */
	protected function generate_cell_date( Carbon $date, $context = '' ) {
		return sprintf( '<td class="%6$s" data-day="%1$s" data-month="%2$s" data-year="%3$s" data-date="%4$s" title="%5$s">' . $this->get_date_contents( $date, $context ) . '</td>',
			esc_attr( $date->day ),
			esc_attr( $date->month ),
			esc_attr( $date->year ),
			esc_attr( $date->toDateString() ),
			esc_attr( $date->format( $this->get_option( 'date_title' ) ) ),
			esc_attr( implode( ' ', $this->get_date_classes( $date ) ) )
		);
	}

	/**
	 * Generate cell padding.
	 *
	 * @param  integer $pad     How how much we should pad.
	 * @param  boolean $colspan Using colspan attribute.
	 * @return string
	 */
	protected function generate_cell_pad( $pad = 1, $colspan = true ) {
		$colspan = ( $colspan && $pad > 1 ) ? ' colspan="' . esc_attr( $pad ) . '"' : '';

		$padding = '<td' . $colspan . ' class="' . $this->get_html_class( '&__pad' ) . '">&nbsp;</td>';

		return $colspan ? $padding : str_repeat( $padding, $pad );
	}

	/**
	 * Return contents of day in cell.
	 *
	 * Override this method if want custom contents.
	 *
	 * @param  Carbon $date    Current day instance.
	 * @param  string $context Optional, context from Calendar.
	 * @return array
	 */
	protected function get_date_contents( Carbon $date, $context ) {
		return '<span class="' . esc_attr( $this->get_html_class( '&__state' ) ) . '">' . ( 'month' === $context ? '%1$s' : '' ) . '</span>';
	}

	/**
	 * Get classess for date.
	 *
	 * @param  Carbon $date Date instance.
	 * @return array
	 */
	protected function get_date_classes( Carbon $date ) {
		$classes[] = $this->get_html_class( '&__day' );

		// Is current day is today, future or past.
		if ( $date->isToday() ) {
			$classes[] = $this->get_html_class( '&__day--today' );
		} elseif ( $date->lt( $this->today ) ) {
			$classes[] = $this->get_html_class( '&__day--past' );
		} elseif ( $date->gt( $this->today ) ) {
			$classes[] = $this->get_html_class( '&__day--future' );
		}

		if ( $date->isWeekend() ) {
			$classes[] = $this->get_html_class( '&__day--weekend' );
		}

		return $classes;
	}

	/**
	 * Get html base class or build new class.
	 *
	 * Uses "&" to represent to "base_class" like SCSS, eg: &__heading.
	 *
	 * @param  string $class Optional, extra classes.
	 * @return string
	 */
	protected function get_html_class( $class = null ) {
		$base_class = $this->get_option( 'base_class' );

		if ( is_null( $class ) ) {
			return $base_class;
		}

		return str_replace( '&', $base_class, $class );
	}

	/**
	 * Retrieve month label by month number depend "month_label" option.
	 *
	 * @param  string|int $month Month number from '01' through '12'.
	 * @return string
	 */
	protected function get_month_label( $month ) {
		global $wp_locale;

		$month_name = $wp_locale->get_month( $month );

		if ( 'abbrev' === $this->get_option( 'month_label' ) ) {
			return $wp_locale->get_month_abbrev( $month_name );
		}

		return $month_name;
	}

	/**
	 * Retrieve weekday label depend "month_label" option.
	 *
	 * @param  int $weekday Weekday number, 0 for Sunday through 6 Saturday.
	 * @return string
	 */
	protected function get_weekday_label( $weekday ) {
		global $wp_locale;

		$weekday_name = $wp_locale->get_weekday( $weekday );

		switch ( $this->get_option( 'weekday_label' ) ) {
			case 'initial':
				return $wp_locale->get_weekday_initial( $weekday_name );
			case 'abbrev':
				return $wp_locale->get_weekday_abbrev( $weekday_name );
			default:
				return $weekday_name;
		}
	}

	/**
	 * Get the Calendar option.
	 *
	 * @param  string $option  Option key name.
	 * @param  mixed  $default Default value.
	 * @return mixed
	 */
	public function get_option( $option, $default = null ) {
		return isset( $this->options[ $option ] ) ? $this->options[ $option ] : $default;
	}

	/**
	 * Set the Calendar option.
	 *
	 * @param  string $option Option key name.
	 * @param  mixed  $value  Option value.
	 * @return $this
	 */
	public function set_option( $option, $value ) {
		$this->options[ $option ] = $value;

		return $this;
	}

	/**
	 * If the Calendar has a option.
	 *
	 * @param  string $option Option key name.
	 * @return boolean
	 */
	public function has_option( $option ) {
		return isset( $this->options[ $option ] );
	}

	/**
	 * Return the Calendar options.
	 *
	 * @return array
	 */
	public function get_options() {
		return $this->options;
	}

	/**
	 * Setup the options.
	 *
	 * @param  array $options Calendar options.
	 * @return void
	 */
	protected function setup_options( array $options ) {
		$this->options = wp_parse_args( $options, $this->defaults );
	}
}
