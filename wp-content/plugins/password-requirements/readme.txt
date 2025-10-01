=== WP Password Policy ===
Contributors: teydeastudio, bartoszgadomski
Tags: password-policy, password-strength, strong-password, passwords, security
Requires at least: 6.6
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 3.3.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Plugin URI: https://wppasswordpolicy.com/?utm_source=WP+Password+Policy

Define advanced password policies, enforce strong password requirements, and improve your WordPress site's security.

== Description ==

**WP Password Policy** is the ultimate solution for WordPress administrators who want to ensure users always use strong, secure passwords. Protect your site from brute-force attacks, compromised credentials, and weak password practices by setting robust, customizable password policies.

**Key benefits:**

- Reduce risk of unauthorized access.
- Promote strong password hygiene.
- Comply with security best practices for WordPress, eCommerce, and multisite networks.
- Simple setup, flexible controls, and seamless integration.

**Features at a glance:**

- Enforce minimum password length and complexity.
- Set password expiration/maximum password age.
- Prevent use of common, weak passwords (PRO).
- Support for multisite networks.
- Support for WooCommerce (PRO).
- Define policies by user roles or individual users (PRO).
- Prevent password reuse (PRO).
- Translation-ready and easy to use.

Discover more at [wppasswordpolicy.com](https://wppasswordpolicy.com/?utm_source=WP+Password+Policy).

**Why strong password policies matter**

Weak passwords are one of the most common causes of WordPress site hacks. By enforcing strong password rules, you reduce the chances of data breaches, unauthorized access, and compliance issues. Whether you run a single blog, manage client sites, or operate a WooCommerce store, this plugin helps you protect your users and business.

== Features ==

**Free Features**

- **Enforce minimum password length:** Set and enforce the minimum number of characters for user passwords.
- **Password complexity requirements:** Require a mix of uppercase, lowercase, numbers, special characters, unique characters, and restrict use of parts of the username.
- **Set maximum password age:** Force users to update their passwords periodically (e.g., every 30 days).
- **Apply policies globally:** Enforce password rules for all users on your site with a single click.
- **Multisite/network support:** Compatible with both standard and multisite WordPress installations.
- **Translation-ready:** Localize the plugin into any language easily.

**PRO Features**

- **[Prevent password reuse](https://wppasswordpolicy.com/features/passwords-reuse-prevention/?utm_source=WP+Password+Policy):** Block users from reusing their previous passwords—encourage new, unique passwords every time.
- **[Custom password policies for user groups](https://wppasswordpolicy.com/features/dedicated-policies-by-user-and-or-role/?utm_source=WP+Password+Policy):** Assign different password rules for admins, editors, WooCommerce customers, or specific usernames.
- **[Block common, weak passwords](https://wppasswordpolicy.com/features/restricted-passwords-list/?utm_source=WP+Password+Policy):** Over 100,000 common passwords blacklisted—prevent users from choosing easy-to-guess passwords.
- **WooCommerce integration:** Enforce password policies on WooCommerce account pages, password reset, and registration forms.
- **Priority support and updates:** Get premium email support and frequent updates as a PRO user.

Upgrade and learn more about the PRO version at [wppasswordpolicy.com](https://wppasswordpolicy.com/pricing/?utm_source=WP+Password+Policy).

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/password-policy-and-complexity-requirements/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the ‘Plugins’ screen in WordPress.
3. Go to the ‘WP Password Policy’ settings page to configure your desired rules.
4. Save changes. Your password policy is now active!

== Frequently Asked Questions ==

= Does this plugin work with WooCommerce? =
Yes! The plugin integrates seamlessly with WooCommerce, enforcing password policies on customer accounts (PRO feature).

= Can I set different password rules for different user roles? =
Yes, with the PRO version you can assign different policies to administrators, editors, customers, or individual users.

= What happens if a user tries to set a weak password? =
They will receive a clear error message and guidance to meet the required password policy.

= Is the plugin compatible with WordPress Multisite? =
Absolutely! You can enforce password policies network-wide or on individual subsites.

= How do I prevent users from reusing old passwords? =
Upgrade to PRO and enable the “Prevent Password Reuse” feature.

== Screenshots ==

1. Password policy configuration overview.
2. Customizable password policy rules.
3. Password policy rules can be adjusted as needed.
4. Enforcement on user password forms.

== Video Tutorial ==

See the plugin in action:

https://www.youtube.com/watch?v=7g_hWHZ4IFs

== Changelog ==

= 3.3.0 (2025-09-19) =
* New feature: require users to provide their current password before changing it
* New feature: added the ability to exclude certain users from being covered by the password policy (through PHP filter); this is useful when certain users are managed externally and we don't want to enforce the password policy on them (for example: users who log in through an SSO provider)
* Compliance checks against the password policy refactored to avoid having duplicated logic in various modules
* Dependencies updated
* Code improvements

= 3.2.2 (2025-07-24) =
* Dependencies updated
* Code improvements

= 3.2.1 (2025-07-04) =
* Plugin's readme.txt file updated

= 3.2.0 (2025-07-01) =
* Network activation process improved
* Password expiry check on user interaction improved
* Automated, conditional logout after plugin settings changes are saved implemented for current user affected by the new policy
* Plugin container loader optimized to avoid duplicated instantiations
* Plugin name updated to avoid confusion, now matching the project's name
* Dependencies updated
* Code improvements

= 3.1.1 (2025-04-25) =
* Issue with nonce in the password reset form on password expiry fixed
* Settings screen style improvements
* Dependencies updated
* Code improvements

= 3.1.0 (2025-04-04) =
* Compatibility with WordPress 6.8 confirmed
* Issue of requesting the translated string too early fixed
* Ability to configure maximum password length introduced; allows to prevent denial-of-service attacks caused by hashing too long passwords
* Dependencies updated
* Code improvements

= 3.0.0 (2025-02-21) =
* The scenario where a user's password does not comply with the policy for reasons other than the minimum age, and the password age is unknown because the user has not changed the password since this plugin has been enabled, is now handled correctly
* Integration with new account registration form improved
* Password hint generation logic improved
* Dependencies updated
* Code improvements

= 2.7.1 (2024-11-25) =
* Plugin now checks whether the PRO version is activated; in case if it is, it stops loading itself
* Uninstall file removed as it was out of date and could conflict with the PRO version of the plugin

= 2.7.0 (2024-11-08) =
* Custom capabilities for managing the plugin settings implemented
* Compatibility with WordPress 6.7 confirmed
* Dependencies updated
* Code improvements

= 2.6.1 (2024-10-25) =
* JS dependency map and tree-shaking optimized
* PHP 7.4 compatibility fixes implemented

(For older records, see the `changelog.txt` file).
