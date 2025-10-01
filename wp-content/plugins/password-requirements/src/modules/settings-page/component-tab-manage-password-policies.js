/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { PasswordPolicySettings } from './component-password-policy-settings.js';

/**
 * TabManagePasswordPolicies component
 *
 * @param {Object}   properties             Component properties object.
 * @param {Object}   properties.settings    Plugin settings.
 * @param {Function} properties.setSettings Function (callback) used to update the settings.
 *
 * @return {JSX} TabManagePasswordPolicies component.
 */
export const TabManagePasswordPolicies = ( { settings, setSettings } ) => {
	/**
	 * Ensure we always have a policy to edit
	 */
	useEffect( () => {
		if ( 0 === Object.keys( settings.policies ).length ) {
			const policyKey = `d:${ Date.now().toString() }0000`;
			const { policy: policyTemplate } = settings.templates.policies;

			setSettings( {
				...settings,
				policies: {
					[ policyKey ]: Object.assign( {}, { key: policyKey, ...policyTemplate } ),
				},
			} );
		}
	}, [ settings, setSettings ] );

	// Get the policy to edit.
	const policy = settings.policies[ Object.keys( settings.policies )?.[ 0 ] ] ?? null;

	if ( null === policy ) {
		return null;
	}

	/**
	 * Render the component
	 */
	return (
		<PasswordPolicySettings
			data={ policy }

			/**
			 * Update the value
			 *
			 * @param {Object} updatedValues Updated values.
			 *
			 * @return {void}
			 */
			onChange={ ( updatedValues ) => {
				const updatedPolicies = Object.assign( {}, settings?.policies ?? {} );
				updatedPolicies[ policy.key ] = updatedValues;

				setSettings( {
					...settings,
					policies: updatedPolicies,
				} );
			} }
		/>
	);
};

/**
 * Props validation
 */
TabManagePasswordPolicies.propTypes = {
	settings: PropTypes.object.isRequired,
	setSettings: PropTypes.func.isRequired,
};
