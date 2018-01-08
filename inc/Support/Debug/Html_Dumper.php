<?php
namespace AweBooking\Support\Debug;

use Symfony\Component\VarDumper\Dumper\HtmlDumper as Symfony_Html_Dumper;

/**
 * This source is take from laravel/framework
 * under MIT license, copyright (c) Taylor Otwell.
 *
 * @link https://github.com/laravel/framework/blob/5.5/src/Illuminate/Support/Debug/HtmlDumper.php
 */
class Html_Dumper extends Symfony_Html_Dumper {
	/**
	 * Colour definitions for output.
	 *
	 * @var array
	 */
	protected $styles = [
		'default'   => 'background-color:#fff; color:#222; line-height:1.2em; font-weight:normal; font:12px Monaco, Consolas, monospace; word-wrap: break-word; white-space: pre-wrap; position:relative; z-index:100000',
		'num'       => 'color:#a71d5d',
		'const'     => 'color:#795da3',
		'str'       => 'color:#df5000',
		'cchr'      => 'color:#222',
		'note'      => 'color:#a71d5d',
		'ref'       => 'color:#a0a0a0',
		'public'    => 'color:#795da3',
		'protected' => 'color:#795da3',
		'private'   => 'color:#795da3',
		'meta'      => 'color:#b729d9',
		'key'       => 'color:#df5000',
		'index'     => 'color:#a71d5d',
	];
}
