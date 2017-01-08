<?php
/**
 * EasyMetaBuilder Functions.
 *
 * @package EasyMetaBuilder
 * @subpackage EasyMetaBuilderFunctions
 * @author Easy Meta Builder
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Returns true/false if user can edit posts. Used to filter access to meta CPT creation/edits
 *
 * @since 1.0.0
 * @return boolean
 */
function emb_user_can_access() {

	$user_can = current_user_can( 'edit_posts' );

	/**
	 * Filters if user can access metabox.
	 *
	 * @since 1.0.0
	 * @var string $user_can current_user_can( 'edit_posts' )
	 * @return bool
	 */
	$can_meta = apply_filters( 'user_can_meta', $user_can );

	return $can_meta;
}

/**
 * Echos supplied meta field
 *
 * @param  string  $meta_key meta field key.
 * @param  boolean $single to show a single item.
 * @return void
 */
function emb_meta( $meta_key = '', $single = true ) {
	echo esc_attr( get_post_meta( get_the_ID(), $meta_key, $single ) );
}
