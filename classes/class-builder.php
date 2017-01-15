<?php
/**
 * EasyMetaBuilder_Builder Class File.
 *
 * @package EasyMetaBuilder
 * @subpackage EasyMetaBuilderBuilder
 * @author Easy Meta Builder
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Easy Meta Builder class.
 *
 * @since 1.0.0
 */
class EasyMetaBuilder_Builder {

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
	 * @var object EasyMetaBuilder_Builder
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
		add_action( 'cmb2_init', array( $this, 'builder_metabox' ) );
		add_action( 'cmb2_init', array( $this, 'process_meta' ) );
	}


	/**
	 * Set it off!
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {
		add_action( 'emb_after_field', array( $this, 'data_field_options' ) );
		add_filter( 'options_data_field_options_filter', array( $this, 'template_tag_data_field' ), 10, 2 );
	}

	/**
	 * Add builder metabox to Meta CPT
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function builder_metabox() {

		$prefix = '_easymetabuilder_';

		$cmb_options = new_cmb2_box( array(
			'id' => $prefix . 'options_metabox',
			'title' => __( 'Options', 'easy-meta-builder' ),
			'object_types' => array( 'easymetabuilder' ),
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => true,
			'classes' => array( 'easymetabuilder', 'options' ),
		) );

		$cmb_options->add_field( array(
			'name' => 'Post Type',
			'desc' => __( 'Choose a post type this metabox will be added to.', 'easy-meta-builder' ),
			'id'   => $prefix . 'post_type',
			'type' => 'select',
			'show_option_none' => false,
			'default' => 'post',
			'options' => $this->get_post_types(),
		) );

		$object_types = apply_filters( 'easymetabuilder_object_types_filter', array( 'easymetabuilder' ) );

		/**
		 * Initiate the fields metabox
		 */
		$cmb = new_cmb2_box( array(
			'id' => $prefix . 'fields_metabox',
			'title' => __( 'Fields', 'easy-meta-builder' ),
			'object_types' => $object_types,
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => true,
			'classes' => array( 'easymetabuilder', 'fields' ),
		) );

		$group_field_id = $cmb->add_field( array(
			'id' => $prefix . 'fields_repeat_group',
			'type' => 'group',
			'description' => __( 'Choose fields to add to this metabox. Some field types may have additional options that are shown after selecting a field type.', 'easy-meta-builder' ),
			'options' => array(
			'group_title' => __( 'Field {#}', 'easy-meta-builder' ),
			'add_button' => __( 'Add Another Field', 'easy-meta-builder' ),
			'remove_button' => __( 'Remove Field', 'easy-meta-builder' ),
			'sortable' => true,
			),
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name' => 'ID',
			'id'   => $prefix . 'id',
			'desc' => __( 'ID is required for field to be included in metabox. No Spaces use underscore.', 'easy-meta-builder' ),
			'type' => 'text',
			'attributes'  => array(
				'required'    => 'required',
				'placeholder' => 'field_name_id',
			),
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name' => 'Name',
			'id'   => $prefix . 'name',
			'type' => 'text',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name' => 'Description',
			'id'   => $prefix . 'description',
			'type' => 'textarea_small',
		) );

		// $cmb->add_group_field( $group_field_id, array(
		// 	'name' => 'Required',
		// 	'id'   => $prefix . 'required',
		// 	'desc' => __( 'Make this field required.', 'easy-meta-builder' ),
		// 	'type' => 'checkbox',
		// ) );

		$cmb->add_group_field( $group_field_id, array(
			'name' => 'Type',
			'desc' => __( 'Choose a field type.', 'easy-meta-builder' ),
			'id'   => $prefix . 'type',
			'type' => 'select',
			'row_classes' => ' typeselect',
			'show_option_none' => false,
			'default' => 'text',
			'options' => $this->get_field_types(),
		) );

		/**
		 * Hook to add extra field options. These options are hidden and shown when a type is selected.
		 *
		 * @since 1.0.0
		 * @hook add_field_options
		 * @param object $cmb CMB2 field object.
		 * @param string $group_field_id ID of CMB2 metabox.
		 * @param string $prefix field ID prefix.
		 * @param array $this->get_field_types() allowed CMB2 field types.
		 */
		do_action( 'add_field_options', $cmb, $group_field_id, $prefix, $this->get_field_types() );

	}


	/**
	 * Process Meta. Get Meta CPT data a builds out a meta box for the post type.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function process_meta() {

		$prefix = '_emb_';

		$args = array(
			'post_type' => 'easymetabuilder',
			'post_status' => 'publish',
		);

		$query = new WP_Query( $args );

		foreach ( $query->posts as $post ) {

			$post_type = get_post_meta( $post->ID, '_easymetabuilder_post_type', true );
			$post_fields_meta = get_post_meta( $post->ID, '_easymetabuilder_fields_repeat_group', true );
			$metabox_fields = $post_fields_meta ? $post_fields_meta : array();
			$metabox_edit_link = emb_user_can_access() ? '   <a href="' . get_edit_post_link( $post->ID ) . '" style="text-decoration:none;display:inline-block; margin-left:5px;">edit</a>' : '';

			$meta_box_args = array(
				'id' => $prefix . $post->ID . '_metabox',
				'title' => $post->post_title . $metabox_edit_link,
				'object_types' => apply_filters( 'easymetabuilder_process_meta_object_types', array( $post_type ) ),
				'context' => 'normal',
				'priority' => 'high',
				'show_names' => true,
				'classes' => array( 'easymetabuilder', 'field-output', 'options' ),
			);

			/**
			 * Filters CMB2 new_cmb2_box args.
			 *
			 * @param array $meta_box_args
			 */
			$meta_box_args = apply_filters( 'easymetabuilder_process_meta_metabox', $meta_box_args );

			$cmb = new_cmb2_box( $meta_box_args );

			foreach ( $metabox_fields as $field => $value ) {

				$field_id = array_key_exists( '_easymetabuilder_id', $metabox_fields[ $field ] ) ? str_replace( ' ', '_', strtolower( $metabox_fields[ $field ]['_easymetabuilder_id'] ) ) : '';
				$field_name = array_key_exists( '_easymetabuilder_name', $metabox_fields[ $field ] ) ? $metabox_fields[ $field ]['_easymetabuilder_name'] : ' ';
				$field_type = array_key_exists( '_easymetabuilder_type', $metabox_fields[ $field ] ) ? $metabox_fields[ $field ]['_easymetabuilder_type'] : '';
				$field_description = array_key_exists( '_easymetabuilder_description', $metabox_fields[ $field ] ) ? $metabox_fields[ $field ]['_easymetabuilder_description'] : '';
				$field_required = array_key_exists( '_easymetabuilder_required', $metabox_fields[ $field ] ) ? $metabox_fields[ $field ]['_easymetabuilder_required'] : '';

				$field_args = array(
					'name' => $field_name,
					'id'   => $prefix . $field_id,
					'type' => $field_type,
					'before_field' => array( $this, 'before_field' ),
					'after_field' => array( $this, 'after_field' ),
				);

				if ( ! empty( $field_description ) ) {
					$field_args['desc'] = $field_description;
				}

				// if ( ! empty( $field_required ) ) {
				// 	$field_args['attributes'] = array(
				// 		'required' => 'required',
				// 	);
				// }

				/**
				 * Filter for cmb2 field arguments.
				 *
				 * @since 1.0.0
				 * @param array $field_args cmb2 add field args.
				 * @param array $metabox_fields metabox fields data.
				 * @param string $field current field id.
				 */
				$field_args = apply_filters( 'easymetabuilder_process_meta', $field_args, $metabox_fields, $field, $field_id );

				if ( ! empty( $field_id ) ) {
					$cmb->add_field( $field_args );
				}
			}
		}

	}

	/**
	 * Get post types
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_post_types() {
		$post_types = get_post_types( array( 'public' => true ) );
		$disallowed_types = array( 'revision', 'attachment', 'nav_menu_item', 'easymetabuilder' );
		$post_types = array_diff( $post_types, $disallowed_types );
		$post_types['user'] = __( 'user profile', 'easy-meta-builder' );

		return apply_filters( 'easymetabuilder_get_post_types', $post_types );
	}

	/**
	 * Get field types
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_field_types() {

		$field_types = array(
			'title'	 => __( 'Title', 'easy-meta-builder' ),
			'text' => __( 'Text Input', 'easy-meta-builder' ),
			'text_small' => __( 'Text Input Small', 'easy-meta-builder' ),
			'text_medium' => __( 'Text Input Medium', 'easy-meta-builder' ),
			'text_email' => __( 'Text Input Email', 'easy-meta-builder' ),
			'text_url' => __( 'Text Input Url', 'easy-meta-builder' ),
			'text_money' => __( 'Text Input Money', 'easy-meta-builder' ),
			'textarea'   => __( 'Textarea', 'easy-meta-builder' ),
			'textarea_small'   => __( 'Textarea Small', 'easy-meta-builder' ),
			'textarea_code'   => __( 'Textarea Code', 'easy-meta-builder' ),
			'radio' => __( 'Radio', 'easy-meta-builder' ),
			'select' => __( 'Select', 'easy-meta-builder' ),
			'checkbox' => __( 'Checkbox', 'easy-meta-builder' ),
			'file' => __( 'File Upload', 'easy-meta-builder' ),
			'wysiwyg' => __( 'WYSIWYG Editor', 'easy-meta-builder' ),
		);

		/**
		 * Filters the types of CMB2 fields
		 *
		 * @since 1.0.0
		 * @param array $field_types unfilterd CMB2 field types
		 */
		return apply_filters( 'easymetabuilder_get_field_types', $field_types );
	}

	/**
	 * Get post type taxomomies
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function add_taxomonomies_json() {

		$tax_array = array();

		foreach ( $this->get_post_types() as $post_type  ) {
			$tax_obj = get_object_taxonomies( $post_type, 'object' );

			foreach ( $tax_obj as $object ) {
				$tax_array['taxonomies'][ $post_type ][ $object->name ] = $object->label;
			}
		}

		return $tax_array;

	}

	/**
	 * CMB2 before field callback
	 *
	 * @param  array $field cmb2 field data.
	 * @return void
	 */
	public function before_field( $field ) {
		do_action( 'emb_before_field', $field );
	}

	/**
	 * CMB2 after field callback
	 *
	 * @param  array $field cmb2 field data.
	 * @return void
	 */
	public function after_field( $field ) {
		do_action( 'emb_after_field', $field );
	}


	/**
	 * Show shortcode data after meta field
	 *
	 * @since 1.0.0
	 * @param array $field cmb2 field data.
	 */
	public function data_field_options( $field ) {

		$field_options = apply_filters( 'options_data_field_options_filter', array(), $field );

		if ( ! empty( $field_options ) ) {

			/**
			 * Filters the CMB2 field data toggle
			 *
			 * @since 1.0.0
			 * @param boolean
			 */
			$toggle_filter = apply_filters( 'option_data_field_toggle_filter', false, $field );

			$disallowed_types = array( 'title' );

			if ( in_array( $field['type'], $disallowed_types, true ) || $toggle_filter ) {
				return false;
			}

			/**
			 * Filters the CMB2 field data
			 *
			 * @since 1.0.0
			 * @param array $field unfilterd CMB2 field data
			 */
			$field = apply_filters( 'option_data_field_filter', $field );

			if ( ! empty( $field ) && 'user' === $field['render_row_cb'][0]->object_type ) {
				return;
			}

			?>
			<span class="emb-field-data" data-id="<?php echo esc_attr( $field['id'] ); ?>">
				<div class="emb-field-data-menu">
					<ul>
						<?php
						foreach ( $field_options as $option => $value ) {
							echo '<li>' . esc_attr( $value ) . '</li>';
						}
						?>
					</ul>
				</div>
			</span>
			<?php
		}
	}

	/**
	 * Add template tag to options
	 *
	 * @since 1.0.0
	 * @param array $options field options added to toggle.
	 * @param array $field cmb2 field data.
	 */
	public function template_tag_data_field( $options = array(), $field ) {

		$options['template_tag'] = 'Template: <?php emb_meta( "' . esc_attr( $field['id'] ) . '" ); ?>';

		return $options;

	}

}
