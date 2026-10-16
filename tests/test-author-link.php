<?php

/**
 * Test if author archive link is replaced.
 */
class AuthorLinkTest extends WP_UnitTestCase {

	/**
	 * Author ID.
	 *
	 * @var int
	 */
	protected static $author_id;

	/**
	 * Create author.
	 *
	 * @param WP_UnitTest_Factory $factory Factory.
	 */
	public static function wpSetUpBeforeClass( $factory ) {
		self::$author_id = $factory->user->create( [
			'role'       => 'author',
			'user_login' => 'secret-login',
		] );
	}

	/**
	 * Author link should not contain nicename.
	 */
	public function test_link_without_nicename() {
		$this->set_permalink_structure( '/%postname%/' );
		$this->assertStringNotContainsString( 'secret-login', get_author_posts_url( self::$author_id ) );
		$this->assertStringNotContainsString( 'author=', get_author_posts_url( self::$author_id ) );
	}

	/**
	 * Default is home if front page shows latest posts.
	 */
	public function test_default_is_home() {
		update_option( 'show_on_front', 'posts' );
		$this->assertSame( home_url(), get_author_posts_url( self::$author_id ) );
	}

	/**
	 * Default is the page for posts if front page is static.
	 */
	public function test_default_is_page_for_posts() {
		$front = self::factory()->post->create( [ 'post_type' => 'page' ] );
		$posts = self::factory()->post->create( [ 'post_type' => 'page' ] );
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front );
		update_option( 'page_for_posts', $posts );
		$this->assertSame( get_permalink( $posts ), get_author_posts_url( self::$author_id ) );
	}

	/**
	 * Option overrides the default.
	 */
	public function test_option() {
		update_option( 'hide_author_archive_author_link', 'https://example.com/about/' );
		$this->assertSame( 'https://example.com/about/', get_author_posts_url( self::$author_id ) );
	}

	/**
	 * URL is filterable per author.
	 */
	public function test_filter() {
		add_filter( 'hide_author_archive_author_link_url', function ( $url, $author_id ) {
			return 'https://example.com/staff/' . $author_id;
		}, 10, 2 );
		$this->assertSame( 'https://example.com/staff/' . self::$author_id, get_author_posts_url( self::$author_id ) );
	}
}
