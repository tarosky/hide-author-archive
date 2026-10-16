<?php
/**
 * Default author information and author link.
 *
 * @package hide-author-archive
 */

// Avoid direct access.
defined( 'ABSPATH' ) || die();

/**
 * Default author name.
 *
 * @return string
 */
function hide_author_archive_default_author_name() {
	return apply_filters( 'hide_author_archive_default_author_name', get_bloginfo( 'name' ) );
}

/**
 * Default author URL.
 *
 * @return string
 */
function hide_author_archive_default_author_url() {
	return apply_filters( 'hide_author_archive_default_author_url', home_url() );
}

/**
 * URL which replaces author archive link.
 *
 * Defaults to posts page(front page or the page for posts).
 *
 * @param int $author_id Author ID.
 * @return string
 */
function hide_author_archive_author_link_url( $author_id = 0 ) {
	$url = (string) get_option( 'hide_author_archive_author_link', '' );
	if ( ! $url ) {
		$url = (string) get_post_type_archive_link( 'post' );
	}
	/**
	 * Filters URL which replaces author archive link.
	 *
	 * @param string $url       URL.
	 * @param int    $author_id Author ID.
	 */
	return (string) apply_filters( 'hide_author_archive_author_link_url', $url, $author_id );
}

/**
 * Replace author archive link.
 *
 * Author archive URL contains user_nicename, which is usually derived from user_login.
 *
 * @param string $link      Author archive URL.
 * @param int    $author_id Author ID.
 * @return string
 */
function hide_author_archive_author_link( $link, $author_id ) {
	return hide_author_archive_author_link_url( (int) $author_id );
}
add_filter( 'author_link', 'hide_author_archive_author_link', 10, 2 );
