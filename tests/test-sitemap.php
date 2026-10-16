<?php

/**
 * Test if author URLs are removed from XML sitemaps.
 */
class SitemapTest extends WP_UnitTestCase {

	/**
	 * Core sitemap should not have users provider.
	 */
	public function test_core_users_provider_removed() {
		$providers = wp_sitemaps_get_server()->registry->get_providers();
		$this->assertArrayNotHasKey( 'users', $providers );
		$this->assertArrayHasKey( 'posts', $providers );
	}

	/**
	 * Yoast author sitemap should be empty.
	 */
	public function test_yoast_authors_excluded() {
		$user = self::factory()->user->create_and_get( [ 'role' => 'author' ] );
		$this->assertSame( [], apply_filters( 'wpseo_sitemap_exclude_author', [ $user ] ) );
	}
}
