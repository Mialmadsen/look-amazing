<?php
/**
 * Ensure that user password is compliant with applying password policy
 * when new user registers
 *
 * @package Teydea_Studio\Password_Requirements
 */

namespace Teydea_Studio\Password_Requirements\Modules;

use Teydea_Studio\Password_Requirements\Dependencies\Utils;
use Teydea_Studio\Password_Requirements\Compliance;
use WP_User;

/**
 * The "Module_Compliance_On_Register" class
 */
final class Module_Compliance_On_Register extends Utils\Module {
	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		// Note the timestamp of password creation after the new user registers successfully.
		add_action( 'user_register', [ $this, 'after_user_registered' ], 10, 2 );
	}

	/**
	 * Note the timestamp of password creation after the new user registers successfully
	 *
	 * @param int                      $user_id  User ID.
	 * @param array{user_pass?:string} $userdata The raw array of data passed to wp_insert_user().
	 *
	 * @return void
	 */
	public function after_user_registered( int $user_id, array $userdata ): void {
		if ( ! isset( $userdata['user_pass'] ) ) {
			return;
		}

		$password = $userdata['user_pass'];
		$user     = get_user_by( 'ID', $user_id );

		if ( ! $user instanceof WP_User ) {
			return;
		}

		$compliance = new Compliance( $this->container, $user );
		$compliance->after_user_registered( $password );
	}
}
