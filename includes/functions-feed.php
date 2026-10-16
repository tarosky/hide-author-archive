<?php
/**
 * Hide author information in feeds.
 *
 * @package hide-author-archive
 */

// Avoid direct access.
defined( 'ABSPATH' ) || die();

/**
 * Replace author name in feeds.
 *
 * Display name equals to user_login by default,
 * so <dc:creator> in RSS and <name> in Atom may reveal login name.
 * Atom requires <author> element, so replace it instead of removing.
 *
 * @param string|null $display_name Author's display name.
 * @return string|null
 */
function hide_author_archive_feed_author( $display_name ) {
	if ( ! is_feed() ) {
		return $display_name;
	}
	return hide_author_archive_default_author_name();
}
add_filter( 'the_author', 'hide_author_archive_feed_author' );

/**
 * Remove author URL(<uri> in Atom) from feeds.
 *
 * @param string $url Author's website URL.
 * @return string
 */
function hide_author_archive_feed_author_url( $url ) {
	return is_feed() ? '' : $url;
}
add_filter( 'get_the_author_user_url', 'hide_author_archive_feed_author_url' );

// Author archive feed link is never shown because author archive does not exist, but just in case.
add_filter( 'feed_links_extra_show_author_feed', '__return_false' );
