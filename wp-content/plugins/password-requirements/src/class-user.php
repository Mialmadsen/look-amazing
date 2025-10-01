<?php
/**
 * User class
 *
 * @package Teydea_Studio\Password_Requirements
 */

namespace Teydea_Studio\Password_Requirements;

use Teydea_Studio\Password_Requirements\Dependencies\Utils;
use WP_Error;
use WP_User;

/**
 * The "User" class
 */
class User extends Utils\User {
	/**
	 * Cache group: user password expiry check
	 *
	 * @var string
	 */
	const CACHE_GROUP__USER_PASSWORD_EXPIRY_CHECK = 'user_password_expiry_checked';

	/**
	 * User meta key: last password change date
	 *
	 * @var string
	 */
	const USER_META_KEY__PASSWORD_CHANGED_AT = 'password_requirements__password_changed_at';

	/**
	 * Get the link to the password reset form
	 *
	 * @param string $notice_key Notice key.
	 *
	 * @return null|string|WP_Error Link to the password reset form; null if user is not logged in; instance of WP_Error in case of "spammy" or non-existed users.
	 */
	public function get_password_reset_form_link( string $notice_key ) {
		$user = $this->get_user();

		if ( null === $user ) {
			return null;
		}

		$key = get_password_reset_key( $user );

		// This only can happen to users who are marked as "spammy" or don't exists.
		if ( $key instanceof WP_Error ) {
			$message = '';

			switch ( $notice_key ) {
				case 'pc':
					$message = __( 'Your current password is not compliant with the password policy.', 'password-requirements' );
					break;
				case 'pe':
					$message = __( 'Your current password has expired.', 'password-requirements' );
					break;
			}

			return new WP_Error(
				'password_invalidated',
				sprintf(
					// Translators: %s - notice message.
					__( '<strong>Error:</strong> %s Please contact the administrator to get your password reset.', 'password-requirements' ),
					$message,
				),
			);
		}

		$query_args = [
			'action'  => 'rp',
			'key'     => $key,
			'login'   => rawurlencode( $this->get_user_login() ?? '' ),
			'wp_lang' => get_user_locale( $user ),
		];

		$path        = add_query_arg( $query_args, 'wp-login.php' );
		$redirect_to = $this->container->is_network_enabled()
			? network_home_url( $path )
			: home_url( $path );

		/**
		 * Allow other plugins and modules to filter the redirection URL
		 *
		 * @param string               $redirect_to URL to redirect user to.
		 * @param WP_User              $user        User object.
		 * @param array<string,string> $query_args  Array of query args used to construct the password reset form link.
		 */
		return apply_filters( 'password_requirements__password_reset_form_link', $redirect_to, $user, $query_args );
	}
}
