<?php
/**
 * REST API endpoint to check whether user should change their password
 *
 * @package Teydea_Studio\Password_Requirements
 */

namespace Teydea_Studio\Password_Requirements\Modules;

use Teydea_Studio\Password_Requirements\Dependencies\Utils;
use Teydea_Studio\Password_Requirements\Compliance;
use Teydea_Studio\Password_Requirements\User;
use WP_Error;
use WP_REST_Response;

/**
 * The "Module_Endpoint_Should_Change_Password" class
 */
final class Module_Endpoint_Should_Change_Password extends Utils\Module {
	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		// Register endpoint.
		add_action( 'rest_api_init', [ $this, 'register_endpoint' ] );
	}

	/**
	 * Register endpoint
	 *
	 * @return void
	 */
	public function register_endpoint(): void {
		register_rest_route(
			sprintf( '%s/v1', $this->container->get_slug() ),
			'/should-change-password',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'should_change_password' ],
				'permission_callback' => 'is_user_logged_in',
			],
		);
	}

	/**
	 * Check whether user should change their password
	 *
	 * If yes, logout the user immediately and return a link
	 * to the password reset form.
	 *
	 * This is used as an action after plugin settings
	 * has changed.
	 *
	 * @return WP_Error|WP_REST_Response Instance of WP_REST_Response on success, instance of WP_Error on failure.
	 */
	public function should_change_password() {
		$user = new User( $this->container );

		if ( null === $user->get_user() ) {
			return new WP_Error(
				'unknown_requestor',
				__( 'Unknown requestor.', 'password-requirements' ),
			);
		}

		$compliance = new Compliance( $this->container );
		$compliance->set_user( $user );

		return new WP_REST_Response( $compliance->is_password_expired() ? $compliance->get_redirect_link() : '', 200 );
	}
}
