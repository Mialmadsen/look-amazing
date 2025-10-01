<?php
/**
 * Ensure that the site administrator is notified in case of a plugin settings validation errors
 *
 * @package Teydea_Studio\Password_Requirements
 */

namespace Teydea_Studio\Password_Requirements\Modules;

use Teydea_Studio\Password_Requirements\Dependencies\Utils;
use Teydea_Studio\Password_Requirements\Settings;

/**
 * The "Module_Site_Health" class
 */
final class Module_Site_Health extends Utils\Module {
	/**
	 * Hold the Settings instance
	 *
	 * @var ?Settings
	 */
	private ?Settings $settings = null;

	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		// Register custom site health tests.
		add_filter( 'site_status_tests', [ $this, 'register_site_health_tests' ] );
	}

	/**
	 * Get the settings class instance
	 *
	 * @return Settings Settings class instance.
	 */
	protected function get_settings(): Settings {
		if ( null === $this->settings ) {
			$this->settings = new Settings( $this->container );
		}

		return $this->settings;
	}

	/**
	 * Register custom site health tests
	 *
	 * @param array<string,array<string,mixed>> $tests Site health tests.
	 *
	 * @return array<string,array<string,mixed>> Site health tests array.
	 */
	public function register_site_health_tests( array $tests ): array {
		$settings        = $this->get_settings();
		$tests['direct'] = array_merge(
			$tests['direct'],
			[
				$settings->get_option_key() => [
					'label' => __( 'Password Policy', 'password-requirements' ),
					'test'  => [ $this, 'check_settings' ],
				],
			]
		);

		return $tests;
	}

	/**
	 * Add test to ensure that the site administrator is notified in case of a Settings class validation errors
	 *
	 * @return array<string,mixed> Test configuration.
	 */
	public function check_settings(): array {
		$settings = $this->get_settings();

		// Build default structure of the health check.
		$result = [
			'label'       => __( 'Password Policy plugin settings are valid', 'password-requirements' ),
			'status'      => 'good',
			'badge'       => [
				'label' => __( 'Security', 'password-requirements' ),
				'color' => 'blue',
			],
			'description' => sprintf(
				'<p>%s</p>',
				__( 'Settings of the "Password Policy" must be valid to ensure that the password compliance checks are working properly.', 'password-requirements' )
			),
			'actions'     => '',
			'test'        => $settings->get_option_key(),
		];

		// Check for validation errors.
		if ( $settings->has_validation_errors() ) {
			$result['label']  = __( 'Password Policy plugin settings are invalid', 'password-requirements' );
			$result['status'] = 'critical';

			$result['description'] .= sprintf(
				'<p><code>%s</code></p>',
				$settings->get_first_validation_error()->get_error_message(),
			);
		}

		return $result;
	}
}
