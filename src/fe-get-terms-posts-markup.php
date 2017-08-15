<?php
/**
 * Get markup for Terms and List of Recent Posts for each term.
 *
 * @package fe-get-terms-posts-markup
 */

/**
 * Get markup for Terms and List of Recent Posts for each.
 *
 * @param string $taxonomy The taxonomy slug to use when finding the terms.
 * @param string $post_type The post type slug to use when finding the posts.
 * @param int    $num_terms The maximum number of terms to display.
 * @param int    $num_posts The maximum number of posts to display.
 *
 * @return string The HTML markup with the terms as headings and posts with those
 *                terms as a list.
 */
function fe_get_terms_posts_markup( $taxonomy = 'post_tag', $post_type = 'post', $num_terms = 10, $num_posts = 10 ) {
	$output = '';

	$terms = get_terms( array(
		'taxonomy' => $taxonomy,
	) );

	// Filter to allow optional sorting.
	$terms = apply_filters( 'fe_gtpm_get_terms', $terms, $taxonomy, $post_type );

	foreach ( $terms as $term_count => $term ) {
		// Stop going through terms if we've reach the maximum number to display.
		if ( 10 === $term_count ) {
			break;
		}

		$output .= sprintf( '<h3><a href="%2$s">%1$s</a> (%3$s)</h3>',
			esc_html( $term->name ),
			esc_url( get_term_link( $term ) ),
			$term->count
		);
		$output .= fe_get_posts_by_term_markup( $term, $taxonomy, $post_type, $num_posts );
	}

	return $output;
}

/**
 * Get markup for List of Recent Posts by term.
 *
 * @param WP_Term $term When finding posts we look only at those with this term.
 * @param string  $taxonomy The taxonomy slug that $term belongs to.
 * @param string  $post_type The post type slug to limit our seach to.
 * @param int     $num_posts The maximum number of posts to display.
 *
 * @return string The HTML markup with the posts as a list.
 */
function fe_get_posts_by_term_markup( $term, $taxonomy = 'post_tag', $post_type = 'post', $num_posts = 6 ) {
	$output = '';

	$args = array(
		'tax_query' => array(
			array(
				'taxonomy' => $taxonomy,
				'field'    => 'term_id',
				'terms'    => $term->term_id,
			),
		),

		'post_type'              => $post_type,
		'posts_per_page'         => $num_posts,

		// Re-enable if you need pagination.
		'no_found_rows'          => true,

		// Re-enable if you use post meta in your output.
		'update_post_meta_cache' => false,

		// Re-enable if you use terms in your output.
		'update_post_term_cache' => false,
	);

	$fe_query = new WP_Query( $args );

	if ( ! $fe_query->have_posts() ) {
		return '';
	}

	$output .= sprintf(
		'<ul class="fe-get-posts-in-term-%1$s-post-type-%2$s">',
		esc_attr( $term->slug ),
		esc_attr( $post_type )
	);
	while ( $fe_query->have_posts() ) {
		$fe_query->the_post();

		$output .= sprintf(
			'<li><a href="%s">%s</a></li>',
			esc_url( get_the_permalink() ),
			esc_html( get_the_title() )
		);
	}
	$output .= '</ul>';

	return $output;
}
