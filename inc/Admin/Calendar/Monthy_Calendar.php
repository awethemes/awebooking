<?php
namespace AweBooking\Admin\Calendar;

use AweBooking\AweBooking;
use AweBooking\Factory;
use AweBooking\Hotel\Room;
use AweBooking\Hotel\Room_Type;
use AweBooking\Booking\Calendar;
use AweBooking\Support\Period;
use AweBooking\Support\Carbonate;
use Illuminate\Support\Arr;
use AweBooking\Support\Collection;
use AweBooking\Support\Abstract_Calendar;

class Monthy_Calendar extends Abstract_Calendar {
	/**
	 * The room-type instance.
	 *
	 * @var AweBooking\Hotel\Room_Type
	 */
	protected $room_type;

	/**
	 * An collection rooms of room-type.
	 *
	 * @var Collection
	 */
	protected $rooms;

	protected $room;

	/**
	 * The year we will working on.
	 *
	 * @var int
	 */
	protected $year;

	/**
	 * The month we will working on.
	 *
	 * @var int
	 */
	protected $month;

	/**
	 * The Calendar default options.
	 *
	 * @var array
	 */
	protected $defaults = [
		'date_title'       => 'l, M j, Y',
		'month_label'      => 'abbrev',  // 'abbrev', 'full'.
		'weekday_label'    => 'abbrev',  // 'initial', 'abbrev', 'full'.
		'base_class'       => 'abkngcal',
		'hide_prev_months' => true,
	];

	/**
	 * Create pricing calendar.
	 *
	 * @param Room_Type $room_type The room-type instance.
	 * @param int       $year      Year of calendar.
	 */
	public function __construct( Room_Type $room_type, $year = null, $month = null ) {
		parent::__construct();

		$this->room_type = $room_type;
		$rooms = new Collection( $this->room_type->get_rooms() );
		$this->rooms = $rooms;

		$this->year = $year ? absint( $year ) : absint( date( 'Y' ) );
		$this->month = ( $month && $month <= 12 && $month >= 1 ) ? $month : absint( date( 'n' ) );
	}

	/**
	 * Prepare setup the data.
	 *
	 * @param  mixed  $data    Mixed input data.
	 * @param  string $context Context from Calendar.
	 * @return mixed
	 */
	protected function prepare_data( $data, $context ) {
		if ( 'year' === $context && is_int( $data ) ) {
			$start_date = Carbonate::createFromDate( $data, 1, 1 );
			$end_date   = $start_date->copy()->endOfYear();
		} elseif ( $data instanceof Carbonate ) {
			$start_date = Carbonate::create_date( $data )->startOfMonth();
			$end_date   = $start_date->copy()->endOfMonth();
		} else {
			return;
		}
		$response = Factory::create_availability_calendar( $this->rooms->all() )
			->getEventsItemized( $start_date, $end_date, Calendar::BAT_DAILY );

		$resources = array_map(function( $item ) {
			return $item[ Calendar::BAT_DAY ];
		}, $response );

		return $resources;
	}

	/**
	 * Return contents of day in cell.
	 *
	 * Override this method if want custom contents.
	 *
	 * @param  Carbonate $date    Current day instance.
	 * @param  string    $context Context from Calendar.
	 * @return array
	 */
	protected function get_date_contents( Carbonate $date, $context ) {

		$getdata = $this->room->get_id() . '.' . $date->format( 'Y.n.\dj' );

		if ( Arr::has( $this->data, $getdata ) ) {
			$contents = '<i></i>
			<span class="abkngcal__day-state"></span>
			<span class="abkngcal__day-selection"></span>';

		}

		return $contents;
	}

	/**
	 * Display the Calendar.
	 *
	 * @return void
	 */
	public function display() {
		$date = Carbonate::createFromDate( $this->year, $this->month, 1 );

		echo '<div class="abkngcal-container abkngcal--availability-calendar">
				<div class="abkngcal-ajax-loading" style="display: none;"><div class="spinner"></div></div>';

		// @codingStandardsIgnoreStart
		echo '<h2>' . esc_html( $this->room_type->get_title() ) . '</h2>';
		$units = $this->rooms->map->only( 'id', 'name' );

		echo $this->generate_scheduler_calendar( $date, $units );
		// @codingStandardsIgnoreEnd

		echo '</div>';
		?>
			<style>
				.abkngcal--availability-calendar .abkngcal__month-heading>span {
				    width: 125px;
				    font-size: 11px;
				    text-align: left;
				    padding: 0 5px;
				    white-space: nowrap;
				    overflow: hidden;
				    text-overflow: ellipsis;
				}

				.abkngcal--availability-calendar .abkngcal__day-heading:not(.hover) {
				    background-color: #fff;
				}

				.abkngcal--availability-calendar td {
				    vertical-align: top;
				}
			</style>
		<?php
	}

	/**
	 * Setup date data before prints.
	 *
	 * @param  Carbonate $date    Date instance.
	 * @param  string    $context Context from Calendar.
	 * @return void
	 */
	protected function setup_date( Carbonate $date, $context ) {
		$this->room = $this->rooms->where( 'id', $context['id'] )->first();
	}

	/**
	 * Return row heading content for scheduler.
	 *
	 * @param  Carbonate $month Current month.
	 * @param  array     $unit  Array of current unit in loop.
	 * @return string
	 */
	protected function get_scheduler_row_heading( $month, $unit ) {
		return '<span><i class="check-column"><input type="checkbox" name="bulk-update[]" value="' . esc_attr( $unit['id'] ) . '" /></i>' . esc_html( $unit['name'] ) . '</span>';
	}
}
