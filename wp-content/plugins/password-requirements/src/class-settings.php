<?php
/**
 * Plugin settings
 *
 * @package Teydea_Studio\Password_Requirements
 */

namespace Teydea_Studio\Password_Requirements;

use Teydea_Studio\Password_Requirements\Dependencies\Utils;
use Teydea_Studio\Password_Requirements\Dependencies\Validatable_Fields;

/**
 * The "Settings" class
 *
 * @phpstan-import-type Type_Settings_Fields_Config from Utils\Settings
 */
class Settings extends Utils\Settings {
	/**
	 * Construct the object
	 *
	 * @param Utils\Container                   $container Container instance.
	 * @param array<string,array<string,mixed>> $config    Configuration data; if not provided, data will be loaded from the database.
	 */
	public function __construct( Utils\Container $container, ?array $config = null ) {
		$this->container     = $container;
		$this->fields_config = $this->get_fields_config();

		/**
		 * Use the parent constructor
		 */
		parent::__construct( $this->container, $this->fields_config, $config );
	}

	/**
	 * Get the fields configuration
	 *
	 * @return Type_Settings_Fields_Config Fields configuration.
	 */
	protected function get_fields_config(): array {
		// The fields configuration.
		$fields_config = [
			'policies' => [
				'type'   => Validatable_Fields\Dynamic_Fields_Group::TYPE,
				'config' => [
					'policy' => [
						'fields' => [
							'key'                          => Validatable_Fields\Configuration::dynamic_field_key_field(),
							'type'                         => Validatable_Fields\Configuration::exact_string_field( 'policy' ),
							'is_active'                    => Validatable_Fields\Configuration::boolean_field( false ),
							'name'                         => Validatable_Fields\Configuration::string_field( __( 'New password policy', 'password-requirements' ) ),
							'rules.complexity'             => Validatable_Fields\Configuration::boolean_field( true ),
							'rules.maximum_age'            => Validatable_Fields\Configuration::boolean_field( true ),
							'rules.minimum_age'            => Validatable_Fields\Configuration::boolean_field( false ),
							'rules.minimum_length'         => Validatable_Fields\Configuration::boolean_field( true ),
							'rules.maximum_length'         => Validatable_Fields\Configuration::boolean_field( true ),
							'rules.require_current_password' => Validatable_Fields\Configuration::boolean_field( true ),
							'rule_settings.complexity.digit' => Validatable_Fields\Configuration::boolean_field( true ),
							'rule_settings.complexity.lowercase' => Validatable_Fields\Configuration::boolean_field( true ),
							'rule_settings.complexity.special_character' => Validatable_Fields\Configuration::boolean_field( true ),
							'rule_settings.complexity.unique_characters' => Validatable_Fields\Configuration::boolean_field( true ),
							'rule_settings.complexity.uppercase' => Validatable_Fields\Configuration::boolean_field( true ),
							'rule_settings.complexity.consecutive_user_symbols' => Validatable_Fields\Configuration::boolean_field( true ),
							'rule_settings.maximum_age'    => Validatable_Fields\Configuration::integer_field( 30, 1, 1000 ),
							'rule_settings.maximum_consecutive_user_symbols' => Validatable_Fields\Configuration::integer_field( 4, 0, 50 ),
							'rule_settings.minimum_age'    => Validatable_Fields\Configuration::integer_field( 2, 1, 1000 ),
							'rule_settings.minimum_length' => Validatable_Fields\Configuration::integer_field( 10, 1, 50 ),
							'rule_settings.maximum_length' => Validatable_Fields\Configuration::integer_field( 256, 64, 512 ),
							'rule_settings.minimum_unique_characters' => Validatable_Fields\Configuration::integer_field( 6, 1, 50 ),
						],
					],
				],
			],
		];

		// Avoid duplicating filters if using PRO version of the plugin.
		if ( defined( 'PASSWORD_REQUIREMENTS_PRO' ) ) {
			return $fields_config; // @codeCoverageIgnore
		}

		/**
		 * Allow other plugins and modules to filter
		 * the settings fields configuration
		 *
		 * @param Type_Settings_Fields_Config $fields_config Configuration of the settings fields.
		 */
		return apply_filters( 'password_requirements__settings_fields_config', $fields_config );
	}
}
