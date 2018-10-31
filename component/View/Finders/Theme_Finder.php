<?php

namespace AweBooking\Component\View\Finders;

class Theme_Finder extends Directory_Finder {
	/**
	 * {@inheritdoc}
	 */
	protected function find_in_paths( $name, $paths ) {
		$paths = ! is_array( $paths ) ? [ $paths ] : $paths;

		foreach ( $paths as $path ) {
			foreach ( $this->get_possible_view_files( $name ) as $file ) {
				if ( file_exists( $view_path = $path . '/' . $file ) ) {
					return $view_path;
				}
			}
		}

		throw new \InvalidArgumentException( "View [$name] not found." );
	}
}
