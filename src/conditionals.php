<?php
/**
 * Conditional Functions File
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
 * Determine if a specified template is being used.
 *
 * `is_page_template()` is not available within the loop or any loop that
 * modifies $wp_query because it changes all the conditionals therein.
 * Since the conditionals change, is_page() no longer returns true, thus
 * is_page_template() will always return false.
 *
 * @link http://codex.wordpress.org/Function_Reference/is_page_template#Cannot_Be_Used_Inside_The_Loop
 *
 * @return bool True if Blog template is being used, false otherwise.
 */
function is_page_template( $template ) {

	global $wp_the_query;

	return get_post_meta( $wp_the_query->get_queried_object_id(), '_wp_page_template', true ) === $template;

}

/**
 * Determines whether a particular page template is active.
 *
 * @param string $pagetemplate Page template.
 *
 * @return bool Whether page template is active anywhere.
 */
function is_pagetemplate_active( $pagetemplate ) {
	global $wpdb;
	$sql    = $wpdb->prepare("select meta_key from %s where meta_key like '_wp_page_template' and meta_value like '%s'", $wpdb->postmeta, $pagetemplate);
	$result = $wpdb->query( $sql );
	if ( $result ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Sets a 30-day cookie to identify a users' first time visit.
 *
 * @return bool
 */
function is_first_time() {
	$cookie = '_wp_' . sanitize_title_with_dashes( get_bloginfo( 'name' ) ) . 'firsttime';
	if ( isset( $_COOKIE[ $cookie ] ) || is_user_logged_in() ) {
		return false;
	} else {
		// expires in 30 days.
		setcookie( $cookie, 1, time() + ( WEEK_IN_SECONDS * 4 ), COOKIEPATH, COOKIE_DOMAIN, false );

		return true;
	}
}

/**
 * Whether the URL is an external URL.
 *
 * @param string $host Host domain.
 *
 * @return bool
 */
function is_external_host( $host ) {
	return ( $host !== $_SERVER['SERVER_NAME'] );
}

/**
 * Whether site is in debug mode.
 *
 * @return bool
 */
function is_debug() {
	return ( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) );
}

/**
 * Determines whether cron is disabled or not.
 *
 * @return bool
 */
function is_cron_disabled() {
	return ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON );
}

/**
 * Determines whether site is doing a CRON job.
 * @return bool
 */
function is_doing_cron() {
	return (
		defined( 'DOING_CRON' ) && DOING_CRON ||
		isset( $_GET['doing_wp_cron'] )
	);
}

/**
 * Whether the theme supports 3 column layouts.
 *
 * @return bool
 */
function has_3_column_layout() {

	$layouts = genesis_get_layouts();

	$three_column_layouts = array(
		'content-sidebar-sidebar',
		'sidebar-content-sidebar',
		'sidebar-sidebar-content',
	);

	foreach ( $three_column_layouts as $layout ) {
		if ( array_key_exists( $layout, $layouts ) ) {
			return true;
		}
	}

	return false;

}

/**
 * Check whether a plugin is active.
 *
 * Only plugins installed in the plugins/ folder can be active.
 *
 * Plugins in the mu-plugins/ folder can't be "activated," so this function will
 * return false for those plugins.
 *
 * @param string $plugin Path to the main plugin file from plugins directory.
 * @return bool True, if in the active plugins list. False, not in the list.
 */
function is_plugin_active( $plugin ) {
	// ensure is_plugin_active() exists (not on frontend)
	if( !function_exists('is_plugin_active') ) {

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	}

	return \is_plugin_active( $plugin );
}

/**
 * Checks to see if a heartbeat is resulting in activity.
 *
 * @return bool
 */
function is_heartbeat() {
	return ( isset( $_POST['action'] ) && 'heartbeat' === $_POST['action'] );
}

/**
 * Checks to see if DOING_AJAX.
 *
 * @return bool
 */
function is_doing_ajax() {
	return ( defined( 'DOING_AJAX' ) && DOING_AJAX );
}

/**
 * Checks to see if WP_CLI.
 *
 * @return bool
 */
function is_wp_cli() {
	return ( defined( 'WP_CLI' ) && WP_CLI );
}