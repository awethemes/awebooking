<?php
namespace AweBooking\Widgets;
/**
 * AweBooking Check Availability Form Widgets
 *
 * @package AweBooking/Widgets
 */
use WP_Widget;

/**
 * Awebooking_Check_Availability_Widget
 */
class Check_Availability_Widget extends WP_Widget {

	/**
	 * Constructor of class
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'awebooking-check-availability-widget',
			'description' => esc_html__( 'Display AweBooking Check Availability Form.', 'awebooking' ),
		);

		parent::__construct( 'awebooking-check-availability', esc_html__( 'AweBooking: Check Availability Form', 'awebooking' ), $widget_ops );
	}

	/**
	 * Display widget.
	 *
	 * @param  array $args     Sidebar data.
	 * @param  array $instance Widget data.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$title = $instance['title'];

		$hide_location = isset( $instance['hide_location'] ) ? $instance['hide_location'] : false;

		echo $args['before_widget']; // WPCS: XSS OK.

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title']; // WPCS: XSS OK.
		}

		if ( $hide_location ) {
			$GLOBALS['awebooking-hide-location'] = true;
		}

		/**
		 * awebooking/check_availability_area hook.
		 *
		 * @hooked abkng_template_check_availability_form - 10
		 */
		do_action( 'awebooking/check_availability_area' );

		echo $args['after_widget']; // WPCS: XSS OK.
	}

	/**
	 * Display widget control.
	 *
	 * @param  array $instance Widget data.
	 * @return void
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'hide_location' => '' ) );
		$title = strip_tags( $instance['title'] );
		$hide_location = strip_tags( $instance['hide_location'] );
		?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'awebooking' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<?php if ( abkng_config( 'enable_location' ) ) : ?>
				<p>
					<input class="checkbox" type="checkbox" <?php checked( $instance[ 'hide_location' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'hide_location' ); ?>" name="<?php echo $this->get_field_name( 'hide_location' ); ?>" />
					<label for="<?php echo $this->get_field_id( 'hide_location' ); ?>"><?php esc_html_e( 'Hide Location?', 'awebooking' ); ?></label>
				</p>
			<?php endif ?>
		<?php
	}

	/**
	 * Save widget data.
	 *
	 * @param  array $new_instance New instance.
	 * @param  array $old_instance Old instnace.
	 * @return array Save data.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = $new_instance['title'];

		if ( isset( $instance['hide_location'] ) ) {
			$instance['hide_location'] = $new_instance['hide_location'];
		}

		return $instance;
	}
}
