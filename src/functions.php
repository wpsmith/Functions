<?php
/**
 * Functions File
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
 * Gets relative path based on ABSPATH.
 *
 * @param string $path Absolute path.
 *
 * @return mixed
 */
function get_relative_path( $path ) {
	return str_replace( ABSPATH, '/', realpath( $path ) );
}

/**
 * Gets relative URL based on site URL.
 *
 * @param string $url The URL.
 *
 * @return mixed
 */
function get_relative_url( $url ) {
	$parsed_url = wp_parse_url( $url );
	$site_url   = site_url();

	if ( false !== strpos( $site_url, $parsed_url['host'] ) ) {
		return str_replace( trailingslashit( $site_url ), '/', $url );
	}

	return str_replace( $parsed_url['host'], '/', $url );
}

/**
 * Each loop helper.
 *
 * @param array    $arr  Array to run function.
 * @param callback $fn   Callback.
 * @param array    $args Callback args.
 */
function each( $arr, $fn, $args ) {
	foreach ( $arr as $item ) {
		if ( is_callable( $fn ) ) {
			call_user_func_array( $fn, array( $item, $args ) );
		}
	}
}

/**
 * Get a script's or style's dependencies.
 *
 * @param string $type   Style or Script.
 * @param string $handle Script handle.
 *
 * @return \stdClass
 */
function get_script_style_dependency( $type, $handle ) {
	if ( 'style' === $type ) {
		return ( isset( wp_styles()->registered[ $handle ] ) ? wp_styles()->registered[ $handle ] : new \stdClass() );
	}

	return ( isset( wp_scripts()->registered[ $handle ] ) ? wp_scripts()->registered[ $handle ] : new \stdClass() );
}

/**
 * Remove entry meta.
 */
function remove_post_type_entry_meta() {
	remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
}

/**
 * Removes Genesis entry footer.
 */
function remove_post_type_entry_footer() {
	remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
	remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
	remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );
}

/**
 * Removes Genesis after entry Author Box and Widget Area.
 */
function remove_post_type_after_entry() {
	remove_action( 'genesis_after_entry', 'genesis_do_author_box_single', 8 );
	remove_action( 'genesis_after_entry', 'genesis_after_entry_widget_area' );
}

/**
 * Gets plural exceptions.
 *
 * @return array
 */
function get_plural_exceptions() {
	return array(
		'addendum' => 'addenda',
		'analysis' => 'analyses',
		'child'    => 'children',
		'goose'    => 'geese',
		'locus'    => 'loci',
		'louse'    => 'lice',
		'oasis'    => 'oases',
		'ovum'     => 'ova',
		'man'      => 'men',
		'mouse'    => 'mice',
		'tooth'    => 'teeth',
		'woman'    => 'women',
	);
}

add_filter( 'wps_plural_exceptions', 'get_plural_exceptions' );
/**
 * Pluralizes words.
 *
 * Limitation: can't convert to plural out of rules, like
 * man : men / to handle this exception list is being used.
 *
 * Example:
 *  echo plural('boy', 0); // 'boy'
 *  echo plural('mango', 2); // 'mangoes'
 *  echo plural('knife', 3, 1); // '3 knives'
 *
 * @param string $word         Word to be pluralized.
 * @param int    $count        How many.
 * @param int    $return_count Return count.
 *
 * @return bool|string
 */
function plural( $word, $count = 2, $return_count = 0 ) {
	/*
	General Plural Literature Suffix Rules:
	[1.0] 'ies' rule    (ends in a consonant + y : baby/lady)
	[2.0] 'ves' rule    (ends in f or fe : leaf/knife) --- roof : rooves (correct but old english, roofs is ok).
	[3.1] 'es' rule 1   (ends in a consonant + o : volcano/mango)
	[3.2] 'es' rule 2   (ends in ch, sh, s, ss, x, z : match/dish/bus/glass/fox/buzz)
	[4.1] 's' rule 1    (ends in a vowel + y or o : boy/radio)
	[4.2] 's' rule 2    (ends in other than above : cat/ball)
	*/

	// Use mb_substr for multibyte #.
	// Normalize case, not even first letter plural as a number supposed to be placed before.
	$word = strtolower( trim( $word ) );

	// Define vowels, all non-vowel is consonant.
	$vowel = array( 'a', 'e', 'i', 'o', 'u' );

	// Prefix count with word, if specified.
	// a noun should be 2 or more letter.
	if ( preg_match( '/^[A-Za-z]{2,}$/', $word ) ) {
		if ( $count > 1 ) {
			$excep = \apply_filers( 'wps_plural_exceptions', get_plural_exceptions() ); // Load exception list.
			if ( array_key_exists( $word, $excep ) ) {
				$word = $excep[ $word ];
			} elseif ( ! in_array( substr( $word, - 2, 1 ), $vowel, true ) && substr( $word, - 1 ) === 'y' ) { // Rule [1.0].
				$word = rtrim( $word, 'y' ) . 'ies';
			} elseif ( substr( $word, - 1 ) === 'f' || substr( $word, - 2, 2 ) === 'fe' ) { // Rule [2.0].
				$word = ( substr( $word, - 1 ) === 'e' ) ? substr( $word, 0, - 2 ) : substr( $word, 0, - 1 );
				$word = $word . 'ves';
			} elseif (
				(
					! in_array( substr( $word, - 2, 1 ), $vowel, true ) &&
					substr( $word, - 1 ) === 'o'
				) || // Rule [3.1].
				in_array( substr( $word, - 2, 2 ), array(
					'ch',
					'sh',
					'ss',
				), true ) ||
				in_array( substr( $word, - 1 ), array( 's', 'x', 'z' ), true )
			) { // Rule [3.2].
				$word = $word . 'es';
			} else { // Rule [4.2], covering [4.1].
				$word = $word . 's';
			}
		} elseif ( $count < 0 ) {
			return false; // No negate in real world object existance !!??
		}

		if ( ! empty( $return_count ) ) {
			$word = $count . ' ' . $word;
		}

		return $word;
	}

	return false;
}
