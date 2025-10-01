<?php
/**
 * Translations module
 * - load plugin textdomain
 *
 * @package Teydea_Studio\Password_Requirements\Dependencies\Universal_Modules
 */

namespace Teydea_Studio\Password_Requirements\Dependencies\Universal_Modules;

use Teydea_Studio\Password_Requirements\Dependencies\Utils;

/**
 * The "Module_Translations" class
 */
class Module_Translations extends Utils\Module {
	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		// Load plugin textdomain.
		add_action( 'init', [ $this, 'load_plugin_textdomain' ], 1 );
	}

	/**
	 * Load plugin textdomain
	 *
	 * @return void
	 */
	public function load_plugin_textdomain(): void {
		load_plugin_textdomain(
			$this->container->get_text_domain(),
			false,
			sprintf(
				'%s/languages',
				dirname( $this->container->get_basename() ),
			),
		);
	}
}
