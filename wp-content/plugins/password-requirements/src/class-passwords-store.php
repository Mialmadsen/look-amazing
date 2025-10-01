<?php
/**
 * Store past passwords of each user to ensure that users will not
 * re-use their past passwords
 *
 * @package Teydea_Studio\Password_Requirements
 */

namespace Teydea_Studio\Password_Requirements;

use DateTime;
use DateTimeZone;
use Teydea_Studio\Password_Requirements\Dependencies\Utils;

/**
 * The "Passwords_Store" class
 */
class Passwords_Store {
	/**
	 * Container instance
	 *
	 * @var Utils\Container
	 */
	protected Utils\Container $container;

	/**
	 * User object
	 *
	 * @var User
	 */
	protected User $user;

	/**
	 * Plugin settings object
	 *
	 * @var Settings
	 */
	protected Settings $settings;

	/**
	 * User meta key for "past passwords" database storage
	 *
	 * @var array<string,string>
	 */
	protected array $user_meta_keys;

	/**
	 * Construct the object
	 *
	 * @param Utils\Container $container Container instance.
	 * @param User            $user      User object.
	 * @param Settings        $settings  Plugin settings object.
	 */
	public function __construct( Utils\Container $container, User $user, Settings $settings ) {
		$this->container = $container;
		$this->user      = $user;
		$this->settings  = $settings;
	}

	/**
	 * Get the "password changed at" value for user
	 *
	 * @return ?DateTime DateTime object if value is retrieved successfully from database, null otherwise.
	 */
	public function get_password_changed_at(): ?DateTime {
		$password_changed_at = $this->user->get_meta_as_integer( User::USER_META_KEY__PASSWORD_CHANGED_AT, true );

		return 0 === $password_changed_at
			? null
			: ( new DateTime( 'now', new DateTimeZone( '+00:00' ) ) )->setTimestamp( $password_changed_at );
	}

	/**
	 * Maybe note user password data
	 *
	 * @param string $password  New password.
	 * @param int    $timestamp Timestamp of the password use or update.
	 * @param bool   $is_new    Whether the password has been changed (true) or used (false).
	 *
	 * @return void
	 *
	 * @codeCoverageIgnore
	 */
	public function maybe_note_user_password_data( string $password, int $timestamp, bool $is_new ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		if ( ! $is_new ) {
			return;
		}

		// Update the password change date for user.
		$this->user->update_meta(
			User::USER_META_KEY__PASSWORD_CHANGED_AT,
			$timestamp,
			true,
		);
	}
}
