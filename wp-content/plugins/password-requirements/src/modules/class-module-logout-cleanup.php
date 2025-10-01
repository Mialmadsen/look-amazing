<?php
/**
 * Cleanup the cookie object after user is logged out
 *
 * @package Teydea_Studio\Password_Requirements
 */

namespace Teydea_Studio\Password_Requirements\Modules;

use Teydea_Studio\Password_Requirements\Dependencies\Utils;

/**
 * The "Module_Logout_Cleanup" class
 */
final class Module_Logout_Cleanup extends Utils\Module {
	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		// Clear dangling cookie data.
		add_action( 'clear_auth_cookie', [ $this, 'clear_dangling_cookie_data' ] );
	}

	/**
	 * Clear dangling cookie data
	 *
	 * When "wp_clear_auth_cookie" function is called in WordPress core, the cookies
	 * itself are unset, however the $_COOKIE global still holds the old data which
	 * might be causing unintended side effects during the further processing.
	 *
	 * In our case, when user is force-logged out, we redirect them to the password
	 * reset form with specific message and nonce that signs that request; the
	 * "create nonce" function thinks that the user ID is now 0 (which is correct),
	 * but the "logged in cookie" token still points to the specific user. This in
	 * turn causes the nonce verification issues in the password reset form.
	 *
	 * @see https://developer.wordpress.org/reference/functions/wp_clear_auth_cookie/
	 *
	 * @return void
	 */
	public function clear_dangling_cookie_data(): void {
		/**
		 * Exactly the same condition as in core's "wp_clear_auth_cookie" function.
		 *
		 * This filter is documented in wp-includes/pluggable.php
		 */
		if ( ! apply_filters( 'send_auth_cookies', true, 0, 0, 0, '', '' ) ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			return;
		}

		if ( defined( 'LOGGED_IN_COOKIE' ) ) {
			unset( $_COOKIE[ LOGGED_IN_COOKIE ] );
		}
	}
}
