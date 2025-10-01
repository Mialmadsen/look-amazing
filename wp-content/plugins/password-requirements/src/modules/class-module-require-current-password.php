<?php
/**
 * Update the User Profile screen to require the current password when changing the password
 *
 * This feature is built based on Automattic's "VIP-Go-Mu-Plugins" repository,
 * slightly adjusted to fit this plugin's structure and settings system.
 *
 * @see https://github.com/Automattic/vip-go-mu-plugins/blob/6d2c6a69e1ec81cca5b5263fbf9e851aa3c33b1b/security/password.php
 *
 * @package Teydea_Studio\Password_Requirements
 */

namespace Teydea_Studio\Password_Requirements\Modules;

use Teydea_Studio\Password_Requirements\Compliance;
use Teydea_Studio\Password_Requirements\Dependencies\Utils;
use WP_Error;
use WP_User;

/**
 * The "Module_Require_Current_Password" class
 */
final class Module_Require_Current_Password extends Utils\Module {
	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		// Skip if Automattic's VIP code is already present to not duplicate the functionality.
		if ( function_exists( 'Automattic\VIP\Security\validate_current_password' ) ) {
			return;
		}

		// Add the "current password" field to the User Profile screen, if enabled.
		add_action( 'show_user_profile', [ $this, 'add_current_password_field' ] );

		// Validate current password in submitted user profile.
		add_action( 'user_profile_update_errors', [ $this, 'validate_current_password' ], 1, 3 );
	}

	/**
	 * Add the "current password" field to the User Profile screen, if enabled
	 *
	 * @param WP_User $user The user object.
	 *
	 * @return void
	 */
	public function add_current_password_field( WP_User $user ): void {
		$compliance = new Compliance( $this->container, $user );

		if ( false === $compliance->is_current_password_required() ) {
			return;
		}

		?>
		<script>
			document.addEventListener( 'DOMContentLoaded', function() {
				// Reposition input inside "Set New Password" section.
				const el = document.getElementById( 'current-password-confirm' );
				const parent = document.getElementsByClassName( 'wp-pwd' )[ 0 ];

				parent.insertBefore( el, parent.firstChild );
				el.style.marginBottom = '1em';

				// Hide no JS fallback UI
				const nojsParent = document.getElementById( 'nojs-current-pass' );
				nojsParent.style.display = 'none';
			} );
		</script>
		<table id="nojs-current-pass" class="form-table" role="presentation">
			<tr>
				<th><label for="current_pass"><?php echo esc_html( __( 'Current Password', 'password-requirements' ) ); ?></label></th>
				<td>
					<div id="current-password-confirm">
						<input type="password" name="current_pass" id="current_pass" placeholder="<?php esc_attr_e( 'Current Password', 'password-requirements' ); ?>" class="regular-text" value="" autocomplete="off" />
						<p class="description"><?php echo esc_html( __( 'Please type your current password to update it.', 'password-requirements' ) ); ?></p>
					</div>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Validate current password in submitted user profile
	 *
	 * @param WP_Error       $errors      Error object passed by reference.
	 * @param bool           $update      Whether this is a user update.
	 * @param object{ID:int} $user_object User object passed by reference.
	 *
	 * @return void
	 */
	public function validate_current_password( WP_Error &$errors, bool $update, &$user_object ): void {
		if ( ! $update ) {
			return;
		}

		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( ! $screen || 'profile' !== $screen->id ) {
			return;
		}

		$user = get_user_by( 'ID', $user_object->ID );

		if ( ! $user instanceof WP_User ) {
			return;
		}

		check_admin_referer( sprintf( 'update-user_%d', $user->ID ) );

		if ( empty( $_POST['pass1'] ) ) {
			return;
		}

		$compliance = new Compliance( $this->container, $user );

		if ( false === $compliance->is_current_password_required() ) {
			return;
		}

		if ( empty( $_POST['current_pass'] ) ) {
			$errors->add(
				'empty_current_password',
				__( '<strong>Error</strong>: Please enter your current password.', 'password-requirements' ),
				[ 'form-field' => 'current_pass' ],
			);

			return;
		}

		/**
		 * Note we can not use the "sanitize_text_field" in this case as this would falsify
		 * the compliance checks due to stripped/changed characters.
		 *
		 * For example:
		 * - "pas&"<>word" passed through the "sanitize_text_field" function would become "pas&"word" (note missing angular brackets)
		 */
		$auth = wp_authenticate( $user->user_login, $_POST['current_pass'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- reason described above.

		if ( is_wp_error( $auth ) ) {
			$errors->add(
				'wrong_current_password',
				__( '<strong>Error</strong>: The entered current password is not correct. Password has not been changed.', 'password-requirements' ),
				[ 'form-field' => 'current_pass' ],
			);
		}
	}
}
