<?php
/**
 * Archives Functions File
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
 * Gets description to category / tag / taxonomy archive pages.
 *
 * If we're not on an author archive page, then nothing is returned.
 *
 * @global \WP_Query $wp_query Query object.
 *
 * @return string Taxonomy Description.
 */
function get_taxonomy_description() {

	global $wp_query;

	if ( ! is_category() && ! is_tag() && ! is_tax() ) {
		return '';
	}

	$term = is_tax() ? get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ) : $wp_query->get_queried_object();

	if ( ! $term ) {
		return '';
	}

	$intro_text = get_term_meta( $term->term_id, 'intro_text', true );

	return apply_filters( 'genesis_term_intro_text_output', $intro_text ? $intro_text : '' );

}

/**
 * Gets description to author archive pages.
 *
 * If we're not on an author archive page, then nothing is returned.
 *
 * @return string Author description.
 */
function get_author_description() {

	if ( ! is_author() ) {
		return '';
	}

	$intro_text = get_the_author_meta( 'intro_text', (int) get_query_var( 'author' ) );

	return apply_filters( 'genesis_author_intro_text_output', $intro_text ? $intro_text : '' );

}

/**
 * Gets description to relevant custom post type archive pages.
 *
 * If we're not on a post type archive page or post type does not have
 * `genesis-cpt-archives-settings` support, then nothing extra is displayed.
 *
 * @return string Gets post type description.
 */
function get_cpt_archive_description() {

	if ( ! is_post_type_archive() || ! genesis_has_post_type_archive_support() ) {
		return '';
	}

	$intro_text = genesis_get_cpt_option( 'intro_text' );

	return apply_filters( 'genesis_cpt_archive_intro_text_output', $intro_text ? $intro_text : '' );

}

/**
 * Add custom heading and description to blog template pages.
 *
 * If we're not on a blog template page, then nothing extra is displayed.
 *
 * @return string Blog template description.
 */
function get_blog_template_description() {

	if (
		! is_page_template( 'page_blog.php' )
		|| get_queried_object_id() === get_option( 'page_for_posts' )
	) {
		return '';
	}

	return get_the_content();

}
