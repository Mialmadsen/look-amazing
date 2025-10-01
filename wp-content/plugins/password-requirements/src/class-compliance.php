<?php
/**
 * Instantiates all necessary objects and processes compliance
 * checks for various actions for a given user
 *
 * @package Teydea_Studio\Password_Requirements
 */

namespace Teydea_Studio\Password_Requirements;

use DateTime;
use DateTimeZone;
use Teydea_Studio\Password_Requirements\Dependencies\Utils;
use WP_Error;
use WP_User;

/**
 * The "Compliance" class
 */
class Compliance {
	/**
	 * Container instance
	 *
	 * @var Utils\Container
	 */
	protected Utils\Container $container;

	/**
	 * Message key
	 *
	 * @var ?string
	 */
	protected ?string $message_key = null;

	/**
	 * Password Policy object
	 *
	 * @var ?Password_Policy
	 */
	protected ?object $password_policy = null;

	/**
	 * Passwords Store object
	 *
	 * @var ?Passwords_Store
	 */
	protected ?object $passwords_store = null;

	/**
	 * Settings object
	 *
	 * @var ?Settings
	 */
	protected ?object $settings = null;

	/**
	 * User object
	 *
	 * @var ?User
	 */
	protected ?object $user = null;

	/**
	 * WP User object
	 *
	 * @var ?WP_User
	 */
	protected ?WP_User $wp_user = null;

	/**
	 * Construct the object
	 *
	 * @param Utils\Container $container Container instance.
	 * @param ?WP_User        $wp_user   WP User object or "null" if not set (yet).
	 */
	public function __construct( Utils\Container $container, ?WP_User $wp_user = null ) {
		$this->container = $container;

		if ( null !== $wp_user ) {
			$this->set_wp_user( $wp_user );
		}
	}

	/**
	 * Note the new user password in the "Passwords Store" after the password has been changed successfully
	 *
	 * This is only triggered after the password compliance validation, so we are only
	 * passing the new password into the "Passwords Store" and resetting the cache.
	 *
	 * @param string $password The plaintext password.
	 *
	 * @return void
	 */
	public function after_password_changed( string $password ): void {
		$user            = $this->get_user();
		$passwords_store = $this->get_passwords_store();

		// Note user password data.
		$passwords_store?->maybe_note_user_password_data( $password, ( new DateTime( 'now', new DateTimeZone( '+00:00' ) ) )->getTimestamp(), true );

		if ( null !== $user && null !== $user->get_user() ) {
			$cache = new Utils\Cache( $this->container );

			// Set the group and object.
			$cache->set_group( $user::CACHE_GROUP__USER_PASSWORD_EXPIRY_CHECK );
			$cache->set_user( $user->get_user() );

			// Reset password expiry cache.
			$cache->delete();
		}
	}

	/**
	 * Note the timestamp of password creation after the new user registers successfully
	 *
	 * @param string $password The plaintext password.
	 *
	 * @return void
	 */
	public function after_user_registered( string $password ): void {
		$this->get_passwords_store()?->maybe_note_user_password_data( $password, ( new DateTime( 'now', new DateTimeZone( '+00:00' ) ) )->getTimestamp(), true );
	}

	/**
	 * Check whether the user can change the password
	 *
	 * @return bool True if the user can change the password, false otherwise.
	 */
	public function can_user_change_password(): bool {
		return $this->get_password_policy()?->can_user_change_password() ?? true;
	}

	/**
	 * Get the hint about password requirements
	 *
	 * @return string The hint about password requirements or an empty string if no user is set.
	 */
	public function get_hint(): string {
		return $this->get_password_policy()?->get_hint() ?? '';
	}

	/**
	 * Get message key
	 *
	 * @return ?string Message key or "null" if not set.
	 */
	public function get_message_key(): ?string {
		return $this->message_key;
	}

	/**
	 * Get the Password Policy object
	 *
	 * @return ?Password_Policy Password Policy object.
	 */
	public function get_password_policy(): ?object {
		if ( null === $this->password_policy ) {
			$user            = $this->get_user();
			$passwords_store = $this->get_passwords_store();

			if ( null === $user || null === $passwords_store ) {
				return null;
			}

			$this->password_policy = new Password_Policy( $this->container, $user, $this->get_settings(), $passwords_store );
		}

		return $this->password_policy;
	}

	/**
	 * Get the Passwords Store object
	 *
	 * @return ?Passwords_Store Passwords Store object.
	 */
	public function get_passwords_store(): ?object {
		if ( null === $this->passwords_store ) {
			$user = $this->get_user();

			if ( null === $user ) {
				// Can't proceed without a valid user.
				return null;
			}

			$this->passwords_store = new Passwords_Store( $this->container, $user, $this->get_settings() );
		}

		return $this->passwords_store;
	}

	/**
	 * Get the redirect link to the password reset form
	 *
	 * @return null|string|WP_Error The redirect link, instance of WP_Error on failure, or "null" if no action is required.
	 */
	public function get_redirect_link() {
		if ( null !== $this->message_key ) {
			$user = $this->get_user();

			if ( null === $user ) {
				return null;
			}

			$redirect_to = $user->get_password_reset_form_link( $this->message_key );

			if ( is_string( $redirect_to ) ) {
				$user->logout_everywhere();

				/**
				 * Add nonce string after logout
				 *
				 * Nonces are user-specific, and since we are logging the user out,
				 * the nonce should be added now as for non-logged in user.
				 */
				$nonce       = new Utils\Nonce( $this->container, 'message_key' );
				$redirect_to = add_query_arg( $nonce->build_query_args( $this->message_key ), $redirect_to );

				return $redirect_to;
			} elseif ( is_wp_error( $redirect_to ) ) {
				$user->logout_everywhere();
				return $redirect_to;
			}
		}

		return null;
	}

	/**
	 * Get the Settings object
	 *
	 * @return Settings Settings object.
	 */
	public function get_settings(): object {
		if ( null === $this->settings ) {
			$this->settings = new Settings( $this->container );
		}

		return $this->settings;
	}

	/**
	 * Get the User object
	 *
	 * @return ?User User object or "null" if not set.
	 */
	public function get_user(): ?User {
		if ( null === $this->user ) {
			if ( null === $this->wp_user ) {
				return null;
			}

			$this->user = new User( $this->container, $this->wp_user );
		}

		return $this->user;
	}

	/**
	 * Get the WP User object
	 *
	 * @return ?WP_User WP User object or "null" if not set.
	 */
	public function get_wp_user(): ?WP_User {
		if ( null === $this->wp_user ) {
			if ( null === $this->user ) {
				return null;
			}

			$this->wp_user = $this->user->get_user();
		}

		return $this->wp_user;
	}

	/**
	 * Check if the current password is compliant with the password policy
	 *
	 * @param string $password The plaintext password.
	 *
	 * @return bool True if the password is compliant, false otherwise.
	 */
	public function is_current_password_compliant( string $password ): bool {
		// Reset message key.
		$this->message_key = null;

		// Note user password data.
		$this->get_passwords_store()?->maybe_note_user_password_data( $password, ( new DateTime( 'now', new DateTimeZone( '+00:00' ) ) )->getTimestamp(), false );

		if ( $this->get_password_policy()?->should_user_change_password() ) {
			// User's password has expired and should be changed.
			$this->message_key = 'pe';
		} elseif ( false === $this->get_password_policy()?->is_password_compliant( $password ) ) {
			// Password is not compliant with applying password policy.
			$this->message_key = 'pc';
		}

		return null === $this->message_key;
	}

	/**
	 * Check whether the current password is required for changing the password
	 *
	 * @return bool True if the current password is required, false otherwise.
	 */
	public function is_current_password_required(): bool {
		return $this->get_password_policy()?->is_current_password_required() ?? false;
	}

	/**
	 * Check if a new password is compliant with the password policy
	 *
	 * @param string $password The plaintext password to check.
	 *
	 * @return bool True if the password is compliant, false otherwise.
	 */
	public function is_new_password_compliant( string $password ): bool {
		$result = $this->get_password_policy()?->is_password_compliant( $password, true ) ?? true;

		if ( false === $result ) {
			// "pc" stands for "password compliance".
			$this->message_key = 'pc';
		}

		return $result;
	}

	/**
	 * Check if the current password has expired
	 */
	public function is_password_expired(): bool {
		$result = $this->get_password_policy()?->should_user_change_password() ?? false;

		if ( true === $result ) {
			// "pe" stands for "password expired".
			$this->message_key = 'pe';
		}

		return $result;
	}

	/**
	 * Try to recognize the current user
	 *
	 * @param array $data The data received in the submitted form.
	 *
	 * @return ?WP_User The recognized WP User object or "null" if not recognized.
	 */
	public function recognize_user( array $data = [] ): ?WP_User /* @phpstan-ignore missingType.iterableValue */ {
		$user = new User( $this->container );

		if ( null === $user->get_user_id() ) {
			$user_login = $data['user_login'] ?? ( $data['username'] ?? ( $data['login'] ?? '' ) );

			if ( ! empty( $user_login ) ) {
				if ( is_email( $user_login ) ) {
					$wp_user = get_user_by( 'email', $user_login );
				} else {
					$wp_user = get_user_by( 'login', $user_login );
				}

				if ( $wp_user instanceof WP_User ) {
					$user->set_user( $wp_user );
				}
			}

			/**
			 * Still nothing? Try to generate the WP_User
			 * object with provided data
			 */
			if ( null === $user->get_user_id() ) {
				$user_email = $data['user_email'] ?? '';
				$user_role  = $data['role'] ?? get_option( 'default_role', 'subscriber' );
				$wp_user    = new WP_User();

				$wp_user->data->user_login = $user_login;
				$wp_user->data->user_email = $user_email;
				$wp_user->roles            = [ $user_role ];

				$user->set_user( $wp_user );
			}
		}

		$this->set_user( $user );
		return $user->get_user();
	}

	/**
	 * Set User object
	 *
	 * @param User $user User object.
	 *
	 * @return void
	 */
	public function set_user( User $user ): void {
		$this->user    = $user;
		$this->wp_user = $user->get_user();
	}

	/**
	 * Set WP user
	 *
	 * @param WP_User $wp_user WP User object.
	 *
	 * @return void
	 */
	public function set_wp_user( WP_User $wp_user ): void {
		$this->wp_user = $wp_user;
		$this->user    = new User( $this->container, $wp_user );
	}
}
