<?php
/**
 * Plugin settings page
 *
 * @package Teydea_Studio\Password_Requirements
 */

namespace Teydea_Studio\Password_Requirements\Modules;

use Teydea_Studio\Password_Requirements\Dependencies\Universal_Modules;

/**
 * The "Module_Settings_Page" class
 */
final class Module_Settings_Page extends Universal_Modules\Module_Settings_Page {
	/**
	 * Setup the values of the class properties
	 *
	 * @return void
	 */
	public function setup_class_properties(): void {
		// Define the page title.
		$this->page_title = __( 'WP Password Policy', 'password-requirements' );

		// Define the menu title.
		$this->menu_title = __( 'Password Policy', 'password-requirements' );

		// Define the list of help & support links.
		$this->help_links = [
			[
				'url'   => sprintf( 'https://wordpress.org/support/plugin/%s/', $this->container->get_slug() ),
				'title' => __( 'Support forum', 'password-requirements' ),
			],
			[
				'url'   => 'https://wppasswordpolicy.com/contact/?utm_source=WP+Password+Policy',
				'title' => __( 'Contact plugin author', 'password-requirements' ),
			],
			[
				'url'   => sprintf( 'https://wordpress.org/plugins/%s/', $this->container->get_slug() ),
				'title' => __( 'Plugin on WordPress.org directory', 'password-requirements' ),
			],
			[
				'url'   => 'https://wppasswordpolicy.com/?utm_source=WP+Password+Policy',
				'title' => __( 'Plugin website', 'password-requirements' ),
			],
		];

		parent::setup_class_properties();
	}
}
