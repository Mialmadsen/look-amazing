<?php
/**
 * Ensure that the password hint match the password policy
 *
 * @package Teydea_Studio\Password_Requirements
 */

namespace Teydea_Studio\Password_Requirements\Modules;

use Teydea_Studio\Password_Requirements\Dependencies\Utils;
use Teydea_Studio\Password_Requirements\Compliance;
use WP_User;

/**
 * The "Module_Password_Hint" class
 */
final class Module_Password_Hint extends Utils\Module {
	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		// Filter the password hint to make it easier to match the password policy.
		add_filter( 'password_hint', [ $this, 'filter_password_hint' ] );
	}

	/**
	 * Get current user object
	 *
	 * @return ?WP_User User object, null if user is not recognized.
	 */
	protected function get_user(): ?WP_User {
		// Get the current action key.
		$action = isset( $_POST['action'] ) ? sanitize_text_field( Utils\Type::ensure_string( wp_unslash( $_POST['action'] ) ) ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		/**
		 * When new user account is created by another user, get the user object
		 * for the user being created, to run the password policy check in the
		 * right context.
		 *
		 * We create the object of the WP_User class dynamically, for user account
		 * that don't yet exists. We need this object to pass it to other classes
		 * to determine the hint content based on user name and role.
		 */
		if ( 'createuser' === $action ) {
			$user_login = isset( $_POST['user_login'] ) ? sanitize_text_field( Utils\Type::ensure_string( wp_unslash( $_POST['user_login'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$user_role  = isset( $_POST['role'] ) ? sanitize_text_field( Utils\Type::ensure_string( wp_unslash( $_POST['role'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			$user = new WP_User( 0, $user_login );
			$user->set_role( $user_role );

			return $user;
		}

		/**
		 * Get currently logged in user
		 * - this applies when password is being changed in the user profile/settings page
		 */
		$user = wp_get_current_user();

		if ( 0 !== $user->ID ) {
			return $user;
		}

		/**
		 * User is not logged in; check the "reset pass" cookie
		 * - this exists when user is resetting password
		 */
		$rp_cookie   = sprintf( 'wp-resetpass-%s', defined( 'COOKIEHASH' ) ? COOKIEHASH : '' );
		$user_cookie = isset( $_COOKIE[ $rp_cookie ] ) ? sanitize_text_field( Utils\Type::ensure_string( wp_unslash( $_COOKIE[ $rp_cookie ] ) ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( ! empty( $user_cookie ) && 0 < strpos( $user_cookie, ':' ) ) {
			$user_cookie = explode( ':', $user_cookie, 2 );
			$user_login  = '';

			if ( is_numeric( $user_cookie[0] ) ) {
				$userdata = get_userdata( Utils\Type::ensure_int( $user_cookie[0] ) );

				if ( $userdata instanceof WP_User ) {
					$user_login = $userdata->user_login;
				}
			} else {
				$user_login = $user_cookie[0];
			}

			$user = check_password_reset_key(
				$user_cookie[1] ?? '',
				$user_login,
			);

			if ( $user instanceof WP_User ) {
				return $user;
			}
		}

		return null;
	}

	/**
	 * Filter the password hint to ensure it match the password policy
	 *
	 * @param string $hint The password hint text.
	 *
	 * @return string Updated password hint text.
	 */
	public function filter_password_hint( string $hint ): string {
		$user = $this->get_user();

		if ( null !== $user ) {
			$compliance  = new Compliance( $this->container, $user );
			$custom_hint = $compliance->get_hint();

			if ( ! empty( $custom_hint ) ) {
				$hint = $custom_hint;
			}
		}

		return $hint;
	}
}
