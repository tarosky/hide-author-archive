<?php
/**
 * Plugin Name: Hide Author Archive
 * Plugin URI:  https://github.com/kuno1/hide-author-archive
 * Description: Hide author archive pages.
 * Version:     %nightly%
 * Author:      Kunoichi INC.
 * Author URI:  https://kunoichiwp.com
 * License:     GPLv3 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-3.0.html
 * Text Domain: hide-author-archives
 */

// This file actually do nothing.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}

// Load autoloader if exists.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

/**
 * Remove rewrite rules.
 */
add_filter( 'author_rewrite_rules', '__return_empty_array' );

/**
 * Stop canonical redirect
 *
 * @param string $redirect_url
 * @param string $requested_url
 * @return string
 */
add_filter( 'redirect_canonical', function( $redirect_url, $requested_url ) {
	if ( is_author() && ! empty( $_GET['author'] ) && preg_match( '|^[0-9]+$|', $_GET['author'] ) ) {
		$redirect_url = false;
	}
	return $redirect_url;
}, 10, 2 );

/**
 * Remove author query vars.
 */
add_filter( 'query_vars', function( $vars ) {
	if ( ! is_admin() ) {
		return $vars;
	}
	$new_vars = [];
	foreach ( $vars as $var ) {
		if ( ! in_array( $var, [ 'author_name', 'author' ] ) ) {
			$new_vars[] = $var;
		}
	}
	return $new_vars;
} );

/**
 * Flush rewrite rules.
 */
function hide_author_archive_activation_hook() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'hide_author_archive_activation_hook' );
