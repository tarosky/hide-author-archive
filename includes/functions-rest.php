<?php
/**
 * Handle user list on REST API.
 *
 * @package hide-author-archive
 */

/**
 * Hide author archive in REST API
 *
 * @param array $args
 * @param WP_REST_Request $request
 * @return array
 */
function hide_author_archive_rest_query_filter( $args, $request ) {
	if ( ! hide_author_archive_in_rest() ) {
		return $args;
	}
	$capability = apply_filters( 'hide_author_archive_rest_query_capability', 'list_users', $args, $request );
	if ( ! current_user_can( $capability ) ) {
		$args['include'] = [ 0 ]; // False user's ID.
	}
	return $args;
}
add_filter( 'rest_user_query', 'hide_author_archive_rest_query_filter', 10, 2 );
