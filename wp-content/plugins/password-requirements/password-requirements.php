<?php
/**
 * Plugin Name: WP Password Policy
 * Plugin URI: https://wppasswordpolicy.com/?utm_source=WP+Password+Policy
 * Description: Define advanced password policies, enforce strong password requirements, and improve your WordPress site's security.
 * Version: 3.3.0
 * Text Domain: password-requirements
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 6.6
 * Tested up to: 6.8
 * Author: Teydea Studio
 * Author URI: https://teydeastudio.com/?utm_source=WP+Password+Policy
 * Network: true
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Teydea_Studio\Password_Requirements
 */

namespace Teydea_Studio\Password_Requirements;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use function Teydea_Studio\Password_Requirements\get_container;

/**
 * Require loader
 */
require_once __DIR__ . '/loader.php';

/**
 * Initialize the plugin
 */
add_action(
	'plugins_loaded',
	function (): void {
		// Skip loading the plugin if its PRO version is enabled.
		if ( defined( 'PASSWORD_REQUIREMENTS_PRO' ) ) {
			return;
		}

		get_container()->init();
	},
);

// Note whether the plugin is activated network-wide.
add_action(
	sprintf( 'activate_%s', get_container()->get_basename() ),
	/**
	 * Note whether the plugin is being activated network-wide
	 *
	 * @param bool $network_wide Whether the plugin is being activated for all sites in the network or just the current site.
	 */
	function ( bool $network_wide ) {
		// Skip processing the plugin if its PRO version is enabled.
		if ( defined( 'PASSWORD_REQUIREMENTS_PRO' ) ) {
			return;
		}

		// Note the network-wide activation status.
		get_container()->note_network_wide_activation_status( $network_wide );
	},
	1,
);

/**
 * Handle the plugin's activation hook
 */
register_activation_hook(
	__FILE__,
	function (): void {
		// Skip loading the plugin if its PRO version is enabled.
		if ( defined( 'PASSWORD_REQUIREMENTS_PRO' ) ) {
			return;
		}

		get_container()->on_activation();
	},
);

/**
 * Handle the plugin's deactivation hook
 */
register_deactivation_hook(
	__FILE__,
	function (): void {
		// Skip loading the plugin if its PRO version is enabled.
		if ( defined( 'PASSWORD_REQUIREMENTS_PRO' ) ) {
			return;
		}

		get_container()->on_deactivation();
	},
);
