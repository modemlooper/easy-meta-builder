<?php
/**
 * EasyMetaBuilder_Field_Types Class File.
 *
 * @package EasyMetaBuilder
 * @subpackage EasyMetaBuilderFieldTypes
 * @author Easy Meta Builder
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Meta Builder Field Types class.
 *
 * @since 1.0.0
 */
class EasyMetaBuilder_Field_Types {

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
	 * @var object EasyMetaBuilder_Field_Types
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
		add_filter( 'easymetabuilder_process_meta', array( $this, 'process_meta' ), 15, 3 );
		add_action( 'add_field_options', array( $this, 'field_options' ), 10, 4 );
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
				case 'taxonomy_select':
					$cmb->add_group_field( $group_field_id, array(
						'name' => __( 'Taxonomy', 'easy-meta-builder' ),
						'id'   => $prefix . $type . '_taxonomy',
						'row_classes' => $type . ' hidden field-option',
						'type' => 'select',
						'options' => get_taxonomies( array( 'public' => true ) ),
					) );
				break;
				case 'radio':
					$cmb->add_group_field( $group_field_id, array(
						'name' => __( 'Radio Options', 'easy-meta-builder' ),
						'id'   => $prefix . $type . '_options',
						'desc' => __( 'Each comma seperated string will be added as an radio option.', 'easy-meta-builder' ),
						'row_classes' => $type . ' hidden field-option',
						'type' => 'text',
					) );
				break;
				case 'select':
					$cmb->add_group_field( $group_field_id, array(
						'name' => __( 'Select Options', 'easy-meta-builder' ),
						'id'   => $prefix . $type . '_options',
						'desc' => __( 'Each comma seperated string will be added as an select option.', 'easy-meta-builder' ),
						'row_classes' => $type . ' hidden field-option',
						'type' => 'text',
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

		if ( ! isset( $metabox_fields[ $field ]['_easymetabuilder_type'] ) ) {
			return $field_args;
		}

		switch ( $metabox_fields[ $field ]['_easymetabuilder_type'] ) {

			case 'taxonomy_select':
				$field_args['taxonomy'] = isset( $metabox_fields[ $field ]['_easymetabuilder_taxonomy_select_taxonomy'] ) ? $metabox_fields[ $field ]['_easymetabuilder_taxonomy_select_taxonomy'] : '';
			break;
			case 'radio':
				if ( isset( $metabox_fields[ $field ]['_easymetabuilder_radio_options'] ) ) {
					$options = explode( ',', $metabox_fields[ $field ]['_easymetabuilder_radio_options'] );
					$arr = array();
					foreach ( $options as $option => $value ) {
						$arr[ trim( $value ) ] = trim( $value );
					}
				}
				$field_args['options'] = $arr;
			break;
			case 'radio_inline':
				if ( isset( $metabox_fields[ $field ]['_easymetabuilder_radio_inline_options'] ) ) {
					$options = explode( ',', $metabox_fields[ $field ]['_easymetabuilder_radio_inline_options'] );
					$arr = array();
					foreach ( $options as $option => $value ) {
						$arr[ trim( $value ) ] = trim( $value );
					}
				}
				$field_args['options'] = $arr;
			break;
			case 'multicheck':
				if ( isset( $metabox_fields[ $field ]['_easymetabuilder_multicheck_options'] ) ) {
					$options = explode( ',', $metabox_fields[ $field ]['_easymetabuilder_multicheck_options'] );
					$arr = array();
					foreach ( $options as $option => $value ) {
						$arr[ trim( $value ) ] = trim( $value );
					}
				}
				$field_args['options'] = $arr;
			break;
			case 'select':
				if ( isset( $metabox_fields[ $field ]['_easymetabuilder_select_options'] ) ) {
					$options = explode( ',', $metabox_fields[ $field ]['_easymetabuilder_select_options'] );
					$arr = array();
					foreach ( $options as $option => $value ) {
						$arr[ trim( $value ) ] = trim( $value );
					}
				}
				$field_args['options'] = $arr;
			break;
		}

		$field_args = apply_filters( 'easymetabuiler_process_meta', $field_args );

		return $field_args;

	}
}
