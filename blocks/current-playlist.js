/**
 * === Radio Current Playlist Block ===
 */
(() => {

	const rs_el = window.wp.element.createElement;
	const rs__ = window.wp.i18n.__;
	const { serverSideRender: ServerSideRender } = window.wp;
	const { registerBlockType } = window.wp.blocks;
	const { InspectorControls } = window.wp.blockEditor;
	const { Fragment } = window.wp.element;
	const { BaseControl, TextControl, SelectControl, RadioControl, RangeControl, ToggleControl, Panel, PanelBody, PanelRow } = window.wp.components;

	registerBlockType( 'radio-station/current-playlist', {

		/* --- Block Settings --- */
		title: '[Radio Station] Current Playlist',
		description: rs__( 'Radio Station current playlist block.', 'radio-station' ),
		icon: 'playlist-audio',
		category: 'radio-station',
		example: {},
		attributes: {
			/* --- Loading Options --- */
			ajax: { type: 'string', default: '' },
			/* dynamic: { type: 'string', default: '' }, */
			hide_empty: { type: 'boolean', default: false },

			/* --- Playlist Display Options --- */
			playlist_title: { type: 'boolean', default: false },
			link: { type: 'boolean', default: true },
			countdown: { type: 'boolean', default: true },
			no_playlist: { type: 'string', default: '' },

			/* --- Track Display Options --- */
			song: { type: 'boolean', default: true },
			artist: { type: 'boolean', default: true },
			album: { type: 'boolean', default: false },
			label: { type: 'boolean', default: false },
			comments: { type: 'boolean', default: false },
			
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
					rs_el( ServerSideRender, { block: 'radio-station/current-playlist', className: 'radio-playlist-block', attributes: atts } ),
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

							/* === Playlist Display Panel === */
							rs_el( PanelBody, { title: rs__( 'Extra Display Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: false },
								/* --- Playlist Title --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Playlist Title', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { playlist_title: value } );
										},
										checked: atts.playlist_title,
									})
								),
								/* --- Link Playlist --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Link to Playlist Page', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { link: value } );
										},
										checked: atts.link,
									})
								),
								/* --- No Playlist Text --- */
								rs_el( PanelRow, {},
									rs_el( TextControl, {
										label: rs__( 'No Current Playlist Text', 'radio-station' ),
										help: rs__( 'Blank for default. 0 for none.', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { no_playlist: value } );
										},
										value: atts.no_playlist
									})
								),
								/* --- Countdown --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Playlist Countdown', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { countdown: value } );
										},
										checked: atts.countdown,
									})
								),
							),

							/* === Track Display Options === */
							rs_el( PanelBody, { title: rs__( 'Track Display Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								/* --- Song Display --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Song Title', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { song: value } );
										},
										checked: atts.song,
									})
								),
								/* --- Artist Display --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Artist', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { artist: value } );
										},
										checked: atts.artist,
									})
								),
								/* --- Display Album --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Album', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { album: value } );
										},
										checked: atts.album,
									})
								),
								/* --- Display Record Label --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Record Label', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { label: value } );
										},
										checked: atts.label,
									})
								),
								/* --- Display Comments --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Display Track Comments', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { comments: value } );
										},
										checked: atts.comments,
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
