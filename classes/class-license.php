<?php
/**
 * EasyMetaBuilder Licnese.
 *
 * @package EasyMetaBuilder
 * @subpackage EasyMetaBuilderLicnese
 * @author Easy Meta Builder
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * License initiation class.
 *
 * @since 1.0.0
 *
 * @var string $plugin  Parent class.
 * @var string $instance This class.
 */
class EasyMetaBuilder_License {

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
	 * @var object EasyMetaBuilder_License
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
		add_action( 'admin_menu', array( $this, 'easymetabuilder_license_menu' ), 999 );
		add_action( 'admin_init', array( $this, 'easymetabuilder_save_option' ) );
	}


	/**
	 * Adds license sub menu item.
	 *
	 * @return void
	 */
	function easymetabuilder_license_menu() {
		add_submenu_page( 'edit.php?post_type=easymetabuilder', __( 'Easy Meta Builder Licenses', 'easymetabuilder' ), __( 'Licenses', 'easymetabuilder' ), 'manage_options', 'easy-meta-builder-license', array( $this, 'easymetabuilder_license_page' ) );
	}


	/**
	 * License page markup
	 *
	 * @return void
	 */
	public function easymetabuilder_license_page() {
		?>
		<div class="wrap wrap-licenses">
			<h2><?php esc_attr_e( 'Easy Meta Builder Licenses', 'easymetabuilder' ); ?></h2>
			<p><?php esc_attr_e( 'Enter your extension license keys here to receive updates for purchased extensions. If your license key has expired, please renew your license.', 'easymetabuilder' ); ?> </p>
			<form method="post" action="">

				<?php settings_fields( 'easy_meta_builder_license' ); ?>

				<table class="form-table">
					<tbody>
					<?php

					$license_fields = apply_filters( 'easymetabuilder_license_fields', array() );

					foreach ( $license_fields as $field => $value ) {

						$product_license = get_option( $value['id'] );
						$status = get_option( $value['id'] . '_active' );
						?>
						<tr valign="top">
							<th scope="row" valign="top">
							<?php echo esc_attr( $value['name'] ); ?>
							</th>
							<td>
								<input id="<?php echo esc_attr( $value['id'] ); ?>" name="<?php echo esc_attr( $value['id'] ); ?>" type="text" class="regular-text" value="<?php echo esc_attr( $product_license ); ?>" placeholder="<?php esc_attr_e( 'Enter your license key', 'easymetabuilder' ); ?>" />

								<?php if ( false !== $product_license ) { ?>
									<?php if ( false !== $status && 'valid' === $status ) { ?>
										<input type="submit" class="button-secondary" name="<?php echo esc_attr( $value['id'] ); ?>_deactivate" value="<?php esc_attr_e( 'Deactivate License', 'easymetabuilder' ); ?>"/>
										<span style="color:green;"><?php esc_attr_e( 'active', 'easymetabuilder' ); ?></span>
									<?php } else { ?>
										<input type="submit" class="button-secondary" name="<?php echo esc_attr( $value['id'] ); ?>_activate" value="<?php esc_attr_e( 'Activate License', 'easymetabuilder' ); ?>"/>
									<?php } ?>
								<?php } ?>
								<div class="edd-license-data edd-license-valid license-expiration-date-notice"><p><?php echo esc_attr( $this->easymetabuilder_license_expiration( $value['license_data'] ) ); ?></p></div>
							</td>
						</tr>
					<?php } ?>

					</tbody>
				</table>
				<?php wp_nonce_field( 'easy_meta_builder_nonce', 'easy_meta_builder_nonce' ); ?>
				<?php submit_button( 'Save Licenses', 'primary', 'easymetabuilder-save-options' ); ?>

			</form>
		<?php
	}

	/**
	 * Returns a string of date or time when license expires
	 *
	 * @param  array $license_data EDD license data.
	 * @return string
	 */
	public function easymetabuilder_license_expiration( $license_data = array() ) {

		if ( empty( $license_data ) ) {
			return;
		}

		switch ( $license_data->expires ) {

			case 'lifetime' :
				$message = __( 'License key never expires.', 'easymetabuilder' );
			break;
			default:
				$now = current_time( 'timestamp' );
				$expiration = strtotime( $license_data->expires, current_time( 'timestamp' ) );

				if ( $expiration > $now && $expiration - $now < ( DAY_IN_SECONDS * 30 ) ) {

					$message = sprintf(
						__( 'Your license key expires soon! It expires on %1. <a href="%2" target="_blank">Renew your license key</a>.', 'easymetabuilder' ),
						date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) ),
						'https://easymetabuilder.com/checkout/?edd_license_key=' . $license_data->license_key . '&utm_campaign=admin&utm_source=licenses&utm_medium=renew'
					);

				} else {

					$message = sprintf(
						__( 'Your license key expires on %s.', 'easymetabuilder' ),
						date_i18n( get_option( 'date_format' ), $expiration )
					);

				}
			break;

		}

		return $message;
	}

	/**
	 * Gets fields from POST and send to sanitizing and save
	 *
	 * @return void
	 */
	public function easymetabuilder_save_option() {

		if ( ! isset( $_POST['easymetabuilder-save-options'] ) ) {
			return;
		}

		if ( ! isset( $_POST['easy_meta_builder_nonce'] ) || ! wp_verify_nonce( sanitize_title( $_POST['easy_meta_builder_nonce'] ), 'easy_meta_builder_nonce' ) ) {
			return;
		}

		foreach ( $_POST as $key => $value ) {
			if ( 'emb_' === substr( $key, 0, 4 ) ) {
				$this->easymetabuilder_sanitize_option( $key, $value );
			}
		}
	}


	/**
	 * Checks option exists either saves or deletes
	 *
	 * @param  mixed $key new option key.
	 * @param  mixed $value new option value.
	 * @return mixed      updated option data
	 */
	public function easymetabuilder_sanitize_option( $key, $value ) {

		if ( empty( $value ) ) {
			$new = delete_option( $key );
			delete_option( $key . '_license_active' );
			delete_option( $key . '_license_key_active' );
			delete_option( $key . '_license_key_data' );
		} else {
			$new = update_option( $key, sanitize_text_field( $value ) );
		}
		return $new;
	}
}
