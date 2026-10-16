<?php

/**
 * Test if author information is hidden in feeds.
 */
class FeedTest extends WP_UnitTestCase {

	/**
	 * Author ID.
	 *
	 * @var int
	 */
	protected static $author_id;

	/**
	 * Create author whose display name is the same as login.
	 *
	 * @param WP_UnitTest_Factory $factory Factory.
	 */
	public static function wpSetUpBeforeClass( $factory ) {
		self::$author_id = $factory->user->create( [
			'role'       => 'author',
			'user_login' => 'secret-login',
			'user_url'   => 'https://example.com/secret',
		] );
		$factory->post->create( [ 'post_author' => self::$author_id ] );
	}

	/**
	 * Author name and URL should be replaced in feeds.
	 */
	public function test_author_in_feed() {
		$this->go_to( home_url( '?feed=rss2' ) );
		$this->assertTrue( is_feed() );
		$this->assertTrue( have_posts() );
		the_post();
		$this->assertSame( get_bloginfo( 'name' ), get_the_author() );
		$this->assertSame( '', get_the_author_meta( 'url' ) );
	}

	/**
	 * Author name and URL should not be changed outside feeds.
	 */
	public function test_author_outside_feed() {
		$this->go_to( home_url( '/' ) );
		$this->assertFalse( is_feed() );
		the_post();
		$this->assertSame( 'secret-login', get_the_author() );
		$this->assertSame( 'https://example.com/secret', get_the_author_meta( 'url' ) );
	}

	/**
	 * Default author name is filterable.
	 */
	public function test_author_name_filter() {
		add_filter( 'hide_author_archive_default_author_name', function () {
			return 'Editorial Team';
		} );
		$this->go_to( home_url( '?feed=atom' ) );
		the_post();
		$this->assertSame( 'Editorial Team', get_the_author() );
	}

	/**
	 * Author feed link should be disabled.
	 */
	public function test_author_feed_link_disabled() {
		$this->assertFalse( apply_filters( 'feed_links_extra_show_author_feed', true ) );
	}
}
