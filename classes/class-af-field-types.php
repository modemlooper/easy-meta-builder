<?php
/**
 * EasyMetaBuilder_AF_Field_Types Class File.
 *
 * @package EasyMetaBuilderAFFieldTypes
 * @subpackage EasyMetaBuilder
 * @author Easy Meta Builder
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'EasyMetaBuilder_AF_Field_Types' ) ) {

	/**
	 * Easy Meta Builder Field Types class.
	 *
	 * @since 1.0.0
	 */
	class EasyMetaBuilder_AF_Field_Types {

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
		 * @var object EasyMetaBuilder_AF_Field_Types
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
		 * @return void
		 */
		public function hooks() {
			add_action( 'init', array( $this, 'init' ) );
			add_filter( 'easymetabuilder_get_field_types', array( $this, 'field_types' ) );
			add_filter( 'easymetabuilder_process_meta', array( $this, 'process_meta' ), 10, 3 );
			add_action( 'add_field_options', array( $this, 'field_options' ), 10, 4 );
		}


		/**
		 * Set it off!
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function init() {
		}


		/**
		 * Get field types
		 *
		 * @param array $allowed_types cmb2 field types.
		 * @since 1.0.0
		 * @return array
		 */
		public function field_types( $allowed_types ) {

			$field_types = array(
				'colorpicker' => __( 'Color Picker', 'easymetabuilder' ),
				'text_time' => __( 'Time Picker', 'easymetabuilder' ),
				'text_date' => __( 'Date Picker', 'easymetabuilder' ),
				'radio_inline' => __( 'Radio Inline', 'easymetabuilder' ),
				'taxonomy_select' => __( 'Taxonomy Select', 'easymetabuilder' ),
				'taxonomy_radio' => __( 'Taxonomy Radio', 'easymetabuilder' ),
				'taxonomy_radio_inline' => __( 'Taxonomy Radio Inline', 'easymetabuilder' ),
				'taxonomy_multicheck' => __( 'Taxonomy Muilticheck', 'easymetabuilder' ),
				'multicheck' => __( 'Muilticheck', 'easymetabuilder' ),
				'dashicon_radio' => __( 'Dashicon Radio', 'easymetabuilder' ),
				'file_list' => __( 'File list', 'easymetabuilder' ),
				'oembed' => __( 'oEmbed', 'easymetabuilder' ),
				'text_date_timestamp' => __( 'Date Picker (UNIX timestamp)', 'easymetabuilder' ),
				'text_datetime_timestamp' => __( 'Text Date/Time Picker Combo (UNIX timestamp)', 'easymetabuilder' ),
				'text_datetime_timestamp_timezone' => __( 'Text Date/Time Picker/Time zone', 'easymetabuilder' ),
				'select_timezone' => __( 'Time Zone Dropdown', 'easymetabuilder' ),
			);

			foreach ( $field_types as $type => $value ) {
				$allowed_types[ $type ] = $value;
			}

			return apply_filters( 'easymetabuilder_advanced_field_types', $allowed_types );
		}

		/**
		 * Field type options
		 *
		 * @param object $cmb            cmb2 object.
		 * @param string $group_field_id metabox id.
		 * @param string $prefix        field id prefix.
		 * @param array  $types          field types.
		 * @since 1.0.0
		 * @return void
		 */
		public function field_options( $cmb, $group_field_id, $prefix, $types ) {

			foreach ( $types as $type => $value ) {
				switch ( $type ) {
					case 'colorpicker':
						$cmb->add_group_field( $group_field_id, array(
							'name' => __( 'Default Color', 'easymetabuilder' ),
							'id'   => $prefix . $type . '_default',
							'desc' => __( 'Sets the default color of the picker.', 'easymetabuilder' ),
							'row_classes' => $type . ' hidden field-option',
							'type' => 'colorpicker',
						) );

						$cmb->add_group_field( $group_field_id, array(
							'name' => __( 'Palette', 'easymetabuilder' ),
							'id'   => $prefix . $type . '_palatte',
							'desc' => __( 'Each comma seperated hex will be added as a palatte below the picker.', 'easymetabuilder' ),
							'row_classes' => $type . ' hidden field-option',
							'attributes' => array(
								'placeholder' => '#ffffff, #000000',
							),
							'type' => 'text',
						) );
					break;
					case 'radio_inline':
						$cmb->add_group_field( $group_field_id, array(
							'name' => __( 'Radio Options', 'easymetabuilder' ),
							'id'   => $prefix . $type . '_options',
							'desc' => __( 'Each comma seperated string will be added as an radio option.', 'easymetabuilder' ),
							'row_classes' => $type . ' hidden field-option',
							'type' => 'text',
						) );
					break;
					case 'multicheck':
						$cmb->add_group_field( $group_field_id, array(
							'name' => __( 'Multicheck Options', 'easymetabuilder' ),
							'id'   => $prefix . $type . '_options',
							'desc' => __( 'Each comma seperated string will be added as an multicheck option.', 'easymetabuilder' ),
							'row_classes' => $type . ' hidden field-option',
							'type' => 'text',
						) );
					break;
					case 'taxonomy_radio':
						$cmb->add_group_field( $group_field_id, array(
							'name' => __( 'Taxonomy', 'easymetabuilder' ),
							'id'   => $prefix . $type . '_taxonomy',
							'row_classes' => $type . ' hidden field-option',
							'type' => 'select',
							'options' => get_taxonomies( array( 'public' => true ) ),
						) );
					break;
					case 'taxonomy_radio_inline':
						$cmb->add_group_field( $group_field_id, array(
							'name' => __( 'Taxonomy', 'easymetabuilder' ),
							'id'   => $prefix . $type . '_taxonomy',
							'row_classes' => $type . ' hidden field-option',
							'type' => 'select',
							'options' => get_taxonomies( array( 'public' => true ) ),
						) );
					break;
					case 'taxonomy_multicheck':
						$cmb->add_group_field( $group_field_id, array(
							'name' => __( 'Taxonomy', 'easymetabuilder' ),
							'id'   => $prefix . $type . '_taxonomy',
							'row_classes' => $type . ' hidden field-option',
							'type' => 'select',
							'options' => get_taxonomies( array( 'public' => true ) ),
						) );
					break;
				}
			}
		}

		/**
		 * Process fields meta
		 *
		 * @param array  $field_args     cmb2 add field args.
		 * @param array  $metabox_fields metabox fields data.
		 * @param string $field current field id.
		 * @since 1.0.0
		 * @return array
		 */
		public function process_meta( $field_args, $metabox_fields, $field ) {

			switch ( $metabox_fields[ $field ]['_easymetabuilder_type'] ) {
				case 'colorpicker':
					if ( ! empty( $metabox_fields[ $field ]['_easymetabuilder_colorpicker_default'] ) ) {
						$field_args['default'] = $metabox_fields[ $field ]['_easymetabuilder_colorpicker_default'];
					}

					if ( ! empty( $metabox_fields[ $field ]['_easymetabuilder_colorpicker_palatte'] ) ) {
						$field_args['attributes'] = array(
							'data-colorpicker' => wp_json_encode( array(
								'palettes' => explode( ',', $metabox_fields[ $field ]['_easymetabuilder_colorpicker_palatte'] ),
							) ),
						);
					}
				break;
				case 'radio_inline':
					$field_args['options'] = explode( ',', $metabox_fields[ $field ]['_easymetabuilder_radio_inline_options']
					);
				break;
				case 'multicheck':
					$field_args['options'] = explode( ',', $metabox_fields[ $field ]['_easymetabuilder_multicheck_options']
					);
				break;
				case 'taxonomy_radio':
					$field_args['taxonomy'] = $metabox_fields[ $field ]['_easymetabuilder_taxonomy_radio_taxonomy'];
				break;
				case 'taxonomy_radio_inline':
					$field_args['taxonomy'] = $metabox_fields[ $field ]['_easymetabuilder_taxonomy_radio_inline_taxonomy'];
				break;
				case 'taxonomy_multicheck':
					$field_args['taxonomy'] = $metabox_fields[ $field ]['_easymetabuilder_taxonomy_multicheck_taxonomy'];
				break;
			}

			$field_args = apply_filters( 'easymetabuiler_process_advanced_meta', $field_args );

			return $field_args;

		}
	}
}
