<?php
namespace Skeleton\CMB2\Render;

use CMB2_Field;
use Skeleton\CMB2\CMB2;
use Skeleton\CMB2\Panel;
use Skeleton\CMB2\Section;
use Skeleton\CMB2\Tabable;

class CMB2_Render implements Render_Interface {
	/**
	 * CMB2 Instance.
	 *
	 * @var CMB2
	 */
	protected $cmb2;

	/**
	 * HTML before display CMB2.
	 *
	 * @var string
	 */
	public $before_display = '';

	/**
	 * HTML after display CMB2.
	 *
	 * @var string
	 */
	public $after_display = '';

	/**
	 * HTML before display navigation.
	 *
	 * @var string
	 */
	public $before_navigation = '';

	/**
	 * HTML after display navigation.
	 *
	 * @var string
	 */
	public $after_navigation = '';

	/**
	 * Extra navigation class.
	 *
	 * @var string
	 */
	public $navigation_class = '';

	/**
	 * HTML before display sections.
	 *
	 * @var string
	 */
	public $before_sections = '';

	/**
	 * HTML after display sections.
	 *
	 * @var string
	 */
	public $after_sections = '';

	/**
	 * Extra sections class.
	 *
	 * @var string
	 */
	public $sections_class = '';

	/**
	 * Constructor of class.
	 *
	 * @param CMB2 $cmb2 CMB2 Instance.
	 */
	public function __construct( CMB2 $cmb2 ) {
		$this->cmb2 = $cmb2;
	}

	/**
	 * Display a property call property callable if need.
	 *
	 * @param  string $property Property name.
	 * @return string|void
	 */
	protected function prop_callback( $property ) {
		if ( ! isset( $this->{$property} ) ) {
			return;
		}

		if ( is_callable( $this->{$property} ) ) {
			ob_start();
			echo call_user_func( $this->{$property}, $this ); // WPCS: XSS OK.
			$display = ob_get_clean();
		} else {
			$display = $this->{$property};
		}

		if ( is_string( $display ) ) {
			print $display; // WPCS: XSS OK.
		}
	}

	/**
	 * Display CMB2 fields.
	 */
	public function display() {
		$this->prop_callback( 'before_display' );

		// Display navigation tabs.
		$this->navigation_template();

		// Display content tabs.
		printf( '<div class="cmb2-tab-content %s">', esc_attr( $this->sections_class ) );
		$this->prop_callback( 'before_sections' );

		$i = 0;
		foreach ( $this->cmb2->tabs() as $tabable ) {
			$pane_class = ( 0 === $i ) ? 'active' : '';

			if ( $tabable instanceof Panel ) {
				// Loop through sections of panel and render each section.
				foreach ( $tabable->sections as $section ) {
					$this->section_template( $section );
				}

				// Continue display next tabable.
				continue;
			}

			// Build and display section template.
			$this->section_template( $tabable, $pane_class );
			$i++;
		}

		$this->prop_callback( 'after_sections' );
		print( '</div><!-- End .cmb2-tab-content -->' );

		$this->prop_callback( 'after_display' );
	}

	/**
	 * Display CMB2 section content template.
	 *
	 * @param  Section $section    Section instance.
	 * @param  string  $pane_class Custom class for the pane.
	 * @return void
	 */
	protected function section_template( Section $section, $pane_class = '' ) {
		// Don't display a section with empty fields.
		if ( empty( $section->fields ) ) {
			return;
		}

		ob_start();
		// Loop through section fields and render each field.
		foreach ( $section->fields as $field ) {
			$this->cmb2->render_field( $field );
		}

		$fields = ob_get_clean();
		printf( '<div id="%1$s" class="cmb2-tab-pane %2$s">%3$s</div>', esc_attr( $section->uniqid() ), esc_attr( $pane_class ), $fields ); // WPCS: XSS OK.
	}

	/**
	 * Display CMB2 navigation tabs template.
	 *
	 * @return void
	 */
	protected function navigation_template() {
		$i = 0;
		$template = '';

		foreach ( $this->cmb2->tabs() as $tabable ) {
			$list_class = ( 0 === $i ) ? 'active' : '';

			if ( $tabable instanceof Panel ) {
				$template .= $this->navigation_panel( $tabable );
			} else {
				$template .= $this->navigation_section( $tabable, $list_class );
			}

			$i++;
		}

		if ( $template ) {
			$this->prop_callback( 'before_navigation' );
			printf( '<nav class="cmb2-nav %2$s"><ul class="cmb2-tabs">%1$s</ul></nav>', $template, $this->navigation_class ); // WPCS: XSS OK.
			$this->prop_callback( 'after_navigation' );
		}
	}

	/**
	 * Return navigation panel template.
	 *
	 * @param  Panel $panel
	 * @return string
	 */
	protected function navigation_panel( Panel $panel ) {
		// Don't display panel if empty sections.
		if ( empty( $panel->sections ) ) {
			return;
		}

		$sections_template = '';
		foreach ( $panel->sections as $section ) {
			$sections_template .= $this->navigation_section( $section );
		}

		return sprintf(
			'<li class="cmb2-panel"><a href="#" class="cmb2-panel-link">%2$s %1$s</a><ul class="cmb2-sub-tabs">%3$s</ul></li>',
			esc_html( $panel->title ? $panel->title : $panel->id ),
			$panel->build_icon(),
			$sections_template
		);
	}

	/**
	 * Return navigation section template.
	 *
	 * @param  Section $section
	 * @param  string  $list_class
	 * @return string
	 */
	protected function navigation_section( Section $section, $list_class = '' ) {
		// Don't display a section with empty fields.
		if ( empty( $section->fields ) ) {
			return;
		}

		return sprintf( '<li class="cmb2-tab %1$s"><a href="#%2$s" class="cmb2-tab-link" data-target="#%2$s">%4$s %3$s</a></li>',
			esc_attr( $list_class ),
			esc_attr( $section->uniqid() ),
			esc_html( $section->title ? $section->title : $section->id ),
			$section->build_icon()
		);
	}

	/**
	 * Render a repeatable group.
	 *
	 * @see CMB2::render_group()
	 *
	 * @param array $args Array of field arguments for a group field parent.
	 * @return CMB2_Field|null Group field object.
	 */
	public function render_group( array $args ) {
		if ( ! isset( $args['id'], $args['fields'] ) || ! is_array( $args['fields'] ) ) {
			return;
		}

		$field_group = $this->cmb2->get_field( $args );

		// If field is requesting to be conditionally shown.
		if ( ! $field_group || ! $field_group->should_show() ) {
			return;
		}

		$desc            = $field_group->args( 'description' );
		$label           = $field_group->args( 'name' );
		$group_val       = (array) $field_group->value();
		$remove_disabled = count( $group_val ) <= 1 ? 'disabled="disabled" ' : '';
		$field_group->index = 0;

		$field_group->peform_param_callback( 'before_group' );

		echo '<div class="cmb-row cmb-repeat-group-wrap ', $field_group->row_classes(), '" data-fieldtype="group"><div data-groupid="', $field_group->id(), '" id="', $field_group->id(), '_repeat" ', $this->cmb2->group_wrap_attributes( $field_group ), '>'; // WPCS: XSS OK.

		if ( $label ) {
			echo '<div class="cmb-th"><label class="cmb-group-name">', $label, '</label></div>'; // WPCS: XSS OK.
		}

		echo '<div class="cmb-td">';
		if ( $desc ) {
			echo '<p class="cmb2-metabox-description">', $desc, '</p>'; // WPCS: XSS OK.
		}

		if ( ! empty( $group_val ) ) {
			foreach ( $group_val as $group_key => $field_id ) {
				$this->cmb2->render_group_row( $field_group, $remove_disabled );
				$field_group->index++;
			}
		} else {
			$this->cmb2->render_group_row( $field_group, $remove_disabled );
		}

		if ( $field_group->args( 'repeatable' ) ) {
			echo '<div class="cmb-row"><div class="cmb-td"><p class="cmb-add-row"><button type="button" data-selector="', $field_group->id(), '_repeat" data-grouptitle="', $field_group->options( 'group_title' ), '" class="cmb-add-group-row button">', $field_group->options( 'add_button' ), '</button></p></div></div>'; // WPCS: XSS OK.
		}

		echo '</div>';
		echo '</div></div>';

		$field_group->peform_param_callback( 'after_group' );

		return $field_group;
	}

	/**
	 * Manually render field.
	 *
	 * @param array      $field_args Array of field arguments.
	 * @param CMB2_Field $field      The CMB2_Field instance.
	 */
	public function render_field( array $field_args, CMB2_Field $field ) {
		// If field is requesting to not be shown on the front-end.
		if ( ! is_admin() && ! $field->args( 'on_front' ) ) {
			return;
		}

		// If field is requesting to be conditionally shown.
		if ( ! $field->should_show() ) {
			return;
		}

		// Build field dependencies.
		$deps = '';
		if ( $field->group && ! $field->args( 'repeatable' ) ) {
			$deps = $this->build_field_deps( $field_args, 'group' );
			$field->args['attributes']['data-group-deps-id'] = $field->id( true );
		} elseif ( ! $field->args( 'repeatable' ) ) {
			$deps = $this->build_field_deps( $field_args );
			$field->args['attributes']['data-deps-id'] = $field->id( true );
		}

		$field->peform_param_callback( 'before_row' );

		printf( "<div class=\"cmb-row %s\" data-fieldtype=\"%s\" %s>\n", $field->row_classes(), $field->type(), $deps ); // WPCS: XSS OK.

		if ( ! $field->args( 'show_names' ) ) {
			echo "\n\t<div class=\"cmb-td\">\n";

			$field->peform_param_callback( 'label_cb' );

		} else {

			if ( $field->get_param_callback_result( 'label_cb' ) ) {
				echo '<div class="cmb-th">', $field->peform_param_callback( 'label_cb' ), '</div>'; // WPCS: XSS OK.
			}

			echo "\n\t<div class=\"cmb-td\">\n";
		}

		$field->peform_param_callback( 'before' );

		if ( $field->maybe_callback( 'render_field_cb' ) ) {
			$field->peform_param_callback( 'render_field_cb' );
		} else {
			$field_type = new \CMB2_Types( $field );
			$field_type->render();
		}

		$field->peform_param_callback( 'after' );

		// Show field validation error.
		$this->peform_field_error( $field );

		echo "\n\t</div>\n</div>";

		$field->peform_param_callback( 'after_row' );

		// For chaining.
		return $field;
	}

	/**
	 * Display field error if exists.
	 *
	 * @param CMB2_Field $field The CMB2_Field instance.
	 */
	protected function peform_field_error( CMB2_Field $field ) {
		$cmb2 = $field->get_cmb();

		// Bail if not see a CMB2 instance.
		if ( ! $cmb2 || is_wp_error( $cmb2 ) ) {
			return;
		}

		$id = $field->id( true );
		$errors = $cmb2->get_errors();

		if ( isset( $errors[ $id ] ) ) {
			$error_message = is_string( $errors[ $id ] ) ? $errors[ $id ] : $errors[ $id ][0];
			printf( '<p class="cmb2-validate-error">%s</p>', $error_message ); // WPCS: XSS OK.
		}
	}

	/**
	 * Build dependency fields, return a HTML attribute.
	 *
	 * @param  array  $field_args Field dependency.
	 * @param  string $prefix     HTML data attr prefix.
	 * @return string
	 */
	protected function build_field_deps( $field_args, $prefix = '' ) {
		if ( empty( $field_args['deps'] ) || ! is_array( $field_args['deps'] ) ) {
			return '';
		}

		if ( count( $field_args['deps'] ) === 1 ) {
			return '';
		}

		$deps = $field_args['deps'];
		$controller = $deps[0];

		if ( count( $deps ) === 2 ) {
			$condition = '==';
			$value = $deps[1];
		} else {
			$condition = $deps[1];
			$value = $deps[2];
		}

		if ( ! $prefix ) {
			return sprintf( 'data-deps="%s" data-deps-condition="%s" data-deps-value="%s"', $controller, $condition, $value );
		} else {
			return sprintf( 'data-%1$s-deps="%2$s" data-%1$s-deps-condition="%3$s" data-%1$s-deps-value="%4$s"', $prefix, $controller, $condition, $value );
		}
	}
}
