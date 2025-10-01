<?php
/**
 * Password_Policy class
 *
 * @package Teydea_Studio\Password_Requirements
 */

namespace Teydea_Studio\Password_Requirements;

use DateTime;
use DateTimeZone;
use Teydea_Studio\Password_Requirements\Dependencies\Utils;
use Teydea_Studio\Password_Requirements\Dependencies\Validatable_Fields;
use WP_User;

/**
 * The "Password_Policy" class
 */
class Password_Policy {
	/**
	 * Container instance
	 *
	 * @var Utils\Container
	 */
	protected Utils\Container $container;

	/**
	 * User to apply the password policy to
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
	 * Passwords Store object
	 *
	 * @var Passwords_Store
	 */
	protected Passwords_Store $passwords_store;

	/**
	 * Settings of the password policy which applies to the given user; null if there's no any policy that applies
	 *
	 * @var ?Validatable_Fields\Fields_Group
	 */
	protected ?Validatable_Fields\Fields_Group $policy = null;

	/**
	 * Construct the object
	 *
	 * @param Utils\Container $container       Container instance.
	 * @param User            $user            User to apply the password policy to.
	 * @param Settings        $settings        Plugin settings object.
	 * @param Passwords_Store $passwords_store Passwords Store object.
	 */
	public function __construct( Utils\Container $container, User $user, Settings $settings, Passwords_Store $passwords_store ) {
		if ( ! $settings->has_validation_errors() ) {
			$this->container       = $container;
			$this->settings        = $settings;
			$this->user            = $user;
			$this->passwords_store = $passwords_store;
			$this->policy          = $this->determine_policy();
		}
	}

	/**
	 * Determine the password policy
	 *
	 * @return ?Validatable_Fields\Fields_Group Settings of the password policy; null if there's no any policy that applies.
	 */
	protected function determine_policy(): ?Validatable_Fields\Fields_Group {
		/**
		 * Allow other plugins and modules to exclude certain
		 * user from being covered by the password policy
		 *
		 * This is useful when certain users are managed
		 * externally and we don't want to enforce the
		 * password policy on them (for example: users
		 * who log in through an SSO provider).
		 *
		 * @param bool     $is_user_excluded Whether the user is excluded from the password policy ("true") or not ("false").
		 * @param ?WP_User $user             User object, or null if not available.
		 */
		$is_user_excluded = apply_filters( 'password_requirements__is_user_excluded_from_policy', false, $this->user->get_user() );

		if ( true === $is_user_excluded ) {
			return null;
		}

		/**
		 * Determine the policy that applies to this user
		 */
		$fields_group = $this->settings->get_fields_group( 'policies' );
		$policies     = null !== $fields_group ? $fields_group->get_fields() : null;

		/** @var ?Validatable_Fields\Fields_Group $policy */
		$policy = null === $policies ? null : array_shift( $policies );

		return null === $policy || false === $policy->get_field_value( 'is_active' )
			? null
			: $policy;
	}

	/**
	 * Check if user can change their password
	 *
	 * If the "minimum password age" rule is enabled, user should not be allowed to change nor reset
	 * their password after a defined period since the last password change.
	 *
	 * @return bool Whether the user can change their password ("true") or not ("false").
	 */
	public function can_user_change_password(): bool {
		if ( null !== $this->policy && true === $this->policy->get_field_value( 'rules.minimum_age' ) ) {
			/** @var int $min_age */
			$min_age         = $this->policy->get_field_value( 'rule_settings.minimum_age' );
			$last_changed_at = $this->passwords_store->get_password_changed_at();

			if ( $last_changed_at instanceof DateTime && false === $this->check_has_minimum_age( $last_changed_at, $min_age ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check if user should change their password
	 *
	 * If the "maximum password age" rule is enabled, user should change their password
	 * no later than a defined period since the last password change
	 *
	 * @return bool Whether the user should change their password ("true") or not ("false").
	 */
	public function should_user_change_password(): bool {
		if ( null !== $this->policy && true === $this->policy->get_field_value( 'rules.maximum_age' ) ) {
			/** @var int $max_age */
			$max_age         = $this->policy->get_field_value( 'rule_settings.maximum_age' );
			$last_changed_at = $this->passwords_store->get_password_changed_at();

			if ( $last_changed_at instanceof DateTime ) {
				// Check if the current password is in use longer than allowed.
				return ! $this->check_has_not_exceeded_maximum_age( $last_changed_at, $max_age );
			} else {
				// We don't know when user changed their password last time; request a new password immediately.
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if provided password is compliant with the password policy
	 * that applies to this user
	 *
	 * @param string $password User's password.
	 * @param bool   $is_new   Whether the a given password is new ("true") or not ("false", default).
	 *
	 * @return bool Whether the password is compliant with the password policy or not.
	 */
	public function is_password_compliant( string $password, bool $is_new = false ): bool {
		/**
		 * There's no any password policy that applies to this user
		 */
		if ( null === $this->policy ) {
			return true;
		}

		/**
		 * Check password complexity
		 *
		 * Applies:
		 * - when enabled
		 */
		if ( true === $this->policy->get_field_value( 'rules.complexity' ) ) {
			/**
			 * Check if password contains at least one digit
			 */
			if ( true === $this->policy->get_field_value( 'rule_settings.complexity.digit' ) && false === $this->check_has_digit( $password ) ) {
				return false;
			}

			/**
			 * Check if password contains at least one lowercase character
			 */
			if ( true === $this->policy->get_field_value( 'rule_settings.complexity.lowercase' ) && false === $this->check_has_lowercase_character( $password ) ) {
				return false;
			}

			/**
			 * Check if password contains at least one uppercase character
			 */
			if ( true === $this->policy->get_field_value( 'rule_settings.complexity.uppercase' ) && false === $this->check_has_uppercase_character( $password ) ) {
				return false;
			}

			/**
			 * Check if password contains at least one special character
			 */
			if ( true === $this->policy->get_field_value( 'rule_settings.complexity.special_character' ) && false === $this->check_has_special_character( $password ) ) {
				return false;
			}

			/**
			 * Check if password contains equally or more than the defined minimum number of unique characters
			 */
			if ( true === $this->policy->get_field_value( 'rule_settings.complexity.unique_characters' ) ) {
				/** @var int $min_unique_characters */
				$min_unique_characters = $this->policy->get_field_value( 'rule_settings.minimum_unique_characters' );

				if ( false === $this->check_has_minimum_unique_characters( $password, $min_unique_characters ) ) {
					return false;
				}
			}

			/**
			 * Check if password contains no more than allowed maximum number of consecutive user symbols
			 */
			if ( true === $this->policy->get_field_value( 'rule_settings.complexity.consecutive_user_symbols' ) ) {
				/** @var int $max_consecutive_user_symbols */
				$max_consecutive_user_symbols = $this->policy->get_field_value( 'rule_settings.maximum_consecutive_user_symbols' );

				if ( false === $this->check_has_not_exceeded_maximum_consecutive_user_symbols( $password, $max_consecutive_user_symbols ) ) {
					return false;
				}
			}
		}

		/**
		 * Check if password is in use longer than a maximum allowed period
		 *
		 * Applies:
		 * - when enabled, and user is logging in
		 */
		if ( true === $this->policy->get_field_value( 'rules.maximum_age' ) && false === $is_new ) {
			/** @var int $max_age */
			$max_age         = $this->policy->get_field_value( 'rule_settings.maximum_age' );
			$last_changed_at = $this->passwords_store->get_password_changed_at();

			if ( $last_changed_at instanceof DateTime && false === $this->check_has_not_exceeded_maximum_age( $last_changed_at, $max_age ) ) {
				return false;
			}
		}

		/**
		 * Check if password is in use shorter than a defined "minimum age", if user is changing password
		 *
		 * Applies:
		 * - when enabled, and user is changing password
		 */
		if ( true === $this->policy->get_field_value( 'rules.minimum_age' ) && true === $is_new ) {
			/** @var int $min_age */
			$min_age         = $this->policy->get_field_value( 'rule_settings.minimum_age' );
			$last_changed_at = $this->passwords_store->get_password_changed_at();

			if ( $last_changed_at instanceof DateTime && false === $this->check_has_minimum_age( $last_changed_at, $min_age ) ) {
				return false;
			}
		}

		/**
		 * Check password minimum length
		 *
		 * Applies:
		 * - when enabled
		 */
		if ( true === $this->policy->get_field_value( 'rules.minimum_length' ) ) {
			/** @var int $min_length */
			$min_length = $this->policy->get_field_value( 'rule_settings.minimum_length' );

			if ( false === $this->check_has_minimum_length( $password, $min_length ) ) {
				return false;
			}
		}

		/**
		 * Check password maximum length
		 *
		 * Applies:
		 * - when enabled
		 */
		if ( true === $this->policy->get_field_value( 'rules.maximum_length' ) ) {
			/** @var int $max_length */
			$max_length = $this->policy->get_field_value( 'rule_settings.maximum_length' );

			if ( false === $this->check_has_maximum_length( $password, $max_length ) ) {
				return false;
			}
		}

		// All checks passed!
		return true;
	}

	/**
	 * Check if password contains at least one digit
	 *
	 * @param string $password Password to validate.
	 *
	 * @return bool Whether the password is passing this check or not.
	 */
	protected function check_has_digit( string $password ): bool {
		return 1 === preg_match( '/\d/', $password );
	}

	/**
	 * Check if password contains at least one lowercase character
	 *
	 * @param string $password Password to validate.
	 *
	 * @return bool Whether the password is passing this check or not.
	 */
	protected function check_has_lowercase_character( string $password ): bool {
		return 1 === preg_match( '/[a-z]/', $password );
	}

	/**
	 * Check if password contains at least one uppercase character
	 *
	 * @param string $password Password to validate.
	 *
	 * @return bool Whether the password is passing this check or not.
	 */
	protected function check_has_uppercase_character( string $password ): bool {
		return 1 === preg_match( '/[A-Z]/', $password );
	}

	/**
	 * Check if password contains at least one special character
	 *
	 * @param string $password Password to validate.
	 *
	 * @return bool Whether the password is passing this check or not.
	 *
	 * @see https://owasp.org/www-community/password-special-characters
	 */
	protected function check_has_special_character( string $password ): bool {
		return 1 === preg_match( '/[!"#$%&\'()*+,-.\/:;<=>?@[\]^_`{|}~\\\\]/', $password );
	}

	/**
	 * Check if password is in use longer than a defined "maximum age"
	 *
	 * @param DateTime $last_changed_at DateTime object representing the time of the last change of the user's password.
	 * @param int      $max_age         Maximum allowed period (in days).
	 *
	 * @return bool Whether the password is passing this check or not.
	 */
	protected function check_has_not_exceeded_maximum_age( DateTime $last_changed_at, int $max_age ): bool {
		$diff = $last_changed_at->diff( new DateTime( 'now', new DateTimeZone( '+00:00' ) ) );
		return $diff->format( '%a' ) <= $max_age;
	}

	/**
	 * Check if password is in use shorter than a defined "minimum age"
	 *
	 * @param DateTime $last_changed_at DateTime object representing the time of the last change of the user's password.
	 * @param int      $min_age         Minimum allowed period.
	 *
	 * @return bool Whether the password is passing this check or not.
	 */
	protected function check_has_minimum_age( DateTime $last_changed_at, int $min_age ): bool {
		$diff = $last_changed_at->diff( new DateTime( 'now', new DateTimeZone( '+00:00' ) ) );
		return $diff->format( '%a' ) >= $min_age;
	}

	/**
	 * Check if password is long enough
	 *
	 * @param string $password   Password to validate.
	 * @param int    $min_length Minimum allowed length.
	 *
	 * @return bool Whether the password is passing this check or not.
	 */
	protected function check_has_minimum_length( string $password, int $min_length ): bool {
		return $min_length <= strlen( $password );
	}

	/**
	 * Check if password is not too long
	 *
	 * @param string $password   Password to validate.
	 * @param int    $max_length Maximum allowed length.
	 *
	 * @return bool Whether the password is passing this check or not.
	 */
	protected function check_has_maximum_length( string $password, int $max_length ): bool {
		return $max_length >= strlen( $password );
	}

	/**
	 * Check if password has minimum number of unique characters
	 *
	 * @param string $password              Password to validate.
	 * @param int    $min_unique_characters Minimum number of unique characters required.
	 *
	 * @return bool Whether the password is passing this check or not.
	 */
	protected function check_has_minimum_unique_characters( string $password, int $min_unique_characters ): bool {
		$chars = str_split( $password );
		return $min_unique_characters <= count( array_unique( $chars ) );
	}

	/**
	 * Check if password has not exceeded the maximum number of allowed consecutive user symbols
	 *
	 * @param string $password                     Password to validate.
	 * @param int    $max_consecutive_user_symbols Maximum number of allowed consecutive user symbols.
	 *
	 * @return bool Whether the password is passing this check or not.
	 */
	protected function check_has_not_exceeded_maximum_consecutive_user_symbols( string $password, int $max_consecutive_user_symbols ): bool {
		if ( strlen( $password ) <= $max_consecutive_user_symbols ) {
			return true;
		}

		$user = $this->user->get_user();

		if ( $user instanceof WP_User ) {
			$user_symbols = [
				$user->user_login,
				$user->display_name,
			];

			// Array of all possible strings with consecutive user symbols.
			$consecutive_pieces = [];

			// Ensure we check all possible user symbols.
			foreach ( $user_symbols as $user_symbol ) {
				$offset             = 0;
				$user_symbol_length = strlen( $user_symbol );

				while ( $offset + $max_consecutive_user_symbols <= $user_symbol_length ) {
					$consecutive_pieces[] = substr( $user_symbol, $offset, $max_consecutive_user_symbols );
					++$offset;
				}
			}

			// Test password against recognized consecutive pieces.
			foreach ( $consecutive_pieces as $piece ) {
				if ( Utils\Strings::str_contains( $password, $piece ) ) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Get hint for creating a compliant password
	 *
	 * @param string[] $hints Array of predefined hints.
	 *
	 * @return ?string The password hint text.
	 */
	public function get_hint( array $hints = [] ): ?string {
		if ( null === $this->policy ) {
			return null;
		}

		/**
		 * Password minimum length
		 */
		if ( true === $this->policy->get_field_value( 'rules.minimum_length' ) ) {
			/** @var int $min_length */
			$min_length = $this->policy->get_field_value( 'rule_settings.minimum_length' );
			$hints[]    = sprintf(
				// Translators: %s - minimum password length (number of characters).
				__( 'should be at least %s long', 'password-requirements' ),
				sprintf(
					// Translators: %d - number of characters.
					_n(
						'%d character',
						'%d characters',
						$min_length,
						'password-requirements',
					),
					$min_length,
				),
			);
		}

		/**
		 * Password complexity
		 */
		if ( true === $this->policy->get_field_value( 'rules.complexity' ) ) {
			$complexity_hints = [];

			// Digit.
			if ( true === $this->policy->get_field_value( 'rule_settings.complexity.digit' ) ) {
				$complexity_hints[] = __( 'base digit(s) (0-9)', 'password-requirements' );
			}

			// Lowercase character.
			if ( true === $this->policy->get_field_value( 'rule_settings.complexity.lowercase' ) ) {
				$complexity_hints[] = __( 'lowercase letter(s) (a-z)', 'password-requirements' );
			}

			// Uppercase character.
			if ( true === $this->policy->get_field_value( 'rule_settings.complexity.uppercase' ) ) {
				$complexity_hints[] = __( 'uppercase letter(s) (A-Z)', 'password-requirements' );
			}

			// Special character.
			if ( true === $this->policy->get_field_value( 'rule_settings.complexity.special_character' ) ) {
				$complexity_hints[] = sprintf( '<a href="https://owasp.org/www-community/password-special-characters" target="_blank" rel="noopener noreferrer">%s</a>', __( 'special character(s)', 'password-requirements' ) );
			}

			// Unique characters.
			if ( true === $this->policy->get_field_value( 'rule_settings.complexity.unique_characters' ) ) {
				/** @var int $min_unique_characters */
				$min_unique_characters = $this->policy->get_field_value( 'rule_settings.minimum_unique_characters' );
				$complexity_hints[]    = sprintf(
					// Translators: %d - number of characters.
					_n(
						'%d unique (non-repeated) character',
						'%d unique (non-repeated) characters',
						$min_unique_characters,
						'password-requirements',
					),
					$min_unique_characters,
				);
			}

			// Merge complexity hints into one hint.
			if ( ! empty( $complexity_hints ) ) {
				// Translators: %s - password complexity hints.
				$hints[] = sprintf( __( 'must contain %s', 'password-requirements' ), implode( ', ', $complexity_hints ) );
			}

			/**
			 * Consecutive symbols in password
			 */
			if ( true === $this->policy->get_field_value( 'rule_settings.complexity.consecutive_user_symbols' ) ) {
				/** @var int $max_consecutive_user_symbols */
				$max_consecutive_user_symbols = $this->policy->get_field_value( 'rule_settings.maximum_consecutive_user_symbols' );
				$hints[]                      = sprintf(
					// Translators: %s - maximum consecutive symbols.
					__( 'cannot contain more than %s of your user name or display name', 'password-requirements' ),
					sprintf(
						// Translators: %d - number of symbols.
						_n(
							'%d consecutive symbol',
							'%d consecutive symbols',
							$max_consecutive_user_symbols,
							'password-requirements',
						),
						$max_consecutive_user_symbols,
					),
				);
			}
		}

		// Return password hint.
		return empty( $hints ) ? null : sprintf( 'Hint: password %s.', implode( '; ', $hints ) );
	}

	/**
	 * Check whether the current password is required when changing the password
	 *
	 * @return bool Whether the current password is required when changing the password ("true") or not ("false").
	 */
	public function is_current_password_required(): bool {
		return null !== $this->policy && true === $this->policy->get_field_value( 'rules.require_current_password' );
	}
}
