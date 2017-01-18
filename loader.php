<?php
/**
 * Easy Meta Builder Loader
 *
 * @package EasyMetaBuilder
 * @subpackage EasyMetaBuilderLoader
 * @author Easy Meta Builder
 * @since 1.0.0
 */

/**
 * Plugin Name: Easy Meta Builder
 * Plugin URI: http://easymetabuilder.com
 * Description: The fastest and easiest way to add meta fields to WordPress.
 * Version:	 1.0.4
 * Author:	  Easy Meta Builder
 * Author URI:  http://easymetabuilder.com
 * License:	 GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 4.4
 * Tested up to: 4.7
 * Stable tag: 1.0.4
 * Text Domain: easy-meta-builder
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2016 Easy Meta Builder (email : easymetabuilder@gmail.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'EasyMetaBuilder' ) ) :

	/**
	 * Autoloads files with classes when needed.
	 *
	 * @since 1.0.0
	 *
	 * @param string $class_name Name of the class being requested.
	 */
	function easymetabuilder_autoload_classes( $class_name ) {

		if ( 0 !== strpos( $class_name, 'EasyMetaBuilder_' ) ) {
			return;
		}

		$filename = strtolower( str_replace(
			'_', '-',
			substr( $class_name, strlen( 'EasyMetaBuilder_' ) )
		) );

		EasyMetaBuilder::include_file( $filename );
	}
	spl_autoload_register( 'easymetabuilder_autoload_classes' );

	/**
	 * Main initiation class.
	 *
	 * @since 1.0.0
	 *
	 * @var string $version  Plugin version.
	 * @var string $basename Plugin basename.
	 * @var string $url      Plugin URL.
	 * @var string $path     Plugin Path.
	 */
	class EasyMetaBuilder {

		/**
		 * Current version.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		const VERSION = '1.0.4';

		/**
		 * Current version.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $version = self::VERSION;

		/**
		 * Name of plugin.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $name = 'Easy Meta Builder';

		/**
		 * URL of plugin directory.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $url = '';

		/**
		 * Path of plugin directory.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $path = '';

		/**
		 * Plugin basename.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $basename = '';

		/**
		 * Singleton instance of plugin.
		 *
		 * @since 1.0.0
		 * @var EasyMetaBuilder
		 */
		protected static $single_instance = null;

		/**
		 * CPT object.
		 *
		 * @var string|object
		 * @since 1.0.0
		 */
		public $cpt = '';

		/**
		 * Builder object.
		 *
		 * @var string|object
		 * @since 1.0.0
		 */
		public $builder = '';

		/**
		 * Fields object.
		 *
		 * @var string|object
		 * @since 1.0.0
		 */
		public $field_types = '';

		/**
		 * Fields object.
		 *
		 * @var string|object
		 * @since 1.0.0
		 */
		public $advnaced_field_types = '';

		/**
		 * Creates or returns an instance of this class.
		 *
		 * @since 1.0.0
		 *
		 * @return EasyMetaBuilder A single instance of this class.
		 */
		public static function get_instance() {
			if ( null === self::$single_instance ) {
				self::$single_instance = new self();
			}

			return self::$single_instance;
		}

		/**
		 * Sets up our plugin.
		 *
		 * @since 1.0.0
		 */
		protected function __construct() {
			$this->basename = plugin_basename( __FILE__ );
			$this->url	  = plugin_dir_url( __FILE__ );
			$this->path	 = plugin_dir_path( __FILE__ );

			$this->includes();
			$this->load_libs();
			$this->plugin_classes();
		}

		/**
		 * Attach other plugin classes to the base plugin class.
		 *
		 * @since 1.0.0
		 */
		public function plugin_classes() {

			$this->cpt = new EasyMetaBuilder_Meta_CPT( $this );
			$this->builder = new EasyMetaBuilder_Builder( $this );
			$this->field_types = new EasyMetaBuilder_Field_Types( $this );
			$this->advnaced_field_types = new EasyMetaBuilder_Advanced_Field_Types( $this );
			do_action( 'easymetabuilder_loaded' );
		}

		/**
		 * Add hooks and filters.
		 *
		 * @since 1.0.0
		 */
		public function hooks() {

			add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
			add_filter( 'plugin_action_links_' . $this->basename, array( $this, 'add_social_links' ) );

		}

		/**
		 * Activate the plugin.
		 *
		 * @since 1.0.0
		 */
		function _activate() {
			// Make sure any rewrite functionality has been loaded.
			flush_rewrite_rules();
		}

		/**
		 * Deactivate the plugin.
		 *
		 * Uninstall routines should be in uninstall.php.
		 *
		 * @since 1.0.0
		 */
		function _deactivate() {}

		/**
		 * Init hooks.
		 *
		 * @since 1.0.0
		 */
		public function init() {
			load_plugin_textdomain( 'easy-meta-builder', false, dirname( $this->basename ) . '/languages/' );
		}

		/**
		 * Register scripts.
		 *
		 * @since 1.0.0
		 */
		public function scripts() {
			global $pagenow;

			if ( isset( $pagenow ) && 'post.php' === $pagenow ||  'post-new.php' === $pagenow ) {

				// Register out CSS file.
				wp_register_style( 'easymetabuilder', easymetabuilder()->url() . 'assets/css/easymetabuilder.css' );
				wp_enqueue_style( 'easymetabuilder' );

				// Register out javascript file.
				wp_register_script( 'easymetabuilder', easymetabuilder()->url() . 'assets/js/easymetabuilder.js' );

				wp_localize_script( 'easymetabuilder', 'easymetabuilder', $this->builder->add_taxomonomies_json() );

				// Enqueued script with localized data.
				wp_enqueue_script( 'easymetabuilder' );
			}
		}

		/**
		 * Load libraries.
		 *
		 * @since 1.0.0
		 */
		public function load_libs() {

			// Load cmb2.
			if ( file_exists( __DIR__ . '/vendor/cmb2/init.php' ) ) {
				require_once  __DIR__ . '/vendor/cmb2/init.php';
			} elseif ( file_exists( __DIR__ . '/vendor/CMB2/init.php' ) ) {
				require_once  __DIR__ . '/vendor/CMB2/init.php';
			}

		}

		/**
		 * Load includes.
		 *
		 * @since 1.0.0
		 */
		public function includes() {

			include( dirname( __FILE__ ) . '/includes/easy-meta-builder-functions.php' );
			include( dirname( __FILE__ ) . '/vendor/custom-fields/dashicon-radio-field.php' );

		}

		/**
		 * Add social media links to plugin screen.
		 *
		 * @since 1.0.1
		 *
		 * @param array $links Plugin action links.
		 * @return array $links Amended array of links to display.
		 */
		public function add_social_links( $links ) {

			$siteLink = 'https://easymetabuilder.com/';
			$twitterStatus = sprintf( __( 'Check out %1 from @EasyMetaBuilder %2', 'easy-meta-builder' ), $this->name, $siteLink );

			array_push( $links, '<a title="' . __( 'Spread the word!', 'easy-meta-builder' ) . '" href="https://www.facebook.com/sharer/sharer.php?u=' . urlencode( $siteLink ) . '" target="_blank" class="dashicons-before dashicons-facebook-alt"></a>' );
			array_push( $links, '<a title="' . __( 'Spread the word!', 'easy-meta-builder' ) . '" href="https://twitter.com/home?status=' . urlencode( $twitterStatus ) . '" target="_blank" class="dashicons-before dashicons-twitter"></a>' );
			array_push( $links, '<a title="' . __( 'Spread the word!', 'easy-meta-builder' ) . '" href="https://plus.google.com/share?url=' . urlencode( $siteLink ) . '" target="_blank" class="dashicons-before dashicons-googleplus"></a>' );

			return $links;
		}

		/**
		 * Magic getter for our object.
		 *
		 * @since 1.0.0
		 *
		 * @throws Exception Throws an exception if the field is invalid.
		 *
		 * @param string $field Field to get.
		 * @return mixed
		 */
		public function __get( $field ) {
			switch ( $field ) {
				case 'version':
					return self::VERSION;
				case 'basename':
				case 'url':
				case 'path':
					return $this->$field;
				default:
					throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
			}
		}

		/**
		 * Include a file from the includes directory.
		 *
		 * @since 1.0.0
		 *
		 * @param string $filename Name of the file to be included.
		 * @return bool Result of include call.
		 */
		public static function include_file( $filename ) {
			$file = self::dir( 'classes/class-' . $filename . '.php' );
			if ( file_exists( $file ) ) {
				return include_once( $file );
			}
			return false;
		}

		/**
		 * This plugin's directory.
		 *
		 * @since 1.0.0
		 *
		 * @param string $path (optional) appended path.
		 * @return string Directory and path.
		 */
		public static function dir( $path = '' ) {
			static $dir;
			$dir = $dir ? $dir : trailingslashit( dirname( __FILE__ ) );
			return $dir . $path;
		}

		/**
		 * This plugin's url.
		 *
		 * @since 1.0.0
		 *
		 * @param string $path (optional) appended path.
		 * @return string URL and path.
		 */
		public static function url( $path = '' ) {
			static $url;
			$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );
			return $url . $path;
		}
	}

	/**
	 * Grab the EasyMetaBuilder object and return it.
	 *
	 * Wrapper for EasyMetaBuilder::get_instance().
	 *
	 * @since 1.0.0
	 *
	 * @return EasyMetaBuilder Singleton instance of plugin class.
	 */
	function easymetabuilder() {
		return EasyMetaBuilder::get_instance();
	}

	// Kick it off.
	add_action( 'plugins_loaded', array( easymetabuilder(), 'hooks' ) );

	register_activation_hook( __FILE__, array( easymetabuilder(), '_activate' ) );
	register_deactivation_hook( __FILE__, array( easymetabuilder(), '_deactivate' ) );

endif;
