/**
 * === Radio Current Show Block ===
 */
(() => {

	const rs_el = window.wp.element.createElement;
	const rs__ = window.wp.i18n.__;
	const { serverSideRender: ServerSideRender } = window.wp;
	const { registerBlockType } = window.wp.blocks;
	const { InspectorControls } = window.wp.blockEditor;
	const { Fragment } = window.wp.element;
	const { BaseControl, TextControl, SelectControl, RadioControl, RangeControl, ToggleControl, Panel, PanelBody, PanelRow } = window.wp.components;

	/* --- create image size options --- */
	image_size_options = [];
	image_sizes = wp.data.select( 'core/block-editor' ).getSettings().imageSizes;
	for ( i = 0; i < image_sizes.length; i++ ) {
		image_size_options[i] = { label: image_sizes[i].name, value: image_sizes[i].slug };
	}

	registerBlockType( 'radio-station/current-show', {

		/* --- Block Settings --- */
		title: '[Radio Station] Current Show',
		description: rs__( 'Radio Station current show block.', 'radio-station' ),
		icon: 'controls-play',
		category: 'radio-station',
		example: {},
		attributes: {
			/* --- Loading Options --- */
			ajax: { type: 'string', default: '' },
			/* dynamic: { type: 'string', default: '' }, */
			no_shows: { type: 'string', default: '' },
			hide_empty: { type: 'boolean', default: false },

			/* --- Show Display Options --- */
			show_link: { type: 'boolean', default: true },
			title_position: { type: 'string', default: 'right' },
			show_avatar: { type: 'boolean', default: true },
			avatar_size: { type: 'string', default: 'thumbnail' },
			avatar_width: { type: 'number', default: 0 },

			/* --- Show Time Display Options --- */
			show_sched: { type: 'boolean', default: true },
			show_all_sched: { type: 'boolean', default: false },
			countdown: { type: 'boolean', default: true },
			time_format: { type: 'string', default: '' },

			/* --- Extra Display Options --- */
			display_hosts: { type: 'boolean', default: false },
			link_hosts: { type: 'boolean', default: true },
			/* display_producers: { type: 'boolean', default: false }, */
			/* link_producers: { type: 'boolean', default: false }, */
			show_desc: { type: 'boolean', default: false },
			show_playlist: { type: 'boolean', default: false },
			show_encore: { type: 'boolean', default: true },
			
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
					rs_el( ServerSideRender, { block: 'radio-station/current-show', className: 'radio-current-block', attributes: atts } ),
					rs_el( InspectorControls, {},
						rs_el( Panel, {},
							
							// === Loading Options === */
							rs_el( PanelBody, { title: rs__( 'Show Display Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								rs_el( PanelRow, {},
									rs_el( SelectControl, {
										label: rs__( 'AJAX Load Block', 'radio-station' ),
										help: rs__( 'To bypass page caching.', 'radio-station' ),
										options : [
											{ label: rs__( 'Plugin Setting', 'radio-station' ), value: '' },
											{ label: rs__( 'On', 'radio-station' ), value: 'on' },
											{ label: rs__( 'Off', 'radio-station' ), value: 'off' },
										],
										onChange: ( value ) => {
											props.setAttributes( { ajax: value } );
										},
										value: atts.ajax
									})
								),
								/* --- [Pro] Dynamic Reloading --- */
								rs_el( PanelRow, {},
									( ( atts.pro ) && 
										rs_el( SelectControl, {
											label: rs__( 'Dynamic Reloading', 'radio-station' ),
											help: rs__( 'Reloads at show changeover times.', 'radio-station' ),
											options : [
												{ label: rs__( 'Plugin Setting', 'radio-station' ), value: '' },
												{ label: rs__( 'On', 'radio-station' ), value: 'on' },
												{ label: rs__( 'Off', 'radio-station' ), value: 'off' },
											],
											onChange: ( value ) => {
												props.setAttributes( { dynamic: value } );
											},
											value: atts.dynamic
										})
									), ( ( !atts.pro ) &&
										rs_el( BaseControl, {
											label: rs__( 'Dynamic Reloading', 'radio-station' ),
											help: rs__( 'Show changeover reloading available in Pro.', 'radio-station' ),
										})
									)
								),
								/* --- No Shows Text --- */
								rs_el( PanelRow, {},
									rs_el( TextControl, {
										label: rs__( 'No Current Show Text', 'radio-station' ),
										help: rs__( 'Blank for default. 0 for none.', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { no_shows: value } );
										},
										value: atts.no_shows
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

							/* === Show Display Options === */
							rs_el( PanelBody, { title: rs__( 'Show Display Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								/* --- Show Link --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Link to Show Page', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_link: value } );
										},
										checked: atts.show_link,
									})
								),
								/* --- Title Position --- */
								rs_el( PanelRow, {},
									rs_el( SelectControl, {
										label: rs__( 'Title Position', 'radio-station' ),
										options : [
											{ label: rs__( 'Above Image', 'radio-station' ), value: 'above' },
											{ label: rs__( 'Left of Image', 'radio-station' ), value: 'left' },
											{ label: rs__( 'Right of Image', 'radio-station' ), value: 'right' },
											{ label: rs__( 'Below Image', 'radio-station' ), value: 'below' },
										],
										onChange: ( value ) => {
											props.setAttributes( { title_position: value } );
										},
										value: atts.title_position
									})
								),
								/* --- Show Avatar --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Show Image', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_avatar: value } );
										},
										checked: atts.show_avatar,
									})
								),
								/* --- Avatar Size --- */
								rs_el( PanelRow, {},
									rs_el( SelectControl, {
										label: rs__( 'Image Size', 'radio-station' ),
										options: image_size_options,
										onChange: ( value ) => {
											props.setAttributes( { avatar_size: value } );
										},
										selected: atts.avatar_size
									})
								),
								/* --- Avatar Width --- */
								rs_el( PanelRow, {},
									rs_el( RangeControl, {
										label: rs__( 'Image Width Override', 'radio-station' ),
										help: rs__( '0 for default.', 'radio-station' ),
										min: 0,
										max: 1000,
										onChange: ( value ) => {
											props.setAttributes( { avatar_width: value } );
										},
										value: atts.avatar_width
									})
								),
							),

							/* === Show Time Display Options === */
							rs_el( PanelBody, { title: rs__( 'Show Time Display Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: false },
								/* --- Show Time --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Show Time', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_sched: value } );
										},
										checked: atts.show_sched,
									})
								),
								/* --- Show Schedule --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display All Show Times', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_all_sched: value } );
										},
										checked: atts.show_all_sched,
									})
								),
								/* --- Countdown --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Remaining Time Countdown', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { countdown: value } );
										},
										checked: atts.countdown,
									})
								),
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
							),				
							
							/* === Extra Displays Panel === */
							rs_el( PanelBody, { title: rs__( 'Extra Display Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: false },
								/* --- Display Hosts --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Show Hosts', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { display_hosts: value } );
										},
										checked: atts.display_hosts,
									})
								),
								/* --- Link Hosts --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Link to Host Profile', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { link_hosts: value } );
										},
										checked: atts.link_hosts,
									})
								),
								/* --- Display Producers --- */
								/* rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Show Producers', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { display_hosts: value } );
										},
										checked: atts.display_hosts,
									})
								), */
								/* --- Link Producers --- */
								/* rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Link to Producer Profile', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { link_producers: value } );
										},
										checked: atts.link_producers,
									})
								), */
								/* --- Show Description Excerpt --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Show Description Excerpt', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_desc: value } );
										},
										checked: atts.show_desc,
									})
								),
								/* --- Show Playlist --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Show Playlist', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { show_playlist: value } );
										},
										checked: atts.show_playlist,
									})
								),
								/* --- Show Encore --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display if Encore Airing', 'radio-station' ),
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
