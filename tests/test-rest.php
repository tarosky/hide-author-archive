<?php

/**
 * Test if user information is hidden in REST API.
 */
class RestTest extends WP_UnitTestCase {

	/**
	 * Author ID.
	 *
	 * @var int
	 */
	protected static $author_id;

	/**
	 * Post ID.
	 *
	 * @var int
	 */
	protected static $post_id;

	/**
	 * Create author and post.
	 *
	 * @param WP_UnitTest_Factory $factory Factory.
	 */
	public static function wpSetUpBeforeClass( $factory ) {
		self::$author_id = $factory->user->create( [
			'role'       => 'author',
			'user_login' => 'secret-login',
		] );
		self::$post_id   = $factory->post->create( [ 'post_author' => self::$author_id ] );
	}

	/**
	 * Get embedded author of the post.
	 *
	 * Embedded resources are fetched via internal request,
	 * so they should be blocked by rest_request_before_callbacks.
	 *
	 * @return array
	 */
	protected function get_embedded_author() {
		$server   = rest_get_server();
		$response = $server->dispatch( new WP_REST_Request( 'GET', '/wp/v2/posts/' . self::$post_id ) );
		$data     = $server->response_to_data( $response, true );
		$this->assertArrayHasKey( '_embedded', $data );
		return $data['_embedded']['author'][0];
	}

	/**
	 * Anonymous user cannot get author via _embed.
	 */
	public function test_embedded_author_hidden() {
		wp_set_current_user( 0 );
		$author = $this->get_embedded_author();
		$this->assertSame( 'rest_user_cannot_view', $author['code'] );
		$this->assertArrayNotHasKey( 'slug', $author );
	}

	/**
	 * Administrator can get author via _embed.
	 */
	public function test_embedded_author_for_admin() {
		wp_set_current_user( self::factory()->user->create( [ 'role' => 'administrator' ] ) );
		$author = $this->get_embedded_author();
		$this->assertSame( 'secret-login', $author['slug'] );
	}
}
