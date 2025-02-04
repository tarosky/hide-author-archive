<?php
/**
 * Override Yoast SEO plugin.
 */


/**
 * No meta=author in <head>.
 */
add_filter( 'wpseo_meta_author', '__return_false', 100 );

/**
 * No author in JSON-LD.
 */
add_filter( 'wpseo_schema_needs_author', '__return_false', 100 );

/**
 * Avoid author name in Slack.
 */
add_filter( 'wpseo_enhanced_slack_data', function ( $data ) {
	return [];
} );

/**
 * Avoid article:author meta tag.
 *
 * If user has a Facebook URL on their contact methods, it will be shown in the meta tag.
 *
 * @param string @author Facebook URL.
 * @return string
 */
add_filter( 'wpseo_opengraph_author_facebook', function ( $author ) {
	return '';
}, 100 );

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
 * Override author in Artible schema.
 *
 * Author is required for the Article schema.
 */
add_filter( 'wpseo_schema_article', function ( $data ) {
	if ( ! empty( $data['author'] ) ) {
		$data['author']['name'] = hide_author_archive_default_author_name();
		$data['author']['@id']  = hide_author_archive_default_author_url();
	}
	return $data;
} );

/**
 * Override oEmbed author.
 *
 * This is not yoast specific, but it is related to the author.
 *
 * @param array $data oEmbed data.
 * @return array
 */
add_filter( 'oembed_response_data', function ( $data ) {
	if ( isset( $data['author_name'], $data['author_url'] ) ) {
		$data['author_name'] = hide_author_archive_default_author_name();
		$data['author_url']  = hide_author_archive_default_author_url();
	}
	return $data;
} );
