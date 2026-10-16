<?php
/**
 * Hide author information in comments.
 *
 * @package hide-author-archive
 */

// Avoid direct access.
defined( 'ABSPATH' ) || die();

/**
 * Remove comment-author-{user_nicename} class.
 *
 * Registered user's comment has a class containing user_nicename,
 * which is usually derived from user_login.
 *
 * @param string[]   $classes Comment classes.
 * @param string[]   $css_class Additional classes.
 * @param string     $comment_id Comment ID.
 * @param WP_Comment $comment Comment object.
 * @return string[]
 */
function hide_author_archive_comment_class( $classes, $css_class, $comment_id, $comment ) {
	if ( ! $comment->user_id ) {
		return $classes;
	}
	$user = get_userdata( (int) $comment->user_id );
	if ( ! $user ) {
		return $classes;
	}
	// Same as get_comment_class().
	$author_class = 'comment-author-' . sanitize_html_class( $user->user_nicename, $comment->user_id );
	return array_values( array_diff( $classes, [ $author_class ] ) );
}
add_filter( 'comment_class', 'hide_author_archive_comment_class', 10, 4 );
