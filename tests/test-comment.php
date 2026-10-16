<?php

/**
 * Test if author information is hidden in comments.
 */
class CommentTest extends WP_UnitTestCase {

	/**
	 * Registered user's comment should not have nicename class.
	 */
	public function test_comment_class_without_nicename() {
		$user_id    = self::factory()->user->create( [ 'user_login' => 'secret-login' ] );
		$post_id    = self::factory()->post->create( [ 'post_author' => $user_id ] );
		$comment_id = self::factory()->comment->create( [
			'comment_post_ID' => $post_id,
			'user_id'         => $user_id,
		] );
		$classes    = get_comment_class( '', $comment_id, $post_id );
		$this->assertNotContains( 'comment-author-secret-login', $classes );
		$this->assertContains( 'byuser', $classes );
		$this->assertContains( 'bypostauthor', $classes );
	}

	/**
	 * Guest comment should not be affected.
	 */
	public function test_guest_comment_class() {
		$post_id    = self::factory()->post->create();
		$comment_id = self::factory()->comment->create( [ 'comment_post_ID' => $post_id ] );
		$classes    = get_comment_class( '', $comment_id, $post_id );
		$this->assertContains( 'comment', $classes );
		$this->assertNotContains( 'byuser', $classes );
	}
}
