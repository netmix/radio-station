/**
 * === Radio Archive Block ===
 */
(() => {

	const rs_el = window.wp.element.createElement;
	const rs__ = window.wp.i18n.__;
	const { serverSideRender: ServerSideRender } = window.wp;
	const { registerBlockType } = window.wp.blocks;
	const { InspectorControls } = window.wp.blockEditor;
	const { Fragment } = window.wp.element;
	const { BaseControl, TextControl, SelectControl, RadioControl, RangeControl, ToggleControl, Panel, PanelBody, PanelRow } = window.wp.components;

	archive_options = [
		{ label: rs__( 'Shows', 'radio-station' ), value: 'shows' },
		{ label: rs__( 'Overrides', 'radio-station' ), value: 'overrides' },
		{ label: rs__( 'Playlists', 'radio-station' ), value: 'playlists' },
		{ label: rs__( 'Shows by Genre', 'radio-station' ), value: 'genres' },
		{ label: rs__( 'Shows by Language', 'radio-station' ), value: 'languages' },
	];
	pro_archive_options = archive_options;
	pro_archive_options[5] = { label: rs__( 'Episodes', 'radio-station' ), value: 'episodes' };
	pro_archive_options[6] = { label: rs__( 'Hosts', 'radio-station' ), value: 'hosts' };
	pro_archive_options[7] = { label: rs__( 'Producers', 'radio-station' ), value: 'producers' };
	/* pro_archive_options[8] = { label: rs__( 'Team', 'radio-station' ), value: 'team' }; */
			
	registerBlockType( 'radio-station/archive', {

		/* --- Block Settings --- */
		title: '[Radio Station] Archive List',
		description: rs__( 'Archive list for Radio Station record types.', 'radio-station' ),
		icon: 'media-audio',
		category: 'radio-station',
		example: {},
		attributes: {
			/* --- Archive List Details --- */
			archive_type: { type: 'string', default: 'shows' },
			view: { type: 'string', default: 'list' },
			perpage: { type: 'number', default: 10 },
			pagination: { type: 'boolean', default: true },
			hide_empty: { type: 'boolean', default: false },

			/* --- Archive Record Query --- */
			orderby: { type: 'string', default: 'title' },
			order: { type: 'string', default: 'ASC' },
			status: { type: 'string', default: 'publish' },
			genre: { type: 'string', default: '' },
			language: { type: 'string', default: '' },

			/* === Archive Record Display === */
			description: { type: 'string', default: 'excerpt' },
			time_format: { type: 'string', default: '' },
			show_avatars: { type: 'boolean', default: true }, /* shows and overrides only */
			with_shifts: { type: 'boolean', default: true }, /* shows only */
			show_dates: { type: 'boolean', default: true },	/* overrides only */
			
			/* --- Hidden Switches --- */
			block: { type: 'boolean', default: true },
			pro: { type: 'boolean', default: false }
		},

		/**
		 * Edit Block Controls
		 */
		edit: (props) => {
			const atts = props.attributes;
			if ( atts.pro ) {
				archive_type_options = pro_archive_options;
				archive_type_help = rs__( 'Which type of records to display.', 'radio-station' );
			} else {
				archive_type_options = archive_options;
				archive_type_help = rs__( 'Episodes, Hosts and Producer archives available in Pro version.', 'radio-station' );
			}

			return (
				rs_el( Fragment, {},
					rs_el( ServerSideRender, { block: 'radio-station/archive', className: 'radio-archive-block', attributes: atts } ),
					rs_el( InspectorControls, {},
						rs_el( Panel, {},
							/* === Archive List Details === */
							rs_el( PanelBody, { title: rs__( 'Archive List Details', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								/* --- Archive Type --- */
								rs_el( PanelRow, {},
									rs_el( SelectControl, {
										label: rs__( 'Archive Type', 'radio-station' ),
										help: archive_type_help,
										options: archive_type_options,
										onChange: ( value ) => {
											props.setAttributes( { archive_type: value } );
										},
										value: atts.archive_type,
									})
								),
								/* --- Archive View --- */
								rs_el( PanelRow, {},
									rs_el( SelectControl, {
										label: rs__( 'Archive View', 'radio-station' ),
										options : [
											{ label: rs__( 'List View', 'radio-station' ), value: 'list' },
											{ label: rs__( 'Grid View', 'radio-station' ), value: 'grid' },
										],
										onChange: ( value ) => {
											props.setAttributes( { view: value } );
										},
										value: atts.view
									})
								),
								/* --- Per Page --- */
								rs_el( PanelRow, {},
									rs_el( RangeControl, {
										label: rs__( 'Records Per Page', 'radio-station' ),
										help: rs__( 'Use 0 for all records.', 'radio-station' ),
										min: 0,
										max: 100,
										onChange: ( value ) => {
											props.setAttributes( { perpage: value } );
										},
										value: atts.perpage
									})
								),
								/* --- Pagination --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Pagination?', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { pagination: value } );
										},
										checked: atts.pagination,
									})
								),
								/* --- Hide if Empty --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Hide if Empty?', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { hide_empty: value } );
										},
										checked: atts.hide_empty,
									})
								),
							),

							/* === Archive Record Query === */
							rs_el( PanelBody, { title: rs__( 'Archive Record Query', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								rs_el( SelectControl, {
									label: rs__( 'Order By', 'radio-station' ),
									options: [
										{ label: rs__( 'Title', 'radio-station' ), value: 'title' },
										{ label: rs__( 'Publish Date', 'radio-station' ), value: 'date' },
										{ label: rs__( 'Modified Date', 'radio-station' ), value: 'modified' },
									],
									onChange: ( value ) => {
										props.setAttributes( { orderby: value } );
									},
									value: atts.orderby
								}),
								rs_el( RadioControl, {
									label: rs__( 'Order', 'radio-station' ),
									options: [
										{ label: rs__( 'Ascending', 'radio-station' ), value: 'ASC' },
										{ label: rs__( 'Descending', 'radio-station' ), value: 'DESC' },
									],
									onChange: ( value ) => {
										props.setAttributes( { order: value } );
									},
									selected: atts.order
								}),
								/* TODO: --- Status Picker ? --- */						
								/* TODO: --- Genre Picker ? --- */
								/* TODO: --- Language Picker ? --- */
							),

							/* === Archive Record Display === */
							rs_el( PanelBody, { title: rs__( 'Archive Record Display', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								/* --- Time Format --- */
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
								/* --- Description --- */
								rs_el( PanelRow, {},
									rs_el( SelectControl, {
										label: rs__( 'Description Display Format', 'radio-station' ),
										options: [
											{ label: rs__( 'View Default', 'radio-station' ), value: '' },
											{ label: rs__( 'None', 'radio-station' ), value: 'none' },
											{ label: rs__( 'Excerpt', 'radio-station' ), value: 'excerpt' },
											{ label: rs__( 'Full', 'radio-station' ), value: 'full' },
										],
										onChange: ( value ) => {
											props.setAttributes( { description: value } );
										},
										value: atts.description
									})
								),
								/* --- Image Display (conditional) --- */
								( ( atts.archive_type == 'shows' || atts.archive_type == 'overrides' ) &&
									rs_el( PanelRow, { className: 'shows-only overrides-only' },
										rs_el( ToggleControl, {
											label: rs__( 'Display Image?', 'radio-station' ),
											help: rs__( 'This setting is for Shows and Overrides.', 'radio-station' ),
											onChange: ( value ) => {
												props.setAttributes( { show_avatars: value } );
											},
											checked: atts.show_avatars
										})
									)
								),
								/* --- With Shifts Only (conditional) --- */
								( ( atts.archive_type == 'shows' ) &&
									rs_el( PanelRow, { className: 'shows-only' },
										rs_el( ToggleControl, {
											label: rs__( 'Only Shows with Shifts?', 'radio-station' ),
											help: rs__( 'This setting is for Shows only.', 'radio-station' ),
											onChange: ( value ) => {
												props.setAttributes( { with_shifts: value } );
											},
											checked: atts.with_shifts
										})
									)
								),
								/* --- Override Dates (conditional) --- */
								( ( atts.archive_type == 'overrides' ) &&
									rs_el( PanelRow, { className: 'overrides-only' },
										rs_el( ToggleControl, {
											label: rs__( 'Display Override Dates?', 'radio-station' ),
											help: rs__( 'This setting is for Overrides only.', 'radio-station' ),
											onChange: ( value ) => {
												props.setAttributes( { show_dates: value } );
											},
											checked: atts.show_dates
										})
									)
								)
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
