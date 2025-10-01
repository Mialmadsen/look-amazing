<?php
/**
 * Ensure that user password is compliant even if user is already
 * logged in, but is interacting with WordPress
 *
 * @package Teydea_Studio\Password_Requirements
 */

namespace Teydea_Studio\Password_Requirements\Modules;

use Teydea_Studio\Password_Requirements\Dependencies\Utils;
use Teydea_Studio\Password_Requirements\Compliance;
use Teydea_Studio\Password_Requirements\User;

/**
 * The "Module_Compliance_On_Interaction" class
 */
final class Module_Compliance_On_Interaction extends Utils\Module {
	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		/**
		 * Check whether the the current password has expired.
		 *
		 * Do this when accessing either the admin panel
		 * or the frontend views.
		 */
		add_action( 'admin_init', [ $this, 'on_interaction' ] );
		add_action( 'parse_request', [ $this, 'on_interaction' ] );
	}

	/**
	 * Check whether the the current password has not expired
	 *
	 * @return void
	 */
	public function on_interaction(): void {
		$user = new User( $this->container );

		if ( null === $user->get_user() ) {
			return;
		}

		$cache = new Utils\Cache( $this->container );

		$cache->set_group( $user::CACHE_GROUP__USER_PASSWORD_EXPIRY_CHECK );
		$cache->set_user( $user->get_user() );

		if ( false === $cache->read() ) {
			$compliance = new Compliance( $this->container );
			$compliance->set_user( $user );

			if ( $compliance->is_password_expired() ) {
				$redirect_to = $compliance->get_redirect_link();

				if ( is_string( $redirect_to ) ) {
					wp_safe_redirect( $redirect_to );
					exit;
				} elseif ( is_wp_error( $redirect_to ) ) {
					wp_die( wp_kses( $redirect_to->get_error_message(), [ [ 'strong' => true ] ] ) );
					exit; // @phpstan-ignore deadCode.unreachable
				}
			}

			// User password has not expired yet - cache that information for 1 hour to avoid unnecessary DB queries.
			$cache->write( true, HOUR_IN_SECONDS );
		}
	}
}
