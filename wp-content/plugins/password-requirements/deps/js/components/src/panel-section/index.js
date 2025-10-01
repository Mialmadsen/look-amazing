/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * Import styles
 */
import './styles.scss';

/**
 * PanelSection component
 *
 * @param {Object} properties          Component properties object.
 * @param {JSX}    properties.children Children components.
 * @param {string} properties.title    Section title.
 *
 * @return {JSX} PanelSection component.
 */
export const PanelSection = ( { children, title } ) => (
	<div className="tsc-panel-section">
		<h2 className="tsc-panel-section__title">
			{ title }
		</h2>
		<div className="tsc-panel-section__fields">
			{ children }
		</div>
	</div>
);

/**
 * Props validation
 */
PanelSection.propTypes = {
	children: PropTypes.element.isRequired,
	title: PropTypes.string.isRequired,
};
