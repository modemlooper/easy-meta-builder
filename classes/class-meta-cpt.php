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
			'name'                  => _x( 'Meta Boxes', 'Post Type General Name', 'easymetabuilder' ),
			'singular_name'         => _x( 'Meta Box', 'Post Type Singular Name', 'easymetabuilder' ),
			'menu_name'             => __( 'Meta', 'easymetabuilder' ),
			'name_admin_bar'        => __( 'Meta', 'easymetabuilder' ),
			'archives'              => __( 'Meta Archives', 'easymetabuilder' ),
			'parent_item_colon'     => __( 'Parent Meta:', 'easymetabuilder' ),
			'all_items'             => __( 'All Meta Boxes', 'easymetabuilder' ),
			'add_new_item'          => __( 'Add New Meta Box', 'easymetabuilder' ),
			'add_new'               => __( 'Add New', 'easymetabuilder' ),
			'new_item'              => __( 'New Meta Box', 'easymetabuilder' ),
			'edit_item'             => __( 'Edit Meta Box', 'easymetabuilder' ),
			'update_item'           => __( 'Update Meta Box', 'easymetabuilder' ),
			'view_item'             => __( 'View Meta Box', 'easymetabuilder' ),
			'search_items'          => __( 'Search Meta', 'easymetabuilder' ),
			'not_found'             => __( 'Not found', 'easymetabuilder' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'easymetabuilder' ),
			'featured_image'        => __( 'Featured Image', 'easymetabuilder' ),
			'set_featured_image'    => __( 'Set featured image', 'easymetabuilder' ),
			'remove_featured_image' => __( 'Remove featured image', 'easymetabuilder' ),
			'use_featured_image'    => __( 'Use as featured image', 'easymetabuilder' ),
			'insert_into_item'      => __( 'Insert into Meta', 'easymetabuilder' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Meta', 'easymetabuilder' ),
			'items_list'            => __( 'Meta list', 'easymetabuilder' ),
			'items_list_navigation' => __( 'Meta list navigation', 'easymetabuilder' ),
			'filter_items_list'     => __( 'Filter meta list', 'easymetabuilder' ),
		);
		$args = array(
			'label'               => __( 'Easy Meta Builder', 'easymetabuilder' ),
			'description'         => __( 'Meta for post types.', 'easymetabuilder' ),
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
