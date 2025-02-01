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
