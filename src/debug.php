<?php
/**
 * Debug Functions File
 *
 * You may copy, distribute and modify the software as long as you track changes/dates in source files.
 * Any modifications to or software including (via compiler) GPL-licensed code must also be made
 * available under the GPL along with build & install instructions.
 *
 * @package    WPS\Functions
 * @author     Travis Smith <t@wpsmith.net>
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
 * Writes to the debug.log.
 *
 * @param mixed  $log   Thing to be logged.
 * @param string $title Title heading.
 */
function write_log( $log, $title = '' ) {
	if ( $title ) {
		$title .= ': ';
	}
	if ( is_array( $log ) || is_object( $log ) ) {
		error_log( $title . print_r( $log, true ) );
	} else {
		error_log( $title . $log );
	}
}

/**
 * Pretty printer.
 *
 * @param mixed  $args Thing to be logged.
 * @param string $name Title heading.
 */
function printr( $args, $name = '' ) {
	if ( apply_filters( 'debug_off', false ) ) {
		return;
	}
	if ( is_string( $name ) && '' != $name ) {
		echo '<strong>' . $name . '</strong><br/>';
	}
	echo '<pre>', htmlspecialchars( print_r( $args, true ) ), "</pre>\n";
}

/**
 * Pretty printer & dies.
 *
 * @param mixed  $args Thing to be logged.
 * @param string $name Title heading.
 */
function wps_die( $args, $name = '' ) {

	printr( $args, $name );
	wp_die();

}
