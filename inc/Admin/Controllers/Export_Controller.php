<?php
namespace AweBooking\Admin\Controllers;

use RuntimeException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use AweBooking\Admin\Calendar\Pricing_Scheduler;
use Awethemes\Http\Request;

class Export_Controller extends Controller {
	/**
	 * Show the pricing scheduler.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function rates( Request $request ) {
		$this->check_requirements();

		$this->prepare_process();

		// Get the rates.
		$scheduler = new Pricing_Scheduler;

		// Setup pricing from request.
		$scheduler->prepare( $request );

		// Build the spreadsheet.
		$spreadsheet = new Spreadsheet();

		$style = $spreadsheet->getDefaultStyle();
		$style->getFont()->setName( 'Arial' )->setSize( 10 );

		$sheet = $spreadsheet->getActiveSheet();
		$sheet->getColumnDimension( 'B' )->setWidth( 30 );

		$row = 3;
		foreach ( $scheduler->get_matrix() as $id => $itemized ) {
			if ( ! $rate = abrs_get_rate( $id ) ) {
				continue;
			}

			$sheet->setCellValueByColumnAndRow( 2, $row, $rate->get_name() );

			$column = 3;
			foreach ( $itemized as $date => $amount ) {
				$amount = abrs_decimal_raw( $amount );

				$sheet->setCellValueByColumnAndRow( $column, $row, $amount->as_string() );

				$column++;
			}

			$row++;
		}

		// Write the spreadsheet into the tmp files.
		$tmpfile = $this->write_spreadsheet( $spreadsheet, $this->tempfile() );

		return BinaryFileResponse::create( $tmpfile, 200, [], true )
			->setContentDisposition( 'inline', 'export.xlsx' )
			->deleteFileAfterSend( false );
	}

	/**
	 * Returns the tempfile path.
	 *
	 * @return string
	 */
	protected function tempfile() {
		return tempnam( get_temp_dir(), 'awebooking-tmp-' );
	}

	/**
	 * Write the spreadsheet.
	 *
	 * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet The spreadsheet.
	 * @param string                                $filename    The file name.
	 * @param string                                $writer_type The valid writer type (csv, xlsx).
	 *
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 * @return string
	 */
	protected function write_spreadsheet( Spreadsheet $spreadsheet, $filename, $writer_type = 'xlsx' ) {
		$writer = IOFactory::createWriter( $spreadsheet, ucfirst( $writer_type ) );

		if ( $writer instanceof Csv ) {
			$writer->setDelimiter( ',' );
			$writer->setEnclosure( '"' );
			$writer->setLineEnding( PHP_EOL );
			$writer->setUseBOM( false );
			$writer->setIncludeSeparatorLine( false );
			$writer->setExcelCompatibility( false );
		}

		$writer->save( $filename );
		$spreadsheet->disconnectWorksheets();

		return $filename;
	}

	/**
	 * Configure the system before process.
	 *
	 * @return void
	 */
	protected function prepare_process() {
		if ( function_exists( 'gc_enable' ) ) {
			gc_enable();
		}

		if ( function_exists( 'apache_setenv' ) ) {
			@apache_setenv( 'no-gzip', 1 ); // @codingStandardsIgnoreLine
		}

		@ini_set( 'zlib.output_compression', 'Off' ); // @codingStandardsIgnoreLine
		@ini_set( 'output_buffering', 'Off' ); // @codingStandardsIgnoreLine
		@ini_set( 'output_handler', '' ); // @codingStandardsIgnoreLine

		abrs_set_time_limit( 0 );
		ignore_user_abort( true );
	}

	/**
	 * Check PHP extendstions for PhpSpreadsheet.
	 *
	 * @return void
	 * @throws RuntimeException
	 */
	protected function check_requirements() {
		if ( ! extension_loaded( 'xml' ) || ! extension_loaded( 'zip' ) ) {
			throw new RuntimeException( 'Export function requires php_zip and php_xml extensions to works.' );
		}
	}
}
