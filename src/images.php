<?php
/**
 * Images Functions File
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
 * Conditionally registers a new image size if size doesn't already exist.
 *
 * @global array     $_wp_additional_image_sizes Associative array of additional image sizes.
 *
 * @param string     $name                       Image size identifier.
 * @param int        $width                      Image width in pixels.
 * @param int        $height                     Image height in pixels.
 * @param bool|array $crop                       Optional. Whether to crop images to specified width and height or
 *                                               resize.
 */
function add_image_size( $name, $width = 0, $height = 0, $crop = false ) {
	global $_wp_additional_image_sizes;

	if ( ! isset( $_wp_additional_image_sizes[ $name ] ) ) {
		\add_image_size( $name, $width, $height, $crop );
	}
}

/**
 * Get size information for all currently-registered image sizes.
 *
 * @global $_wp_additional_image_sizes
 * @uses   get_intermediate_image_sizes()
 * @return array $sizes Data for all currently-registered image sizes.
 */
function get_image_sizes() {
	global $_wp_additional_image_sizes;

	$sizes = array();

	foreach ( get_intermediate_image_sizes() as $_size ) {
		if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
			$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
			$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
			$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
		} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
			$sizes[ $_size ] = array(
				'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
				'height' => $_wp_additional_image_sizes[ $_size ]['height'],
				'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
			);
		}
	}

	return $sizes;
}

/**
 * Get size information for a specific image size.
 *
 * @uses   get_image_sizes()
 *
 * @param  string $size The image size for which to retrieve data.
 *
 * @return bool|array $size Size data about an image size or false if the size doesn't exist.
 */
function get_image_size( $size ) {
	$sizes = get_image_sizes();

	if ( isset( $sizes[ $size ] ) ) {
		return $sizes[ $size ];
	}

	return false;
}

/**
 * Get the width of a specific image size.
 *
 * @uses   get_image_size()
 *
 * @param  string $size The image size for which to retrieve data.
 *
 * @return bool|string $size Width of an image size or false if the size doesn't exist.
 */
function get_image_width( $size ) {
	$size = get_image_size( $size );

	if ( ! $size ) {
		return false;
	}

	if ( isset( $size['width'] ) ) {
		return $size['width'];
	}

	return false;
}

/**
 * Get the height of a specific image size.
 *
 * @uses   get_image_size()
 *
 * @param  string $size The image size for which to retrieve data.
 *
 * @return bool|string $size Height of an image size or false if the size doesn't exist.
 */
function get_image_height( $size ) {
	$size = get_image_size( $size );

	if ( ! $size ) {
		return false;
	}

	if ( isset( $size['height'] ) ) {
		return $size['height'];
	}

	return false;
}

add_filter( 'genesis_pre_get_image', __NAMESPACE__ . '\genesis_pre_get_image', 10, 2 );
/**
 * Short-circuit genesis_get_image to output oEmbed featured items.
 *
 * @param false|string $pre  Default false.
 * @param array        $args Array of supported args. Supported $args keys are:
 *                           - format   - string, default is 'html'.
 *                           - size     - string, default is 'full'.
 *                           - num      - integer, default is 0.
 *                           - attr     - string, default is ''.
 *                           - fallback - mixed, default is 'first-attached'.
 *
 * @return false|string
 */
function genesis_pre_get_image( $pre, $args ) {
	$meta = get_post_meta( get_the_ID(), 'video', true );
	if ( $meta && 'html' === $args['format'] ) {
		if ( isset( $args['size'] ) ) {
			if ( is_string( $args['size'] ) ) {
				$size = get_image_size( $args['size'] );

				return wp_oembed_get( $meta, $size );
			} elseif ( is_array( $args['size'] ) ) {
				return wp_oembed_get( $meta, array(
					'height' => $args['size'][0],
					'width'  => $args['size'][1],
				) );
			}
		}

		return wp_oembed_get( $meta );
	}

	return $pre;
}
