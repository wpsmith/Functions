<?php
/**
 * Column Classes Functions File
 *
 * You may copy, distribute and modify the software as long as you track changes/dates in source files.
 * Any modifications to or software including (via compiler) GPL-licensed code must also be made
 * available under the GPL along with build & install instructions.
 *
 * @package    WPS\Functions
 * @author     Travis Smith <t@wpsmith.net>
 * @link       http://www.billerickson.net/code/get-column-classes/
 * @copyright  2015-2018 Travis Smith
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License v2
 * @link       https://github.com/wpsmith/WPS
 * @version    1.0.0
 * @since      0.1.0
 */

namespace WPS;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Column Classes
 *
 * @param int    $columns       How many columns content should be broken into.
 * @param int    $count         The current post in the loop (starts at 0).
 * @param string $extra_classes Any additional classes to add on all posts.
 *
 * @return string $classes
 */
function get_column_classes( $columns = 2, $count = 0, $extra_classes = '' ) {
	$column_classes = array( '', 'full-width', 'one-half', 'one-third', 'one-fourth', 'one-fifth', 'one-sixth' );

	$output = $column_classes[ $columns ];
	if ( 0 === $count || 0 === $count % $columns ) {
		$output .= ' first';
	}
	if ( $extra_classes ) {
		$output .= ' ' . $extra_classes;
	}

	return $output;
}

/**
 * Column Classes.
 *
 * @param string $column_class  How many columns content should be broken into.
 * @param int    $count         The current post in the loop (starts at 0).
 * @param string $extra_classes Any additional classes to add on all posts.
 *
 * @return string $classes
 */
function get_column_classes_by_column_class( $column_class = '', $count = 0, $extra_classes = '' ) {
	$column_classes = array(
		'',
		'',
		'one-half',
		'one-third',
		'one-fourth',
		'one-fifth',
		'one-sixth',
	);

	$output  = $column_class;
	$columns = array_search( $column_class, $column_classes, true );
	if ( 0 === $count || 0 === $count % $columns ) {
		$output .= ' first';
	}
	if ( $extra_classes ) {
		$output .= ' ' . $extra_classes;
	}

	return $output;
}

/**
 * Gets column class number from column class string.
 *
 * @param string $column_class Column class (e.g., 'one-third').
 *
 * @return false|int|string
 */
function get_column_class_num_by_column_class_name( $column_class ) {
	$column_classes = array(
		'',
		'',
		'one-half',
		'one-third',
		'one-fourth',
		'one-fifth',
		'one-sixth',
	);

	return array_search( $column_class, $column_classes, true );
}
