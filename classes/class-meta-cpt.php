<?php
/**
 * EasyMetaBuilder_Meta_CPT Class File.
 *
 * @package EasyMetaBuilder
 * @subpackage EasyMetaBuilderCPT
 * @author Easy Meta Builder
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Main initiation class.
 *
 * @internal
 *
 * @since 1.0.0
 */
class EasyMetaBuilder_Meta_CPT {

	/**
	 * Parent plugin class.
	 *
	 * @var object
	 * @since 1.0.0
	 */
	protected $plugin = null;

	/**
	 * Holds an instance of the object.
	 *
	 * @var object easymetabuilder_Pages_CPT
	 * @since 1.0.0
	 */
	private static $instance = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin this class.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'easymetabuilder_meta_post_type' ) );
	}


	/**
	 * Set it off!
	 *
	 * @since 1.0.0
	 */
	public function init() {
	}


	/**
	 * Register Custom Post Type.
	 *
	 * @since 1.0.0
	 */
	public function easymetabuilder_meta_post_type() {

		$labels = array(
			'name'                  => _x( 'Meta Boxes', 'Post Type General Name', 'easy-meta-builder' ),
			'singular_name'         => _x( 'Meta Box', 'Post Type Singular Name', 'easy-meta-builder' ),
			'menu_name'             => __( 'Meta', 'easy-meta-builder' ),
			'name_admin_bar'        => __( 'Meta', 'easy-meta-builder' ),
			'archives'              => __( 'Meta Archives', 'easy-meta-builder' ),
			'parent_item_colon'     => __( 'Parent Meta:', 'easy-meta-builder' ),
			'all_items'             => __( 'All Meta Boxes', 'easy-meta-builder' ),
			'add_new_item'          => __( 'Add New Meta Box', 'easy-meta-builder' ),
			'add_new'               => __( 'Add New', 'easy-meta-builder' ),
			'new_item'              => __( 'New Meta Box', 'easy-meta-builder' ),
			'edit_item'             => __( 'Edit Meta Box', 'easy-meta-builder' ),
			'update_item'           => __( 'Update Meta Box', 'easy-meta-builder' ),
			'view_item'             => __( 'View Meta Box', 'easy-meta-builder' ),
			'search_items'          => __( 'Search Meta', 'easy-meta-builder' ),
			'not_found'             => __( 'Not found', 'easy-meta-builder' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'easy-meta-builder' ),
			'featured_image'        => __( 'Featured Image', 'easy-meta-builder' ),
			'set_featured_image'    => __( 'Set featured image', 'easy-meta-builder' ),
			'remove_featured_image' => __( 'Remove featured image', 'easy-meta-builder' ),
			'use_featured_image'    => __( 'Use as featured image', 'easy-meta-builder' ),
			'insert_into_item'      => __( 'Insert into Meta', 'easy-meta-builder' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Meta', 'easy-meta-builder' ),
			'items_list'            => __( 'Meta list', 'easy-meta-builder' ),
			'items_list_navigation' => __( 'Meta list navigation', 'easy-meta-builder' ),
			'filter_items_list'     => __( 'Filter meta list', 'easy-meta-builder' ),
		);
		$args = array(
			'label'               => __( 'Easy Meta Builder', 'easy-meta-builder' ),
			'description'         => __( 'Meta for post types.', 'easy-meta-builder' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'taxonomies'          => array(),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-image-filter',
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'page',
		);

		if ( emb_user_can_access() ) {
			register_post_type( 'easymetabuilder', $args );
		}

	}
}
