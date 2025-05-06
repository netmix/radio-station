/**
 * === Radio Clock Block ===
 */
(() => {

	const rs_el = window.wp.element.createElement;
	const rs__ = window.wp.i18n.__;
	const { serverSideRender: ServerSideRender } = window.wp;
	const { registerBlockType } = window.wp.blocks;
	const { InspectorControls } = window.wp.blockEditor;
	const { Fragment } = window.wp.element;
	const { BaseControl, TextControl, SelectControl, RadioControl, RangeControl, ToggleControl, Panel, PanelBody, PanelRow } = window.wp.components;

	registerBlockType( 'radio-station/clock', {

		/* --- Block Settings --- */
		title: '[Radio Station] Radio Clock',
		description: rs__( 'Radio Station Clock time display.', 'radio-station' ),
		icon: 'clock',
		category: 'radio-station',
		example: {},
		attributes: {
			/* --- Clock Display Options --- */
			time_format: { type: 'string', default: '' },
			day: { type: 'string', default: 'full' },
			date: { type: 'boolean', default: true },
			month: { type: 'string', default: 'full' },
			zone: { type: 'boolean', default: true },
			seconds: { type: 'boolean', default: true },
			
			/* --- Hidden Switches --- */
			block: { type: 'boolean', default: true },
			pro: { type: 'boolean', default: false }
		},

		/**
		 * Edit Block Control
		 */
		edit: (props) => {
			const atts = props.attributes;
			return (
				rs_el( Fragment, {},
					rs_el( ServerSideRender, { block: 'radio-station/clock', className: 'radio-clock-block', attributes: atts } ),
					rs_el( InspectorControls, {},
						rs_el( Panel, {},
							rs_el( PanelBody, { title: rs__( 'Clock Display Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								/* Time Display Format */
								rs_el( PanelRow, {},
									rs_el( SelectControl, {
										label: rs__( 'Time Display Format', 'radio-station' ),
										options: [
											{ label: rs__( 'Plugin Setting', 'radio-station' ), value: '' },
											{ label: rs__( '12 Hour', 'radio-station' ), value: '12' },
											{ label: rs__( '24 Hour', 'radio-station' ), value: '24' },
										],
										onChange: ( value ) => {
											props.setAttributes( { time_format: value } );
										},
										value: atts.time_format
									})
								),
								/* Day Display Format */
								rs_el( PanelRow, {},
									rs_el( SelectControl, {
										label: rs__( 'Day Display Format', 'radio-station' ),
										options: [
											{ label: rs__( 'Full', 'radio-station' ), value: 'full' },
											{ label: rs__( 'Short', 'radio-station' ), value: 'short' },
											{ label: rs__( 'None', 'radio-station' ), value: 'none' },
										],
										onChange: ( value ) => {
											props.setAttributes( { day: value } );
										},
										value: atts.day
									})
								),
								/* Date Display */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Date?', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { date: value } );
										},
										checked: atts.date,
									})
								),
								/* Month Display Format */
								rs_el( PanelRow, {},
									rs_el( SelectControl, {
										label: 'Month Display Format',
										options: [
											{ label: rs__( 'Full', 'radio-station' ), value: 'full' },
											{ label: rs__( 'Short', 'radio-station' ), value: 'short' },
											{ label: rs__( 'None', 'radio-station' ), value: 'none' },
										],
										onChange: ( value ) => {
											props.setAttributes( { month: value } );
										},
										value: atts.month
									})
								),
								/* Timezone Display */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Timezone?', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { zone: value } );
										},
										checked: atts.zone,
									})
								),
								/* Seconds Display */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Seconds?', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { seconds: value } );
										},
										checked: atts.seconds,
									})
								),
							)
							/* end panels */
						)
					)
				)
			);
		},

		/**
		 * Returns nothing because this is a dynamic block rendered via PHP
		 */
		save: () => null,
	});
})();
