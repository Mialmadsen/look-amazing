/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { FieldsGroup } from '@teydeastudio/components/src/fields-group/index.js';
import { IntegerControl } from '@teydeastudio/components/src/integer-control/index.js';
import { PanelSection } from '@teydeastudio/components/src/panel-section/index.js';

/**
 * WordPress dependencies
 */
import { CheckboxControl, Panel, ToggleControl, TextControl } from '@wordpress/components';
import { createInterpolateElement } from '@wordpress/element';
import { __, _n, sprintf } from '@wordpress/i18n';

/**
 * PasswordPolicySettings component
 *
 * @param {Object}   properties          Component properties object.
 * @param {Object}   properties.data     Component data object.
 * @param {Function} properties.onChange Function (callback) used to update the data.
 *
 * @return {JSX} PasswordPolicySettings component.
 */
export const PasswordPolicySettings = ( { data, onChange } ) => {
	/**
	 * Build the password complexity explanation string
	 *
	 * @return {string} Complexity explanation.
	 */
	const getComplexityExplanation = () => {
		const parts = [];

		if ( true === data[ 'ruleSettings.complexity.uppercase' ] ) {
			parts.push( __( 'uppercase letter(s)', 'password-requirements' ) );
		}

		if ( true === data[ 'ruleSettings.complexity.lowercase' ] ) {
			parts.push( __( 'lowercase letter(s)', 'password-requirements' ) );
		}

		if ( true === data[ 'ruleSettings.complexity.digit' ] ) {
			parts.push( __( 'base digit(s) (0 through 9)', 'password-requirements' ) );
		}

		if ( true === data[ 'ruleSettings.complexity.specialCharacter' ] ) {
			parts.push( __( 'special character(s)', 'password-requirements' ) );
		}

		if ( true === data[ 'ruleSettings.complexity.uniqueCharacters' ] ) {
			parts.push( sprintf(
				// Translators: %d - number of characters.
				_n(
					'%d unique (non-repeated) character',
					'%d unique (non-repeated) characters',
					data[ 'ruleSettings.minimumUniqueCharacters' ],
					'password-requirements',
				),
				data[ 'ruleSettings.minimumUniqueCharacters' ],
			) );
		}

		if ( true === data[ 'ruleSettings.complexity.consecutiveUserSymbols' ] ) {
			parts.push( sprintf(
				// Translators: %1$s - optional glue, %2$s - number of consecutive symbols of the user's name or display name allowed in the password.
				__( '%1$sallow up to %2$s from the user\'s name or display name', 'password-requirements' ),
				0 === parts.length
					? ''
					: sprintf( '%s ', __( 'and to', 'password-requirements' ) ),
				sprintf(
					// Translators: %d - number of symbols.
					_n(
						'%d consecutive symbol',
						'%d consecutive symbols',
						data[ 'ruleSettings.maximumConsecutiveUserSymbols' ],
						'password-requirements',
					),
					data[ 'ruleSettings.maximumConsecutiveUserSymbols' ],
				),
			) );
		}

		return 0 === parts.length
			? __( 'Currently, all complexity rules for this password policy are disabled.', 'password-requirements' )
			: sprintf(
				// Translators: %s - complexity explanation.
				__( 'Currently set to require: %s.', 'password-requirements' ),
				parts.join( ', ' )
			);
	};

	/**
	 * Render the component
	 */
	return (
		<div
			className="tsc-settings-tabs__container"
		>
			<Panel
				header={ sprintf(
					'%1$s: %2$s',
					(
						data.isActive
							? __( 'Policy', 'password-requirements' )
							: __( 'Inactive Policy', 'password-requirements' )
					),
					(
						'' === data.name
							? '-'
							: data.name
					),
				) }
			>
				<PanelSection
					title={ __( 'General settings', 'password-requirements' ) }
				>
					<FieldsGroup>
						<ToggleControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Activate this policy', 'password-requirements' ) }
							help={ __( 'You can deactivate any policy if you want to keep its settings but don\'t want to enforce it for users.', 'password-requirements' ) }
							checked={ data.isActive }
							onChange={ () => {
								onChange( {
									...data,
									isActive: ! data.isActive,
								} );
							} }
						/>
						<TextControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Policy name', 'password-requirements' ) }
							value={ data.name }
							help={ __( 'We suggest using a descriptive name.', 'password-requirements' ) }

							/**
							 * Update the value
							 *
							 * @param {string} updatedValue Updated value.
							 *
							 * @return {void}
							 */
							onChange={ ( updatedValue ) => {
								onChange( {
									...data,
									name: updatedValue,
								} );
							} }
						/>
					</FieldsGroup>
				</PanelSection>
				<PanelSection
					title={ __( 'Enabled rules', 'password-requirements' ) }
				>
					<FieldsGroup>
						<ToggleControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Enforce the minimum password length', 'password-requirements' ) }
							help={ sprintf(
								// Translators: %s - minimum password length (number of characters with text).
								__( 'Once enabled, the users\' password length must equal or exceed the defined value (currently set to %s).', 'password-requirements' ),
								sprintf(
									// Translators: %d - number of characters.
									_n(
										'%d character',
										'%d characters',
										data[ 'ruleSettings.minimumLength' ],
										'password-requirements',
									),
									data[ 'ruleSettings.minimumLength' ],
								),
							) }
							checked={ data[ 'rules.minimumLength' ] }
							onChange={ () => {
								onChange( {
									...data,
									'rules.minimumLength': ! data[ 'rules.minimumLength' ],
								} );
							} }
						/>
						<ToggleControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Enforce the maximum password length', 'password-requirements' ) }
							help={ sprintf(
								// Translators: %s - maximum password length (number of characters with text).
								__( 'Once enabled, the users\' password length must be equal or less than the defined value (currently set to %s).', 'password-requirements' ),
								sprintf(
									// Translators: %d - number of characters.
									_n(
										'%d character',
										'%d characters',
										data[ 'ruleSettings.maximumLength' ],
										'password-requirements',
									),
									data[ 'ruleSettings.maximumLength' ],
								),
							) }
							checked={ data[ 'rules.maximumLength' ] }
							onChange={ () => {
								onChange( {
									...data,
									'rules.maximumLength': ! data[ 'rules.maximumLength' ],
								} );
							} }
						/>
						<ToggleControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Enforce the minimum password age', 'password-requirements' ) }
							help={ sprintf(
								// Translators: %s - minimum password age (number of days with text).
								__( 'Once enabled, users can only change their passwords if the current password has been used for at least a defined period (currently set to %s).', 'password-requirements' ),
								sprintf(
									// Translators: %d - number of days.
									_n(
										'%d day',
										'%d days',
										data[ 'ruleSettings.minimumAge' ],
										'password-requirements',
									),
									data[ 'ruleSettings.minimumAge' ],
								),
							) }
							checked={ data[ 'rules.minimumAge' ] }
							onChange={ () => {
								onChange( {
									...data,
									'rules.minimumAge': ! data[ 'rules.minimumAge' ],
								} );
							} }
						/>
						<ToggleControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Enforce the maximum password age', 'password-requirements' ) }
							help={ sprintf(
								// Translators: %s - maximum password age (number of days with text).
								__( 'Once enabled, users will have to change their passwords if the current password has been in use for a defined period (currently set to %s).', 'password-requirements' ),
								sprintf(
									// Translators: %d - number of days.
									_n(
										'%d day',
										'%d days',
										data[ 'ruleSettings.maximumAge' ],
										'password-requirements',
									),
									data[ 'ruleSettings.maximumAge' ],
								),
							) }
							checked={ data[ 'rules.maximumAge' ] }
							onChange={ () => {
								onChange( {
									...data,
									'rules.maximumAge': ! data[ 'rules.maximumAge' ],
								} );
							} }
						/>
						<ToggleControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Enforce the password complexity requirements', 'password-requirements' ) }
							help={ sprintf(
								// Translators: %s - complexity explanation.
								__( 'Once enabled, users\' password must meet the complexity requirements. %s', 'password-requirements' ),
								getComplexityExplanation(),
							) }
							checked={ data[ 'rules.complexity' ] }
							onChange={ () => {
								onChange( {
									...data,
									'rules.complexity': ! data[ 'rules.complexity' ],
								} );
							} }
						/>
						<ToggleControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Require current password when changing password', 'password-requirements' ) }
							help={ __( 'Once enabled, users must provide their current password to change it; applies to the User Profile screen.', 'password-requirements' ) }
							checked={ data[ 'rules.requireCurrentPassword' ] }
							onChange={ () => {
								onChange( {
									...data,
									'rules.requireCurrentPassword': ! data[ 'rules.requireCurrentPassword' ],
								} );
							} }
						/>
					</FieldsGroup>
				</PanelSection>
				<PanelSection
					title={ __( 'Rule settings', 'password-requirements' ) }
				>
					<FieldsGroup>
						<IntegerControl
							label={ __( 'Minimum password length', 'password-requirements' ) }
							help={ __( 'Number of characters required in passwords; a valid value should be between 1 and 50.', 'password-requirements' ) }
							min={ 1 }
							max={ 50 }
							value={ data[ 'ruleSettings.minimumLength' ] }
							defaultValue={ 10 }

							/**
							 * Update the value
							 *
							 * @param {number} updatedValue Updated value.
							 *
							 * @return {void}
							 */
							onChange={ ( updatedValue ) => {
								onChange( {
									...data,
									'ruleSettings.minimumLength': updatedValue,
								} );
							} }
						/>
						<IntegerControl
							label={ __( 'Maximum password length', 'password-requirements' ) }
							help={ __( 'Maximum number of characters allowed in passwords; a valid value should be between 64 and 512.', 'password-requirements' ) }
							min={ 64 }
							max={ 512 }
							value={ data[ 'ruleSettings.maximumLength' ] }
							defaultValue={ 256 }

							/**
							 * Update the value
							 *
							 * @param {number} updatedValue Updated value.
							 *
							 * @return {void}
							 */
							onChange={ ( updatedValue ) => {
								onChange( {
									...data,
									'ruleSettings.maximumLength': updatedValue,
								} );
							} }
						/>
						<IntegerControl
							label={ __( 'Minimum password age', 'password-requirements' ) }
							help={ __( 'Number of days; a valid value should be between 1 and 1000.', 'password-requirements' ) }
							min={ 1 }
							max={ 1000 }
							value={ data[ 'ruleSettings.minimumAge' ] }
							defaultValue={ 2 }

							/**
							 * Update the value
							 *
							 * @param {string} updatedValue Updated value.
							 *
							 * @return {void}
							 */
							onChange={ ( updatedValue ) => {
								onChange( {
									...data,
									'ruleSettings.minimumAge': updatedValue,
								} );
							} }
						/>
						<IntegerControl
							label={ __( 'Maximum password age', 'password-requirements' ) }
							help={ __( 'Number of days; a valid value should be between 1 and 1000.', 'password-requirements' ) }
							min={ 1 }
							max={ 1000 }
							value={ data[ 'ruleSettings.maximumAge' ] }
							defaultValue={ 30 }

							/**
							 * Update the value
							 *
							 * @param {string} updatedValue Updated value.
							 *
							 * @return {void}
							 */
							onChange={ ( updatedValue ) => {
								onChange( {
									...data,
									'ruleSettings.maximumAge': updatedValue,
								} );
							} }
						/>
						<FieldsGroup
							label={ __( 'Password complexity requirements', 'password-requirements' ) }
							withBaseControl
							withReducedGap
						>
							<CheckboxControl
								__nextHasNoMarginBottom
								__next40pxDefaultSize
								label={ __( 'Uppercase letter(s) required', 'password-requirements' ) }
								checked={ data[ 'ruleSettings.complexity.uppercase' ] }
								onChange={ () => {
									onChange( {
										...data,
										'ruleSettings.complexity.uppercase': ! data[ 'ruleSettings.complexity.uppercase' ],
									} );
								} }
							/>
							<CheckboxControl
								__nextHasNoMarginBottom
								__next40pxDefaultSize
								label={ __( 'Lowercase letter(s) required', 'password-requirements' ) }
								checked={ data[ 'ruleSettings.complexity.lowercase' ] }
								onChange={ () => {
									onChange( {
										...data,
										'ruleSettings.complexity.lowercase': ! data[ 'ruleSettings.complexity.lowercase' ],
									} );
								} }
							/>
							<CheckboxControl
								__nextHasNoMarginBottom
								__next40pxDefaultSize
								label={ __( 'Base digit(s) (0 through 9) required', 'password-requirements' ) }
								checked={ data[ 'ruleSettings.complexity.digit' ] }
								onChange={ () => {
									onChange( {
										...data,
										'ruleSettings.complexity.digit': ! data[ 'ruleSettings.complexity.digit' ],
									} );
								} }
							/>
							<CheckboxControl
								__nextHasNoMarginBottom
								__next40pxDefaultSize
								label={ sprintf(
									// Translators: %s - minimum number of unique (non-repeated) characters in password, with text.
									__( 'At least %s required', 'password-requirements' ),
									sprintf(
										// Translators: %d - number of characters.
										_n(
											'%d unique (non-repeated) character',
											'%d unique (non-repeated) characters',
											data[ 'ruleSettings.minimumUniqueCharacters' ],
											'password-requirements',
										),
										data[ 'ruleSettings.minimumUniqueCharacters' ],
									),
								) }
								checked={ data[ 'ruleSettings.complexity.uniqueCharacters' ] }
								onChange={ () => {
									onChange( {
										...data,
										'ruleSettings.complexity.uniqueCharacters': ! data[ 'ruleSettings.complexity.uniqueCharacters' ],
									} );
								} }
							/>
							<CheckboxControl
								__nextHasNoMarginBottom
								__next40pxDefaultSize
								label={ sprintf(
									// Translators: %s - number of consecutive symbols of the user's name or display name allowed in the password, with text.
									__( 'Up to %s from the user\'s name or display name allowed', 'password-requirements' ),
									sprintf(
										// Translators: %d - number of symbols.
										_n(
											'%d consecutive symbol',
											'%d consecutive symbols',
											data[ 'ruleSettings.maximumConsecutiveUserSymbols' ],
											'password-requirements',
										),
										data[ 'ruleSettings.maximumConsecutiveUserSymbols' ],
									),
								) }
								checked={ data[ 'ruleSettings.complexity.consecutiveUserSymbols' ] }
								onChange={ () => {
									onChange( {
										...data,
										'ruleSettings.complexity.consecutiveUserSymbols': ! data[ 'ruleSettings.complexity.consecutiveUserSymbols' ],
									} );
								} }
							/>
							<CheckboxControl
								__nextHasNoMarginBottom
								__next40pxDefaultSize
								label={ __( 'Special character(s) required', 'password-requirements' ) }
								checked={ data[ 'ruleSettings.complexity.specialCharacter' ] }
								help={ createInterpolateElement(
									__( 'Special character: one of punctuation characters that are present on standard US keyboard. See: <a>Password Special Characters</a> for more details.', 'password-requirements' ),
									{
										a: <a href="https://owasp.org/www-community/password-special-characters" target="_blank" rel="noreferrer noopener" />, // eslint-disable-line jsx-a11y/anchor-has-content
									}
								) }
								onChange={ () => {
									onChange( {
										...data,
										'ruleSettings.complexity.specialCharacter': ! data[ 'ruleSettings.complexity.specialCharacter' ],
									} );
								} }
							/>
						</FieldsGroup>
						<IntegerControl
							label={ __( 'Minimum number of unique (non-repeated) characters in password', 'password-requirements' ) }
							help={ __( 'Example: in the "aabc" password, three characters are unique (non-repeated); a valid value should be between 1 and 50.', 'password-requirements' ) }
							min={ 1 }
							max={ 50 }
							value={ data[ 'ruleSettings.minimumUniqueCharacters' ] }
							defaultValue={ 6 }

							/**
							 * Update the value
							 *
							 * @param {string} updatedValue Updated value.
							 *
							 * @return {void}
							 */
							onChange={ ( updatedValue ) => {
								onChange( {
									...data,
									'ruleSettings.minimumUniqueCharacters': updatedValue,
								} );
							} }
						/>
						<IntegerControl
							label={ __( 'Number of consecutive symbols of the user\'s name or display name allowed in the password', 'password-requirements' ) }
							help={ __( 'A valid value should be between 0 and 50. Examples: if "0" is chosen, all characters used in user name or display name will not be allowed in user\'s password; if "2" is chosen and user name is "Bart", password can contain "ba", "ar", and "rt", but not "bar" or "art".', 'password-requirements' ) }
							min={ 0 }
							max={ 50 }
							value={ data[ 'ruleSettings.maximumConsecutiveUserSymbols' ] }
							defaultValue={ 4 }

							/**
							 * Update the value
							 *
							 * @param {string} updatedValue Updated value.
							 *
							 * @return {void}
							 */
							onChange={ ( updatedValue ) => {
								onChange( {
									...data,
									'ruleSettings.maximumConsecutiveUserSymbols': updatedValue,
								} );
							} }
						/>
					</FieldsGroup>
				</PanelSection>
			</Panel>
		</div>
	);
};

/**
 * Props validation
 */
PasswordPolicySettings.propTypes = {
	data: PropTypes.object.isRequired,
	onChange: PropTypes.func.isRequired,
};
