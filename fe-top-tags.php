<?php
/**
 * Plugin Name: Top Tags
 * Plugin URI: https://github.com/salcode/fe-top-tags
 * Description: Display the most used tags and the most recent posts within those tags using the shortcode [fe_top_tags].
 * Version: 1.0.0
 * Author: Sal Ferrarello
 * Author URI: http://salferrarello.com/
 * License: Apache-2.0
 * License URI: https://spdx.org/licenses/Apache-2.0.html
 * Text Domain: fe-top-tags
 * Domain Path: /languages
 *
 * @package fe-top-tags
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( 'src/fe-get-terms-posts-markup.php' );

add_shortcode( 'fe_top_tags', 'fe_top_tags_shortcode' );

// Apply our filter to sort the terms.
add_filter( 'fe_gtpm_get_terms', 'fe_sort_terms_by_desc_count' );

/**
 * Get HTML markup for the post_terms with the highest count and the
 * first few posts from each of them.
 *
 * NOTE: We are using the default values for fe_get_terms_posts_markup().
 * The results can be customized by providing parameters.
 *
 * @return HTML for the most populate terms as headings with a list of the
 *         most recent posts for each term.
 */
function fe_top_tags_shortcode() {
	$output = '';

	$output .= fe_get_terms_posts_markup();

	return $output;
}

/**
 * Sort terms using a comparison function.
 *
 * @param  WP_Term[] $terms Array of terms on the site.
 * @return WP_Term[] Array of terms on the site sorted in order by descending count.
 */
function fe_sort_terms_by_desc_count( $terms ) {

	usort( $terms, 'fe_compare_terms_by_count' );

	return $terms;
}

/**
 * Compare two instances of WP_Term based on their count.
 *
 * @param  WP_Term $term_a The first value to compare.
 * @param  WP_Term $term_b The second value to compare.
 * @return int One of -1, 0, or 1 for A has a lower count, the counts are equal,
 *             B has a lower count, respectively.
 */
function fe_compare_terms_by_count( $term_a, $term_b ) {
	if ( $term_a->count === $term_b->count ) {
		return 0;
	}
	if ( $term_a->count < $term_b->count ) {
		return 1;
	}
	return -1;
}
