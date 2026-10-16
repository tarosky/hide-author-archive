<?php
/**
 * Remove author URLs from XML sitemaps.
 *
 * @package hide-author-archive
 */

// Avoid direct access.
defined( 'ABSPATH' ) || die();

/**
 * Remove users provider from core XML sitemaps.
 *
 * Author archive URLs contain user_nicename, which is derived from user_login.
 *
 * @param WP_Sitemaps_Provider|false $provider Sitemap provider.
 * @param string                     $name     Provider name.
 * @return WP_Sitemaps_Provider|false
 */
function hide_author_archive_sitemap_provider( $provider, $name ) {
	return 'users' === $name ? false : $provider;
}
add_filter( 'wp_sitemaps_add_provider', 'hide_author_archive_sitemap_provider', 10, 2 );
