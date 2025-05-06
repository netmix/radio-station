/**
 * === Radio Schedule Block ===
 */
(() => {

	const rs_el = window.wp.element.createElement;
	const rs__ = window.wp.i18n.__;
	const { serverSideRender: ServerSideRender } = window.wp;
	const { registerBlockType } = window.wp.blocks;
	const { InspectorControls } = window.wp.blockEditor;
	const { Fragment } = window.wp.element;
	const { BaseControl, TextControl, SelectControl, RadioControl, RangeControl, ToggleControl, Panel, PanelBody, PanelRow } = window.wp.components;

	/* --- set schedule view options --- */
	schedule_views = [
		{ label: rs__( 'Table', 'radio-station' ), value: 'table' },
		{ label: rs__( 'Tabbed', 'radio-station' ), value: 'tabs' },
		{ label: rs__( 'List', 'radio-station' ), value: 'list' },
	];
	pro_views = [
		{ label: rs__( 'Table', 'radio-station' ), value: 'table' },
		{ label: rs__( 'Tabbed', 'radio-station' ), value: 'tabs' },
		{ label: rs__( 'Grid', 'radio-station' ), value: 'grid' },
		{ label: rs__( 'Calendar', 'radio-station' ), value: 'calendar' },
		{ label: rs__( 'List', 'radio-station' ), value: 'list' },
	];
	default_setting = [ { label: rs__( 'Plugin Setting', 'radio-station' ), value: '' } ];
	default_views = default_setting.concat(pro_views);

	registerBlockType( 'radio-station/schedule', {

		/* --- Block Settings --- */
		title: '[Radio Station] Program Schedule',
		description: rs__( 'Radio Station Schedule block.', 'radio-station' ),
		icon: 'calendar-alt',
		category: 'radio-station',
		example: {},
		attributes: {
			
			/* --- Schedule Display --- */
			view: { type: 'string', default: 'table' },
			image_position: { type: 'string', default: 'left' },
			hide_past_shows: { type: 'boolean', default: false },

			/* --- Header Displays --- */
			time_header: { type: 'string', default: 'clock' },
			/* clock: { type: 'boolean', default: true }, */
			/* timezone: { type: 'boolean', default: true }, */
			selector: { type: 'boolean', default: true },

			/* --- Times Display --- */
			display_day: { type: 'string', default: 'short' },
			display_month: { type: 'string', default: 'short' },
			start_day: { type: 'string', default: '' },
			time_format: { type: 'string', default: '' },
			/* days: { type: '', default: false },*/
			/* start_date:  { type: '', default: false }, */
			/* active_date: { type: '', default: false }, */
			/* display_date: { type: 'string', default: 'jS' }, */

			/* --- Show Display --- */
			show_times: { type: 'boolean', default: true },
			show_link: { type: 'boolean', default: true },
			show_image: { type: 'boolean', default: false },
			show_desc: { type: 'boolean', default: false },
			show_hosts: { type: 'boolean', default: false },
			link_hosts: { type: 'boolean', default: false },
			show_genres: { type: 'boolean', default: false },
			show_encore: { type: 'boolean', default: true },
			// show_file: { type: 'boolean', default: false },
			
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
					rs_el( ServerSideRender, { block: 'radio-station/schedule', className: 'radio-schedule-block', attributes: atts } ),
					rs_el( InspectorControls, {},
						rs_el( Panel, {},
							/* === Schedule Display Panel === */
							rs_el( PanelBody, { title: rs__( 'Schedule Display Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								/* --- View Selection --- */
								( ( !atts.pro ) &&
									rs_el( PanelRow, {},
										rs_el( SelectControl, {
											label: rs__( 'Schedule View', 'radio-station' ),
											help: rs__( 'Grid and Calendar Views available in Pro version.', 'radio-station' ),
											options: schedule_views,
											onChange: ( value ) => {
												props.setAttributes( { view: value } );
											},
											value: atts.view
										})
									)
								),
								( ( !atts.pro ) &&
									rs_el( PanelRow, {},
										rs_el( BaseControl, {
											label: rs__( 'View Switching', 'radio-station' ),
											help: rs__( 'Multiple view switching available in Pro version.', 'radio-station' ),
										})
									)
								),
								/* --- [Pro] Multiple View Selection --- */
								/* ( ( atts.pro && atts.multi_view ) && */
								( ( atts.pro ) &&
									rs_el( PanelRow, {},
										rs_el( SelectControl, {
											multiple: true,
											label: rs__( 'Select Schedule Views', 'radio-station' ),
											help: rs__( 'Ctrl-Click to select multiple views.', 'radio-station' ),
											options: pro_views,
											onChange: ( value ) => {
												props.setAttributes( { views: value } );
											},
											value: atts.views
										})
									)
								),
								/* --- [Pro] Default View --- */
								( ( atts.pro ) &&
									rs_el( PanelRow, {},
										rs_el( SelectControl, {
											label: rs__( 'Default View', 'radio-station' ),
											options: default_views,
											onChange: ( value ) => {
												props.setAttributes( { default_view: value } );
											},
											value: atts.default_view
										})
									)
								),
								/* --- Tab View Options */
								( ( ( !atts.pro && ( atts.view == 'tabs' ) )
								|| ( atts.pro && atts.views.includes('tabs') ) ) &&
									/* --- Image Position --- */
									rs_el( PanelRow, {},
										rs_el( SelectControl, {
											label: rs__( 'Image Position', 'radio-station' ),
											help: rs__( 'Affects Tabbed View only.', 'radio-station' ),
											options: [
												{ label: rs__( 'Left', 'radio-station' ), value: 'left' },
												{ label: rs__( 'Right', 'radio-station' ), value: 'right' }
											],
											onChange: ( value ) => {
												props.setAttributes( { image_position: value } );
											},
											value: atts.image_position
										})
									)
								),
								( ( ( !atts.pro && ( atts.view == 'tabs' ) )
								|| ( atts.pro && atts.views.includes('tabs') ) ) &&
									/* --- Hide Past Shows */
									rs_el( PanelRow, {},
										rs_el( ToggleControl, {
											label: rs__( 'Hide Past Shows', 'radio-station' ),
											help: rs__( 'Affects Tabbed View only.', 'radio-station' ),
											onChange: ( value ) => {
												props.setAttributes( { hide_past_shows: value } );
											},
											checked: atts.hide_past_shows,
										})
									)
								),
								/* --- [Pro] Grid View Options --- */
								( ( atts.pro && atts.views.includes('grid') ) &&
									/* --- Grid Width --- */
									rs_el( PanelRow, {},
										rs_el( RangeControl, {
											label: rs__( 'Grid Width', 'radio-station' ),
											help: rs__( 'Grid view Show column width in pixels.', 'radio-station' ),
											min: 0,
											max: 1000,
											onChange: ( value ) => {
												props.setAttributes( { gridwith: value } );
											},
											value: atts.gridwidth
										})
									)
								),
								( ( atts.pro && atts.views.includes('grid') ) &&
									/* --- Time Spaced Grid --- */
									rs_el( PanelRow, {},
										rs_el( ToggleControl, {
											label: rs__( 'Time Spaced Grid', 'radio-station' ),
											help: rs__( 'Line up Shows by times in Grid view.', 'radio-station' ),
											onChange: ( value ) => {
												props.setAttributes( { time_spaced: value } );
											},
											checked: atts.time_spaced,
										})
									)
								),
								/* --- [Pro] Calendar View Options --- */
								( ( atts.pro && atts.views.includes('calendar') ) &&
									/* --- Calendar Weeks --- */
									rs_el( PanelRow, {},
										rs_el( RangeControl, {
											label: rs__( 'Calendar Weeks', 'radio-station' ),
											help: rs__( 'Week rows to display in view.', 'radio-station' ),
											min: 1,
											max: 8,
											onChange: ( value ) => {
												props.setAttributes( { weeks: value } );
											},
											value: atts.weeks
										})
									)
								),
								( ( atts.pro && atts.views.includes('calendar') ) &&
									/* --- Previous Weeks --- */
									rs_el( PanelRow, {},
										rs_el( RangeControl, {
											label: rs__( 'Previous Weeks', 'radio-station' ),
											help: rs__( 'Previous Weeks Display', 'radio-station' ),
											min: 0,
											max: 4,
											onChange: ( value ) => {
												props.setAttributes( { previous_weeks: value } );
											},
											value: atts.previous_weeks,
										})
									)
								)
							),

							/* === Header Displays Panel === */
							rs_el( PanelBody, { title: rs__( 'Header Display Options', 'radio-station' ), initialOpen: false },
								/* --- Clock/Timezone Header --- */
								rs_el( PanelRow, {},
									rs_el( SelectControl, {
										label: rs__( 'Radio Time Header', 'radio-station' ),
										options: [
											{ label: rs__( 'Display Radio Clock', 'radio-station' ), value: 'clock' },
											{ label: rs__( 'Display Radio Timezone', 'radio-station' ), value: 'timezone' },
											{ label: rs__( 'No Time Header Display', 'radio-station' ), value: 'none' }
										],
										onChange: ( value ) => {
											props.setAttributes( { time_header: value } );
										},
										value: atts.time_header
									})
								),
								/* --- Genre Highlighter --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Genre Highlighter', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { selector: value } );
										},
										checked: atts.selector,
									})
								),
							),

							/* === Time Display Options === */
							rs_el( PanelBody, { title: rs__( 'Time Display Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								/* --- Day Display --- */
								rs_el( PanelRow, {},
									rs_el( RadioControl, {
										label: rs__( 'Day Display Format', 'radio-station' ),
										options : [
											{ label: 'Abbreviated', value: 'short' },
											{ label: 'Full Name', value: 'full' }
										],
										onChange: ( value ) => {
											props.setAttributes( { display_day: value } );
										},
										selected: atts.display_day
									})
								),
								/* --- Month Display --- */
								rs_el( PanelRow, {},
									rs_el( RadioControl, {
										label: rs__( 'Month Display Format', 'radio-station' ),
										options: [
											{ label: rs__( 'Abbreviated', 'radio-station' ), value: 'short' },
											{ label: rs__( 'Full Name', 'radio-station' ), value: 'full' }
										],
										onChange: ( value ) => {
											props.setAttributes( { display_month: value } );
										},
										selected: atts.display_month
									})
								),
								/* --- Schedule Start Day --- */
								rs_el( PanelRow, {},
									rs_el( SelectControl, {
										label: rs__( 'Schedule Start Day', 'radio-station' ),
										options: [
											{ label: rs__( 'WP Start of Week', 'radio-station' ), value: '' },
											{ label: rs__( 'Today', 'radio-station' ), value: 'today' },
											{ label: rs__( 'Monday', 'radio-station' ), value: 'Monday' },
											{ label: rs__( 'Tuesday', 'radio-station' ), value: 'Tuesday' },
											{ label: rs__( 'Wednesday', 'radio-station' ), value: 'Wednesday' },
											{ label: rs__( 'Thursday', 'radio-station' ), value: 'Thursday' },
											{ label: rs__( 'Friday', 'radio-station' ), value: 'Friday' },
											{ label: rs__( 'Saturday', 'radio-station' ), value: 'Saturday' },
											{ label: rs__( 'Sunday', 'radio-station' ), value: 'Sunday' }							
										],
										onChange: ( value ) => {
											props.setAttributes( { start_day: value } );
										},
										value: atts.start_day
									})
								),
								/* --- Time Format --- */
								rs_el( PanelRow, {},
									rs_el( SelectControl, {
										label: rs__( 'Time Display Format', 'radio-station' ),
										options: [
											{ label: rs__( 'Plugin Setting', 'radio-station' ), value: '' },
											{ label: rs__( '12 Hour', 'radio-station' ), value: '12' },
											{ label: rs__( '24 Hour', 'radio-station' ), value: '24' }
										],
										onChange: ( value ) => {
											props.setAttributes( { time_format: value } );
										},
										value: atts.time_format
									})
								)
							),

							/* === Show Display Options === */
							rs_el( PanelBody, { title: rs__( 'Show Display Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: false },
								/* --- Show Times --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Show Time', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_times: value } );
										},
										checked: atts.show_times,
									})
								),
								/* --- Show Link --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Link to Shows', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_link: value } );
										},
										checked: atts.show_link,
									})
								),
								/* --- Show Image --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Show Image', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_image: value } );
										},
										checked: atts.show_image,
									})
								),
								/* --- Show Description --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Show Description', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_desc: value } );
										},
										checked: atts.show_desc,
									})
								),
								/* --- Show Hosts --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Show Hosts', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_hosts: value } );
										},
										checked: atts.show_hosts,
									})
								),
								/* --- Link Hosts --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Link to Hosts', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { link_hosts: value } );
										},
										checked: atts.link_hosts,
									})
								),
								/* --- Show Genres --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Show Genres', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_genres: value } );
										},
										checked: atts.show_genres,
									})
								),
								/* --- Show Encore --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Encore', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_encore: value } );
										},
										checked: atts.show_encore,
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
