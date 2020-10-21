<?php

/**
 * Test if filters work properly.
 *
 */
class FilterTest extends WP_UnitTestCase {

	/**
	 * Check test case.
	 */
	public function test_rewrite_rules() {
		// Check if author permalink not exists.
		global $wp_rewrite;
		$rules = $wp_rewrite->generate_rewrite_rules( '/%year%/%monthnum%/%postname%/' );
		// Check if array has no "author"
		$this->assertEquals( 0, count( array_filter( $rules, function( $key ) {
			return false !== strpos( $key, 'author' );
		}, ARRAY_FILTER_USE_KEY ) ) );
	}
	
	/**
	 * Check path to post id.
	 */
	public function test_query_vars() {
		global $wp;
		$wp->parse_request();
		foreach ( [ 'author', 'author_name' ] as $key ) {
			$this->assertFalse( in_array( $key, $wp->public_query_vars ) );
		}
	}
}
