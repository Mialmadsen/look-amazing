<?php
/**
 * Ensure that the new user's passwords are:
 * - passed to the past password store for compliance check
 * - compliant with applying password policy
 *
 * @package Teydea_Studio\Password_Requirements
 */

namespace Teydea_Studio\Password_Requirements\Modules;

use Teydea_Studio\Password_Requirements\Dependencies\Utils;
use Teydea_Studio\Password_Requirements\Compliance;
use stdClass;
use WP_Error;
use WP_User;

/**
 * The "Module_Compliance_On_Password_Change" class
 */
final class Module_Compliance_On_Password_Change extends Utils\Module {
	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		// Note the new user password in the "Passwords Store" when user is updated.
		add_action( 'wp_update_user', [ $this, 'on_update_user' ], 1, 3 );

		// Note the new user password in the "Passwords Store" when password is changed.
		add_action( 'wp_set_password', [ $this, 'on_password_set' ], 1, 2 );

		// Validate the password reset request.
		add_action( 'validate_password_reset', [ $this, 'validate_password_reset' ], 1, 2 );

		// Check if a new password is compliant with the password policy.
		add_action( 'password_reset', [ $this, 'on_password_reset' ], 1, 2 );

		// Check if a new password is compliant with the password policy.
		add_action( 'user_profile_update_errors', [ $this, 'on_user_profile_update' ], 1, 3 );

		// Maybe disallow password reset for user, depending on password policy settings.
		add_filter( 'allow_password_reset', [ $this, 'filter_allow_password_reset' ], 10, 2 );

		// Maybe hide "password fields" in the user profile, depending on password policy settings.
		add_filter( 'show_password_fields', [ $this, 'filter_show_password_fields' ], 10, 2 );
	}

	/**
	 * Validate the password reset request
	 *
	 * @param WP_Error         $errors WP Error object.
	 * @param WP_User|WP_Error $user   WP_User object if the login and reset key match. WP_Error object otherwise.
	 *
	 * @return void
	 */
	public function validate_password_reset( WP_Error $errors, $user ): void {
		// Only proceed if user object is valid.
		if ( ! $user instanceof WP_User ) {
			return;
		}

		/**
		 * Get the new password value
		 */
		$password   = null;
		$form_field = null;

		if ( isset( $_POST['pass1'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$password   = Utils\Type::ensure_string( wp_unslash( $_POST['pass1'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$form_field = 'pass1';
		} elseif ( isset( $_POST['password_1'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$password   = Utils\Type::ensure_string( wp_unslash( $_POST['password_1'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$form_field = 'password_1';
		}

		// Skip if new password is not known.
		if ( null === $password ) {
			return;
		}

		$compliance = new Compliance( $this->container, $user );

		if ( false === $compliance->is_new_password_compliant( $password ) ) {
			$errors->add(
				'pass',
				wp_kses(
					sprintf(
						// Translators: %s - password hint.
						__( '<strong>Error:</strong> New password is not compliant with the password policy. %s', 'password-requirements' ),
						$compliance->get_hint(),
					),
					[ 'strong' => [] ],
				),
				[ 'form-field' => $form_field ],
			);
		}
	}

	/**
	 * Note the new user password in the "Passwords Store" when password is changed
	 *
	 * This is only triggered after the password compliance validation, so we are only
	 * passing the new password into the "Passwords Store" and resetting the cache.
	 *
	 * @param string $password The plaintext password just set.
	 * @param int    $user_id  The ID of the user whose password was just set.
	 *
	 * @return void
	 */
	public function on_password_set( string $password, int $user_id ): void {
		$user = get_user_by( 'ID', $user_id );

		if ( ! $user instanceof WP_User ) {
			return;
		}

		$compliance = new Compliance( $this->container, $user );
		$compliance->after_password_changed( $password );
	}

	/**
	 * Note new user password in the "Passwords Store" when user is updated
	 *
	 * This is only triggered after the password compliance validation, so we are only
	 * passing the new password into the "Passwords Store" and resetting the cache.
	 *
	 * @param int                      $user_id      The ID of the user that was just updated.
	 * @param array{user_pass?:string} $userdata     The array of user data that was updated.
	 * @param array{user_pass?:string} $userdata_raw The unedited array of user data that was updated.
	 *
	 * @return void
	 */
	public function on_update_user( int $user_id, array $userdata, array $userdata_raw ): void {
		if ( isset( $userdata_raw['user_pass'] ) ) {
			$this->on_password_set( $userdata_raw['user_pass'], $user_id );
		}
	}

	/**
	 * Check if a new password is compliant with the password policy
	 *
	 * Applies to user passwords edited in:
	 * - /wp-login.php?action=resetpass
	 * - /wp-login.php?action=rp
	 *
	 * If password is compliant with the policy, user data will be updated and
	 * the "wp_set_password" hook will be fired; password's update will be passed
	 * to the "Passwords Store" by a dedicated function above.
	 *
	 * @param WP_User $user     The user.
	 * @param string  $new_pass New user password.
	 */
	public function on_password_reset( WP_User $user, string $new_pass ): void {
		$password   = wp_unslash( $new_pass );
		$compliance = new Compliance( $this->container, $user );

		if ( false === $compliance->is_new_password_compliant( $password ) ) {
			wp_die(
				wp_kses(
					sprintf(
						// Translators: %s - password hint.
						__( '<strong>Error:</strong> New password is not compliant with the password policy. %s', 'password-requirements' ),
						$compliance->get_hint(),
					),
					[ 'strong' => [] ],
				),
				'',
				[ 'back_link' => true ],
			);

			exit; // @phpstan-ignore deadCode.unreachable
		}
	}

	/**
	 * Check if new password is compliant with the password policy
	 *
	 * Applies to user passwords edited in:
	 * - /wp-admin/user-edit.php
	 * - /wp-admin/profile.php
	 *
	 * If password is compliant with the policy, user data will be updated and
	 * the "wp_update_user" hook will be fired; new password will be noted to
	 * the "Passwords Store" by a dedicated function above.
	 *
	 * @param WP_Error $errors    WP_Error object (passed by reference).
	 * @param bool     $update    Whether this is a user update.
	 * @param stdClass $user_data User object (passed by reference).
	 *
	 * @return void
	 */
	public function on_user_profile_update( WP_Error &$errors, bool $update, stdClass $user_data ): void {
		// Only run custom check if user pass was changed.
		if ( isset( $user_data->user_pass ) ) {
			$password = wp_unslash( $user_data->user_pass );
			$user     = null;

			if ( isset( $user_data->ID ) ) {
				// Existing user is updated - get user object.
				$user = get_user_by( 'ID', $user_data->ID );
			} elseif ( false === $update ) {
				/**
				 * New user is created
				 * - initiate the WP_User object with provided data
				 *   in order to validate password compliance
				 */
				$user = new WP_User();

				$user->data->user_login   = $user_data->user_login;
				$user->data->user_email   = $user_data->user_email;
				$user->data->display_name = sprintf( '%1$s %2$s', $user_data->first_name, $user_data->last_name );

				$user->roles = [ $user_data->role ];
			}

			if ( ! $user instanceof WP_User ) {
				return;
			}

			$compliance = new Compliance( $this->container, $user );

			if ( false === $compliance->is_new_password_compliant( $password ) ) {
				$errors->add(
					'pass',
					wp_kses(
						sprintf(
							// Translators: %s - password hint.
							__( '<strong>Error:</strong> New password is not compliant with the password policy. %s', 'password-requirements' ),
							$compliance->get_hint(),
						),
						[ 'strong' => [] ],
					),
					[ 'form-field' => 'pass1' ],
				);
			}
		}
	}

	/**
	 * Maybe disallow password reset for user, depending on password policy settings
	 *
	 * @param bool $allow   Whether to allow the password to be reset. Default true.
	 * @param int  $user_id The ID of the user attempting to reset a password.
	 *
	 * @return bool Whether to allow the password to be reset.
	 */
	public function filter_allow_password_reset( bool $allow, int $user_id ): bool {
		// Only proceed if default configuration allows this user to reset the password.
		if ( true === $allow ) {
			$user = get_user_by( 'ID', $user_id );

			if ( $user instanceof WP_User ) {
				$compliance = new Compliance( $this->container, $user );
				$allow      = $compliance->can_user_change_password();
			}
		}

		return $allow;
	}

	/**
	 * Maybe hide "password fields" in the user profile, depending on password policy settings
	 *
	 * @param bool    $show Whether to show the password fields. Default true.
	 * @param WP_User $user User object for the current user to edit.
	 *
	 * @return bool Whether to show the password fields.
	 */
	public function filter_show_password_fields( bool $show, WP_User $user ): bool {
		// Only proceed if default configuration allows this user to reset the password.
		if ( true === $show ) {
			$compliance = new Compliance( $this->container, $user );
			$show       = $compliance->can_user_change_password();
		}

		return $show;
	}
}
