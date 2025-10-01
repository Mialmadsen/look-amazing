<?php
/**
 * Load plugin tokens and dependencies
 *
 * @package Teydea_Studio\Password_Requirements
 */

namespace Teydea_Studio\Password_Requirements;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Teydea_Studio\Password_Requirements\Dependencies\Universal_Modules;
use Teydea_Studio\Password_Requirements\Dependencies\Utils;

/**
 * Class autoloader
 */
spl_autoload_register(
	/**
	 * Autoload plugin classes
	 *
	 * @param string $class_name Class name.
	 *
	 * @return void
	 */
	function ( string $class_name ): void {
		$class_map = include __DIR__ . '/classmap.php';

		if ( isset( $class_map[ $class_name ] ) ) {
			require_once __DIR__ . $class_map[ $class_name ];
		}
	},
);

/**
 * Get the plugin container object
 *
 * @return Utils\Plugin Plugin container object.
 */
function get_container(): Utils\Plugin {
	static $plugin = null;

	if ( null === $plugin ) {
		// Construct the plugin object.
		$plugin = new Utils\Plugin();

		$plugin->set_data_prefix( 'password_requirements' );
		$plugin->set_main_dir( __DIR__ );
		$plugin->set_name( 'WP Password Policy' );
		$plugin->set_slug( 'password-requirements' );
		$plugin->set_supports_network( true );
		$plugin->set_text_domain( 'password-requirements' );
		$plugin->set_version( '3.3.0' );

		$plugin->register_modules(
			[
				Modules\Module_Compliance_On_Interaction::class,
				Modules\Module_Compliance_On_Login::class,
				Modules\Module_Compliance_On_Password_Change::class,
				Modules\Module_Compliance_On_Register::class,
				Modules\Module_Endpoint_Should_Change_Password::class,
				Modules\Module_Logout_Cleanup::class,
				Modules\Module_Password_Hint::class,
				Modules\Module_Plugin_Upgrade_Action_Link::class,
				Modules\Module_Require_Current_Password::class,
				Modules\Module_Settings_Page::class,
				Modules\Module_Site_Health::class,
				Universal_Modules\Module_Cache_Invalidation::class,
				Universal_Modules\Module_Endpoint_Settings::class,
				Universal_Modules\Module_Translations::class,
			],
		);
	}

	return $plugin;
}
