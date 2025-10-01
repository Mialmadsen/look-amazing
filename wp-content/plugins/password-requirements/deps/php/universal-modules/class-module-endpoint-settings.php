<?php
/**
 * REST API endpoint for getting and updating settings
 *
 * @package Teydea_Studio\Password_Requirements\Dependencies\Universal_Modules
 */

namespace Teydea_Studio\Password_Requirements\Dependencies\Universal_Modules;

use Teydea_Studio\Password_Requirements\User;
use Teydea_Studio\Password_Requirements\Settings;
use Teydea_Studio\Password_Requirements\Dependencies\Utils;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * The "Module_Endpoint_Settings" class
 */
class Module_Endpoint_Settings extends Utils\Module {
	/**
	 * Hold the Settings instance
	 *
	 * @var ?Settings
	 */
	protected ?Settings $settings = null;

	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		// Register endpoints.
		add_action( 'rest_api_init', [ $this, 'register_endpoints' ] );
	}

	/**
	 * Register endpoints
	 *
	 * @return void
	 */
	public function register_endpoints(): void {
		register_rest_route(
			sprintf( '%s/v1', $this->container->get_slug() ),
			'/settings',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_settings' ],

				/**
				 * Ensure that user is logged in and has the required
				 * capability
				 *
				 * @return bool Boolean "true" if user has the permission to process this request, "false" otherwise.
				 */
				'permission_callback' => function (): bool {
					$user = new User( $this->container );
					return $user->has_managing_permissions();
				},
			],
		);

		register_rest_route(
			sprintf( '%s/v1', $this->container->get_slug() ),
			'/settings',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'save_settings' ],
				'args'                => [
					'nonce'    => [
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',

						/**
						 * Nonce value validation
						 *
						 * @param string $value Nonce value.
						 *
						 * @return bool Whether the nonce value is valid or not.
						 */
						'validate_callback' => function ( string $value ): bool {
							$nonce = new Utils\Nonce( $this->container, 'save_settings' );
							return false !== wp_verify_nonce( $value, $nonce->get_action() );
						},
					],
					'settings' => [
						'required'          => true,
						'type'              => 'array',

						/**
						 * Settings data sanitization
						 *
						 * @return Settings Instance of a Settings class.
						 */
						'sanitize_callback' => function (): Settings {
							if ( null === $this->settings ) {
								$this->settings = new Settings( $this->container );
							}

							return $this->settings;
						},

						/**
						 * Settings data validation
						 *
						 * @param array<string,array<string,mixed>> $data Settings to save.
						 *
						 * @return true|WP_Error Boolean "true" if a given settings data are valid, instance of WP_Error otherwise.
						 */
						'validate_callback' => function ( array $data ) {
							$this->settings = new Settings( $this->container, $data );

							return $this->settings->has_validation_errors()
								? $this->settings->get_first_validation_error()
								: true;
						},
					],
				],

				/**
				 * Ensure that user is logged in and has the required
				 * capability
				 *
				 * @return bool Boolean "true" if user has the permission to process this request, "false" otherwise.
				 */
				'permission_callback' => function (): bool {
					$user = new User( $this->container );
					return $user->has_managing_permissions();
				},
			],
		);
	}

	/**
	 * Get plugin settings
	 *
	 * @return WP_Error|WP_REST_Response Instance of WP_REST_Response on success, instance of WP_Error on failure.
	 */
	public function get_settings() {
		$settings = new Settings( $this->container );

		if ( $settings->has_validation_errors() ) {
			return $settings->get_first_validation_error();
		}

		$data = $settings->get_normalized_data();

		if ( null === $data ) {
			return new WP_Error(
				'validation_errors_found',
				'Can\'t get settings data; resolve validation errors first.',
			);
		}

		return new WP_REST_Response(
			array_merge(
				$data,
				[ 'templates' => $settings->get_templates() ],
			),
			200,
		);
	}

	/**
	 * Save plugin settings
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_Error|WP_REST_Response Instance of WP_REST_Response on success, instance of WP_Error on failure.
	 *
	 * @phpstan-ignore missingType.generics
	 */
	public function save_settings( WP_REST_Request $request ) {
		/** @var Settings $settings */
		$settings = $request->get_param( 'settings' );
		$saved    = $settings->save();

		return $saved instanceof WP_Error
			? $saved
			: new WP_REST_Response( [], 200 );
	}
}
