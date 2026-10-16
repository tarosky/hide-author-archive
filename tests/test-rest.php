<?php

/**
 * Test REST API user endpoints.
 *
 * @package hide-author-archive
 */
class RestUserTest extends WP_UnitTestCase {

	/**
	 * Author who has a published post.
	 *
	 * @var int
	 */
	protected static $with_posts;

	/**
	 * Author who has no post.
	 *
	 * @var int
	 */
	protected static $no_posts;

	/**
	 * @var int
	 */
	protected static $subscriber;

	/**
	 * @var int
	 */
	protected static $author;

	/**
	 * @var int
	 */
	protected static $editor;

	/**
	 * @var int
	 */
	protected static $admin;

	/**
	 * Subscriber who has only list_users capability (no edit_user).
	 *
	 * @var int
	 */
	protected static $lister;

	/**
	 * Arguments passed to the capability filter.
	 *
	 * @var array|null
	 */
	protected $filter_args = null;

	/**
	 * Create users.
	 *
	 * @param WP_UnitTest_Factory $factory Factory.
	 */
	public static function wpSetUpBeforeClass( $factory ) {
		self::$with_posts = $factory->user->create( [ 'role' => 'author' ] );
		$factory->post->create( [
			'post_author' => self::$with_posts,
			'post_status' => 'publish',
		] );
		self::$no_posts   = $factory->user->create( [ 'role' => 'author' ] );
		self::$subscriber = $factory->user->create( [ 'role' => 'subscriber' ] );
		self::$author     = $factory->user->create( [ 'role' => 'author' ] );
		self::$editor     = $factory->user->create( [ 'role' => 'editor' ] );
		self::$admin      = $factory->user->create( [ 'role' => 'administrator' ] );
		self::$lister     = $factory->user->create( [ 'role' => 'subscriber' ] );
		$lister           = new WP_User( self::$lister );
		$lister->add_cap( 'list_users' );
	}

	/**
	 * Build a single user request.
	 *
	 * @param int    $user_id User ID.
	 * @param string $context Context.
	 * @return WP_REST_Request
	 */
	protected function single_request( $user_id, $context = 'view' ) {
		$request = new WP_REST_Request( 'GET', '/wp/v2/users/' . $user_id );
		$request->set_param( 'context', $context );
		return $request;
	}

	/**
	 * Dispatch a single user request.
	 *
	 * @param int    $user_id User ID.
	 * @param string $context Context.
	 * @return WP_REST_Response
	 */
	protected function get_user( $user_id, $context = 'view' ) {
		return rest_get_server()->dispatch( $this->single_request( $user_id, $context ) );
	}

	/**
	 * Dispatch a user list request.
	 *
	 * @return WP_REST_Response
	 */
	protected function get_users() {
		return rest_get_server()->dispatch( new WP_REST_Request( 'GET', '/wp/v2/users' ) );
	}

	/**
	 * Assert that the response is a successful single user.
	 *
	 * @param int              $user_id  Expected user ID.
	 * @param WP_REST_Response $response Response.
	 */
	protected function assertUserResponse( $user_id, $response ) {
		$this->assertSame( 200, $response->get_status() );
		$data = $response->get_data();
		$this->assertSame( $user_id, $data['id'] );
	}

	/**
	 * Assert that the response is an error.
	 *
	 * @param int              $status   Expected status.
	 * @param string           $code     Expected error code.
	 * @param WP_REST_Response $response Response.
	 */
	protected function assertErrorResponse( $status, $code, $response ) {
		$this->assertSame( $status, $response->get_status() );
		$data = $response->get_data();
		$this->assertSame( $code, $data['code'] );
	}

	/**
	 * Add 'read' capability to see users.
	 */
	protected function allow_read() {
		add_filter( 'hide_author_archive_rest_query_capability', function ( $caps ) {
			$caps[] = 'read';
			return $caps;
		} );
	}

	/**
	 * Assert that the plugin passes the request to core.
	 *
	 * @param int $user_id User ID.
	 */
	protected function assertPluginPasses( $user_id ) {
		$this->assertNull( hide_author_archive_get_user( null, [], $this->single_request( $user_id ) ) );
	}

	/**
	 * Guest can't get a user.
	 */
	public function test_guest_cannot_get_user() {
		wp_set_current_user( 0 );
		$this->assertErrorResponse( 401, 'rest_user_cannot_view', $this->get_user( self::$with_posts ) );
	}

	/**
	 * Subscriber can't get other user.
	 */
	public function test_subscriber_cannot_get_user() {
		wp_set_current_user( self::$subscriber );
		$this->assertErrorResponse( 403, 'rest_user_cannot_view', $this->get_user( self::$with_posts ) );
	}

	/**
	 * Editor can get other user to display post author in block editor.
	 */
	public function test_editor_can_get_user() {
		wp_set_current_user( self::$editor );
		$this->assertUserResponse( self::$with_posts, $this->get_user( self::$with_posts ) );
	}

	/**
	 * Author can get themselves.
	 */
	public function test_author_can_get_self() {
		wp_set_current_user( self::$author );
		$this->assertUserResponse( self::$author, $this->get_user( self::$author ) );
	}

	/**
	 * Administrator can get other user.
	 */
	public function test_admin_can_get_user() {
		wp_set_current_user( self::$admin );
		$this->assertUserResponse( self::$with_posts, $this->get_user( self::$with_posts ) );
	}

	/**
	 * Filtered capability is applied to single user request.
	 */
	public function test_filtered_capability_can_get_user() {
		$this->allow_read();
		wp_set_current_user( self::$subscriber );
		$this->assertUserResponse( self::$with_posts, $this->get_user( self::$with_posts ) );
	}

	/**
	 * Editor can't get user without posts, because core rejects it.
	 */
	public function test_editor_cannot_get_user_without_posts() {
		wp_set_current_user( self::$editor );
		$this->assertPluginPasses( self::$no_posts );
		$this->assertErrorResponse( 403, 'rest_user_cannot_view', $this->get_user( self::$no_posts ) );
	}

	/**
	 * Editor can't get user in edit context, because core rejects it.
	 */
	public function test_editor_cannot_get_user_in_edit_context() {
		wp_set_current_user( self::$editor );
		$this->assertErrorResponse( 403, 'rest_forbidden_context', $this->get_user( self::$with_posts, 'edit' ) );
	}

	/**
	 * Filtered capability doesn't exceed core restrictions.
	 */
	public function test_filtered_capability_respects_core() {
		$this->allow_read();
		wp_set_current_user( self::$subscriber );
		$this->assertPluginPasses( self::$no_posts );
		$this->assertErrorResponse( 403, 'rest_user_cannot_view', $this->get_user( self::$no_posts ) );
		$this->assertErrorResponse( 403, 'rest_forbidden_context', $this->get_user( self::$with_posts, 'edit' ) );
	}

	/**
	 * Author can't get other user.
	 */
	public function test_author_cannot_get_other_user() {
		wp_set_current_user( self::$author );
		$this->assertErrorResponse( 403, 'rest_user_cannot_view', $this->get_user( self::$with_posts ) );
	}

	/**
	 * Editor can list users.
	 */
	public function test_editor_can_list_users() {
		wp_set_current_user( self::$editor );
		$response = $this->get_users();
		$this->assertSame( 200, $response->get_status() );
		$this->assertContains( self::$with_posts, wp_list_pluck( $response->get_data(), 'id' ) );
	}

	/**
	 * Subscriber gets empty user list.
	 */
	public function test_subscriber_gets_empty_list() {
		wp_set_current_user( self::$subscriber );
		$response = $this->get_users();
		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( [], $response->get_data() );
	}

	/**
	 * Capability filter is called for single user request.
	 */
	public function test_capability_filter_is_called_for_single_user() {
		add_filter( 'hide_author_archive_rest_query_capability', function ( $caps, $args, $request ) {
			$this->filter_args = [ $args, $request ];
			return [ 'manage_options' ];
		}, 10, 3 );
		wp_set_current_user( self::$editor );
		$this->assertErrorResponse( 403, 'rest_user_cannot_view', $this->get_user( self::$with_posts ) );
		$this->assertNotNull( $this->filter_args, 'Filter should be called.' );
		$this->assertSame( [], $this->filter_args[0] );
		$this->assertInstanceOf( WP_REST_Request::class, $this->filter_args[1] );
	}

	/**
	 * User with list_users can get user even if the filter removes all capabilities.
	 */
	public function test_list_users_is_kept() {
		add_filter( 'hide_author_archive_rest_query_capability', '__return_empty_array' );
		wp_set_current_user( self::$lister );
		$this->assertUserResponse( self::$with_posts, $this->get_user( self::$with_posts ) );
	}

	/**
	 * Plugin does nothing if the option is off.
	 */
	public function test_option_off() {
		update_option( 'hide_author_archive_int_rest', '' );
		wp_set_current_user( 0 );
		$this->assertUserResponse( self::$with_posts, $this->get_user( self::$with_posts ) );
	}
}
