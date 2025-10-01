<?php
/**
 * Ensure that user password is compliant with applying password policy
 * when user is logging in
 *
 * @package Teydea_Studio\Password_Requirements
 */

namespace Teydea_Studio\Password_Requirements\Modules;

use Teydea_Studio\Password_Requirements\Dependencies\Utils;
use Teydea_Studio\Password_Requirements\Compliance;
use WP_Error;
use WP_User;

/**
 * The "Module_Compliance_On_Login" class
 */
final class Module_Compliance_On_Login extends Utils\Module {
	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		// Render custom nonce field on login form.
		add_action( 'login_form', [ $this, 'render_nonce_field' ] );

		// Check if current password is compliant with the password policy when user is logging in.
		add_filter( 'login_redirect', [ $this, 'on_login' ], 2, 3 );

		// Filter the login message.
		add_filter( 'login_message', [ $this, 'filter_login_message' ] );
	}

	/**
	 * Render custom nonce field on login form
	 *
	 * @return void
	 */
	public function render_nonce_field(): void {
		$nonce = new Utils\Nonce( $this->container, 'login' );
		$nonce->render_field();
	}

	/**
	 * Check if current password is compliant with the password policy when user is logging in
	 *
	 * @param string           $redirect_to           The redirect destination URL.
	 * @param string           $requested_redirect_to The requested redirect destination URL passed as a parameter.
	 * @param WP_User|WP_Error $user                  WP_User object if login was successful, WP_Error object otherwise.
	 *
	 * @return string Updated redirect destination URL.
	 */
	public function on_login( string $redirect_to, string $requested_redirect_to, $user ): string {
		// Predefine the variable's value.
		$password = null;

		// Get the nonce data.
		$nonce        = new Utils\Nonce( $this->container, 'login' );
		$nonce_value  = isset( $_POST[ $nonce->get_key() ] ) ? sanitize_text_field( Utils\Type::ensure_string( wp_unslash( $_POST[ $nonce->get_key() ] ) ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$nonce_action = $nonce->get_action();

		/**
		 * Note we can not use the "sanitize_text_field" in this case as this would falsify
		 * the compliance checks due to stripped/changed characters.
		 *
		 * For example:
		 * - "pas&"<>word" passed through the "sanitize_text_field" function would become "pas&"word" (note missing angular brackets)
		 */
		if ( ! empty( $nonce_value ) && false !== wp_verify_nonce( $nonce_value, $nonce_action ) && isset( $_POST['pwd'] ) ) {
			$password = ( is_string( $_POST['pwd'] ) && '' === $_POST['pwd'] ) ? null : Utils\Type::ensure_string( wp_unslash( $_POST['pwd'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- reason described above.
		}

		/**
		 * Only proceed if:
		 * - user has provided the password
		 * - the password is correct, thus user has been successfully logged-in
		 */
		if ( null !== $password && $user instanceof WP_User ) {
			$compliance = new Compliance( $this->container, $user );

			if ( false === $compliance->is_current_password_compliant( $password ) ) {
				$updated_redirect_to = $compliance->get_redirect_link();

				if ( is_string( $updated_redirect_to ) ) {
					$redirect_to = $updated_redirect_to;
				} elseif ( is_wp_error( $updated_redirect_to ) ) {
					wp_die( wp_kses( $updated_redirect_to->get_error_message(), [ [ 'strong' => true ] ] ) );
					exit; // @phpstan-ignore deadCode.unreachable
				}
			}
		}

		return $redirect_to;
	}

	/**
	 * Filter the login message
	 *
	 * @param string $message Login message text.
	 *
	 * @return string Updated login message text.
	 */
	public function filter_login_message( string $message ): string {
		// Predefine the variable's value.
		$message_key = null;

		// Get the nonce data.
		$nonce        = new Utils\Nonce( $this->container, 'message_key' );
		$nonce_value  = isset( $_GET[ $nonce->get_key() ] ) ? sanitize_text_field( Utils\Type::ensure_string( wp_unslash( $_GET[ $nonce->get_key() ] ) ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$nonce_action = $nonce->get_action();

		// Verify nonce.
		if ( ! empty( $nonce_value ) && false !== wp_verify_nonce( $nonce_value, $nonce_action ) ) {
			$message_key = isset( $_GET[ $nonce_action ] ) ? sanitize_text_field( Utils\Type::ensure_string( wp_unslash( $_GET[ $nonce_action ] ) ) ) : null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		}

		// Do we have the login message identifier given?
		if ( null !== $message_key ) {
			switch ( $message_key ) {
				// Password is not compliant with the password policy.
				case 'pc':
					$message = sprintf(
						'<p class="message">%s</p>',
						__( 'Your current password is not compliant with the password policy. Enter your new password below or generate one.', 'password-requirements' ),
					);

					break;
				// Password has expired.
				case 'pe':
					$message = sprintf(
						'<p class="message">%s</p>',
						__( 'Your current password has expired. Enter your new password below or generate one.', 'password-requirements' ),
					);

					break;
			}
		}

		return $message;
	}
}
