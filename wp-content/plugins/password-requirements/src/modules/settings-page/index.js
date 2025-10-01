/**
 * External dependencies
 */
import { render } from '@teydeastudio/utils/src/render.js';
import { SettingsContainer } from '@teydeastudio/components/src/settings-container/index.js';
import { SettingsTabs } from '@teydeastudio/components/src/settings-tabs/index.js';
import { UpsellPanel } from '@teydeastudio/components/src/upsell-panel/index.js';
import { withSettings } from '@teydeastudio/components/src/with-settings/index.js';

/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { addAction, addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { TabManagePasswordPolicies } from './component-tab-manage-password-policies.js';

/**
 * Filter the settings page tabs configuration to include
 * the password policy settings
 */
addFilter(
	'password_requirements__settings_page_tabs',
	'teydeastudio/password-requirements/settings-page',

	/**
	 * Filter the settings page tabs configuration
	 *
	 * @param {Array}    tabsConfig  Settings page tabs configuration.
	 * @param {Object}   settings    Settings object.
	 * @param {Function} setSettings Settings setter.
	 *
	 * @return {Array} Updated settings page tabs configuration.
	 */
	( tabsConfig, settings, setSettings ) => {
		/**
		 * Add custom tab configuration to
		 * the filtered array
		 */
		tabsConfig.push( {
			name: 'password-policy',
			title: __( 'Manage password policy', 'password-requirements' ),
			component: (
				<TabManagePasswordPolicies
					settings={ settings }
					setSettings={ setSettings }
				/>
			),
		} );

		return tabsConfig;
	}
);

/**
 * Render the "upsell" panel
 */
addFilter(
	'password_requirements__upsell_panel',
	'teydeastudio/password-requirements/settings-page',

	/**
	 * Render the "upsell" panel
	 *
	 * @param {JSX} panel The "upsell" panel.
	 *
	 * @return {JSX} Updated "upsell" panel.
	 */
	( panel ) => {
		// Load the panel only if PRO version of the plugin is not active.
		if ( ! window?.teydeaStudio?.passwordRequirements?.plugin?.isPro ) {
			panel = (
				<UpsellPanel
					url="https://wppasswordpolicy.com/pricing/?utm_source=WP+Password+Policy"
					benefits={ [
						<a
							key="dedicated-policies-by-user-and-or-role"
							href="https://wppasswordpolicy.com/features/dedicated-policies-by-user-and-or-role/"
							target="_blank"
							rel="noreferrer"
						>
							{ __( 'Create Unlimited, Dedicated Password Policies for Different User Groups', 'password-requirements' ) }
						</a>,
						<a
							key="passwords-reuse-prevention"
							href="https://wppasswordpolicy.com/features/passwords-reuse-prevention/"
							target="_blank"
							rel="noreferrer"
						>
							{ __( 'Prevent Reusing the Same Passwords', 'password-requirements' ) }
						</a>,
						<a
							key="restricted-passwords-list"
							href="https://wppasswordpolicy.com/features/restricted-passwords-list/"
							target="_blank"
							rel="noreferrer"
						>
							{ __( 'Prevent Usage of Common, Weak Passwords', 'password-requirements' ) }
						</a>,
						__( 'Full integration with WooCommerce', 'password-requirements' ),
						__( 'Access to PRO updates and our premium support', 'password-requirements' ),
					] }
				/>
			);
		}

		// Return updated panel component.
		return panel;
	},
);

/**
 * Check whether user should be logged-out after the plugin
 * settings has been saved.
 *
 * This might be the case after the plugin has been just
 * enabled, user password age is not known, and the "max
 * password age" rule has been enabled.
 *
 * In such case, we need to ask user to create a new password.
 */
addAction(
	'password_requirements__settings_saved',
	'teydeastudio/password-requirements/settings-page',
	async () => {
		await apiFetch( { path: '/password-requirements/v1/should-change-password' } )
			.then( ( response ) => {
				if ( '' !== response ) {
					window.location.replace( response );
				}

				return response;
			} );
	},
);

/**
 * Define the product data
 */
const product = {
	key: 'passwordRequirements',
	type: 'plugin',
};

// Destructure the product object.
const { key: productKey } = product;

// Collect the necessary data.
const { pageTitle } = window.teydeaStudio[ productKey ].settingsPage;

/**
 * SettingsPage component
 *
 * @return {JSX}
 */
const SettingsPage = withSettings( ( { SaveSettingsButton, setSettings, settings } ) => {
	/**
	 * Render the component
	 */
	return (
		<SettingsContainer
			actions={ <SaveSettingsButton /> }
			pageTitle={ pageTitle }
			product={ product }
		>
			<SettingsTabs
				product={ product }
				settings={ settings }
				setSettings={ setSettings }
			/>
		</SettingsContainer>
	);
} );

/**
 * Render the settings page
 */
render(
	<SettingsPage
		product={ product }

		/**
		 * LoadingContainer component
		 *
		 * @param {JSX} children Children to render.
		 *
		 * @return {JSX} LoadingContainer component.
		 */
		LoadingContainer={ ( { children } ) => (
			<SettingsContainer
				pageTitle={ pageTitle }
				product={ product }
			>
				{ children }
			</SettingsContainer>
		) }
	/>,
	document.querySelector( 'div#password-requirements-settings-page' ),
);
