/**
 * === Radio Player Block ===
 */
(() => {

	/* --- Import Modules/Components --- */
	const rs_el = window.wp.element.createElement;
	const rs__ = window.wp.i18n.__;
	const { serverSideRender: ServerSideRender } = window.wp;
	const { registerBlockType, getBlockType } = window.wp.blocks;
	const { InspectorControls, useBlockProps } = window.wp.blockEditor;
	const { Fragment, useEffect, createRef, useRef } = window.wp.element;
	const { BaseControl, TextControl, SelectControl, RadioControl, RangeControl, ToggleControl, ColorPicker, Dropdown, Button, Panel, PanelBody, PanelRow } = window.wp.components;
	
	/* --- Register Block --- */
	if ( !getBlockType('radio-station/player' ) ) {
	 registerBlockType( 'radio-station/player', {

		/* --- Block Settings --- */
		title: rs__( '[Radio Station] Stream Player', 'radio-station' ),
		description: rs__( 'Audio stream player block.', 'radio-station' ),
		icon: 'controls-volumeon',
		category: 'radio-station',
		example: {},
		attributes: {
			/* --- Player Content --- */
			url: { type: 'string', default: '' },
			title: { type: 'string', default: '' },
			image: { type: 'string', default: 'default' },
			/* --- Player Options --- */
			script: { type: 'string', default: 'default' },
			volume: { type: 'number', default: 77 },
			volumes: { type: 'array', default: ['slider'] },
			default: { type: 'boolean', default: false },
			/* --- Player Styles --- */
			layout: { type: 'string', default: 'horizontal' },
			theme: { type: 'string', default: 'default' },
			buttons: { type: 'string', default: 'default' },
			/* --- Hidden Switches --- */
			block: { type: 'boolean', default: true },
			pro: { type: 'boolean', default: false }
		},

		/**
		 * Edit Block Control
		 */
		edit: (props) => {
			const atts = props.attributes;
			let blockRef = createRef(null);
			const blockProps = useBlockProps({ ref: blockRef });

			/* load control colors on render */
			useEffect(() => {
				/* console.log('Player block rendered.', atts, blockProps); */
				radio_player_control_colors(blockProps.id, atts);
			}, [atts.text_color, atts.background_color, atts.playing_color, atts.button_color, atts.track_color, atts.thumb_color]);

			return (
				rs_el( Fragment, {},
					rs_el( ServerSideRender, { block: 'radio-station/player', className: 'radio-player-block', attributes: atts } ),
					rs_el( InspectorControls, {},
						rs_el( Panel, {},
							/* === Player Content === */
							rs_el( PanelBody, { title: rs__( 'Player Content', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								/* --- Stream URL --- */
								rs_el( PanelRow, {},
									rs_el( TextControl, {
										label: rs__( 'Stream URL', 'radio-station' ),
										help: rs__( 'Leave blank to use default stream.', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { url: value } );
										},
										value: atts.url,
									})
								),
								/* --- Player Title Text --- */
								rs_el( PanelRow, {},
									rs_el( TextControl, {
										label: rs__( 'Player Title Text', 'radio-station' ),
										help: rs__( 'Empty for default, 0 for none.', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { title: value } );
										},
										value: atts.title
									})
								),
								/* --- Image --- */
								rs_el( PanelRow, {},
									rs_el( SelectControl, {
										label: rs__( 'Player Image', 'radio-station' ),
										options : [
											{ label: rs__( 'Plugin Setting', 'radio-station' ), value: 'default' },
											{ label: rs__( 'Display Station Image', 'radio-station' ), value: '1' },
											{ label: rs__( 'Do Not Display Station Image', 'radio-station' ), value: '0' },
											/* { label: rs__( 'Display Custom Image', 'radio-station' ), value: 'custom' }, */
										],
										onChange: ( value ) => {
											props.setAttributes( { image: value } );
										},
										value: atts.image
									})
								)
							),

							/* === Player Options === */
							rs_el( PanelBody, { title: rs__( 'Player Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								/* --- Script --- */
								rs_el( PanelRow, {},
									rs_el( SelectControl, {
										label: rs__( 'Player Script', 'radio-station' ),
										options : [
											{ label: rs__( 'Plugin Setting', 'radio-station' ), value: 'default' },
											{ label: rs__( 'Amplitude', 'radio-station' ), value: 'amplitude' },
											{ label: rs__( 'Howler', 'radio-station' ), value: 'howler' },
											{ label: rs__( 'jPlayer', 'radio-station' ), value: 'jplayer' },
										],
										onChange: ( value ) => {
											props.setAttributes( { script: value } );
										},
										value: atts.script
									})
								),
								/* --- Volume --- */
								rs_el( PanelRow, {},
									rs_el( RangeControl, {
										label: rs__( 'Initial Volume', 'radio-station' ),
										min: 0,
										max: 100,
										onChange: ( value ) => {
											props.setAttributes( { volume: value } );
										},
										value: atts.volume
									})
								),
								/* --- Volume controls --- */
								rs_el( PanelRow, {},
									rs_el( SelectControl, {
										multiple: true,
										label: rs__( 'Volume Controls', 'radio-station' ),
										help: rs__( 'Ctrl-Click to select multiple controls.', 'radio-station' ),
										options: [
											{ label: rs__( 'Volume Slider', 'radio-station' ), value: 'slider' },
											{ label: rs__( 'Up and Down Buttons', 'radio-station' ), value: 'updown' },
											{ label: rs__( 'Mute Button', 'radio-station' ), value: 'mute' },
											{ label: rs__( 'Maximize Button', 'radio-station' ), value: 'max' },
										],
										onChange: ( value ) => {
											props.setAttributes( { volumes: value } );
										},
										value: atts.volumes
									})
								),
								/* --- Default Player --- */
								rs_el( PanelRow, {},
									rs_el( ToggleControl, {
										label: rs__( 'Use as Default Player', 'radio-station' ),
										help: rs__( 'Make this the default player on this page.', 'radio-station' ),
										onChange: ( value ) => {
											props.setAttributes( { default: value } );
										},
										checked: atts.default,
									})
								),
								/* --- Popup Player Button --- */
								rs_el( PanelRow, {},
									( ( atts.pro ) && 
										rs_el( SelectControl, {
											label: rs__( 'Popup Player', 'radio-station' ),
											help: rs__( 'Enables button to open Player in separate window.', 'radio-station' ),
											options : [
												{ label: rs__( 'Plugin Setting', 'radio-station' ), value: 'default' },
												{ label: rs__( 'On', 'radio-station' ), value: 'on' },
												{ label: rs__( 'Off', 'radio-station' ), value: 'off' },
											],
											onChange: ( value ) => {
												props.setAttributes( { popup: value } );
											},
											value: atts.popup
										})
									), ( ( !atts.pro ) &&
										rs_el( BaseControl, {
											label: rs__( 'Popup Player', 'radio-station' ),
											help: rs__( 'Popup Player Button available in Pro.', 'radio-station' ),
										})
									)
								),
							),

							/* === Player Styles === */
							rs_el( PanelBody, { title: rs__( 'Player Design', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								/* --- Player Layout --- */
								rs_el( PanelRow, {},
									rs_el( RadioControl, {
										label: rs__( 'Player Layout', 'radio-station' ),
										options : [
											{ label: rs__( 'Vertical (Stacked)', 'radio-station' ), value: 'vertical' },
											{ label: rs__( 'Horizontal (Inline)', 'radio-station' ), value: 'horizontal' },
										],
										onChange: ( value ) => {
											props.setAttributes( { layout: value } );
										},
										checked: atts.layout
									})
								),
								/* --- Player Theme --- */
								( ( !atts.pro ) &&
									rs_el( PanelRow, {},
										rs_el( SelectControl, {
											label: rs__( 'Player Theme', 'radio-station' ),
											options : [
												{ label: rs__( 'Plugin Setting', 'radio-station' ), value: 'default' },
												{ label: rs__( 'Light', 'radio-station' ), value: 'light' },
												{ label: rs__( 'Dark', 'radio-station' ), value: 'dark' },
											],
											onChange: ( value ) => {
												props.setAttributes( { theme: value } );
											},
											value: atts.theme
										})
									)
								),
								/* [Pro] Extra Theme Color Options */
								( ( atts.pro ) &&
									rs_el( PanelRow, {},
										rs_el( SelectControl, {
											label: rs__( 'Player Theme', 'radio-station' ),
											options : [
												{ label: rs__( 'Plugin Setting', 'radio-station' ), value: 'default' },
												{ label: rs__( 'Light', 'radio-station' ), value: 'light' },
												{ label: rs__( 'Dark', 'radio-station' ), value: 'dark' },
												{ label: rs__( 'Red', 'radio-station' ), value: 'red' },
												{ label: rs__( 'Orange', 'radio-station' ), value: 'orange' },
												{ label: rs__( 'Yellow', 'radio-station' ), value: 'yellow' },
												{ label: rs__( 'Light Green', 'radio-station' ), value: 'light-green' },
												{ label: rs__( 'Green', 'radio-station' ), value: 'green' },
												{ label: rs__( 'Cyan', 'radio-station' ), value: 'cyan' },
												{ label: rs__( 'Light Blue', 'radio-station' ), value: 'light-blue' },
												{ label: rs__( 'Blue', 'radio-station' ), value: 'blue' },
												{ label: rs__( 'Purple', 'radio-station' ), value: 'purple' },
												{ label: rs__( 'Magenta', 'radio-station' ), value: 'magenta' },
											],
											onChange: ( value ) => {
												props.setAttributes( { theme: value } );
											},
											value: atts.theme
										})
									)
								),
								/* --- Player Buttons --- */
								rs_el( PanelRow, {},
									rs_el( SelectControl, {
										label: rs__( 'Player Buttons', 'radio-station' ),
										options : [
											{ label: rs__( 'Plugin Setting', 'radio-station' ), value: 'default' },
											{ label: rs__( 'Circular', 'radio-station' ), value: 'circular' },
											{ label: rs__( 'Rounded', 'radio-station' ), value: 'rounded' },
											{ label: rs__( 'Square', 'radio-station' ), value: 'square' },
										],
										onChange: ( value ) => {
											props.setAttributes( { buttons: value } );
										},
										value: atts.buttons
									})
								)					
							),
							
							/* === [Pro] Player Colors === */
							( ( atts.pro ) &&
								rs_el( PanelBody, { title: rs__( 'Player Colors', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
								
									/* --- Text Color --- */
									rs_el( PanelRow, {},
										rs_el( BaseControl, {
											label: rs__( 'Text Color', 'radio-station' ),
											className: 'color-dropdown-control'
										},
											rs_el( Dropdown, {
												renderContent: () => (
													rs_el( ColorPicker, {
														disableAlpha: true,
														defaultValue: '',
														onChangeComplete: color => {
															props.setAttributes( {text_color: color.hex} );
														},
														color: atts.text_color
													})
												),
												renderToggle: (args) => (
													rs_el( 'div', {className: 'color-dropdown-buttons'},
														rs_el( Button, {
															className: 'color-dropdown-text_color',
															onClick: args.onToggle,
															variant: 'secondary',
															'aria-expanded': args.isOpen,
															'aria-haspopup': 'true',
															'aria-label': rs__( 'Select Text Color', 'radio-station' )
														},
														( ('' != atts.text_color) ? atts.text_color : rs__( 'Select', 'radio-station' ) )
														),
														rs_el( Button, {
															onClick: () => {
																props.setAttributes( {text_color: ''} );
																args.onClose();
															},
															isSmall: true,
															variant: 'tertiary',
															'aria-label': rs__( 'Clear Text Color Selection', 'radio-station' )
														},
														rs__( 'Clear', 'radio-station' )
														),
														( ( '' != atts.text_color ) &&
															rs_el( 'style', {}, '.components-button.is-secondary.color-dropdown-text_color {background-color:'+atts.text_color+'}' )
														)
													)
												)
											} ) 
										)
									),

									/* --- Background Color --- */
									rs_el( PanelRow, {},
										rs_el( BaseControl, {
											label: rs__( 'Background Color', 'radio-station' ),
											className: 'color-dropdown-control'
										},
											rs_el( Dropdown, {
												renderContent: () => (
													rs_el( ColorPicker, {
														defaultValue: '',
														onChangeComplete: color => {
															props.setAttributes( {background_color: color.hex} );
														},
														color: atts.background_color
													})
												),
												renderToggle: (args) => (
													rs_el( 'div', {className: 'color-dropdown-buttons'},
														rs_el( Button, {
															className: 'color-dropdown-background_color',
															onClick: args.onToggle,
															variant: 'secondary',
															'aria-expanded': args.isOpen,
															'aria-haspopup': 'true',
															'aria-label': rs__( 'Select Background Color', 'radio-station' )
														},
														( ('' != atts.background_color) ? atts.background_color : rs__( 'Select', 'radio-station' ) )
														),
														rs_el( Button, {
															onClick: () => {
																props.setAttributes( {background_color: ''} );
																args.onClose();
															},
															isSmall: true,
															variant: 'tertiary',
															'aria-label': rs__( 'Clear Background Color Selection', 'radio-station' )
														},
														rs__( 'Clear', 'radio-station' )
														),
														( ( '' != atts.background_color ) &&
															rs_el( 'style', {}, '.components-button.is-secondary.color-dropdown-background_color {background-color:'+atts.background_color+'}' )
														)
													)
												)
											} ) 
										)
									),
									
									/* --- Playing Color --- */
									rs_el( PanelRow, {},
										rs_el( BaseControl, {
											label: rs__( 'Playing Highlight', 'radio-station' ),
											className: 'color-dropdown-control'
										},
											rs_el( Dropdown, {
												renderContent: () => (
													rs_el( ColorPicker, {
														defaultValue: '',
														onChangeComplete: color => {
															props.setAttributes( {playing_color: color.hex} );
														},
														color: atts.playing_color
													})
												),
												renderToggle: (args) => (
													rs_el( 'div', {className: 'color-dropdown-buttons'},
														rs_el( Button, {
															className: 'color-dropdown-playing_color',
															onClick: args.onToggle,
															variant: 'secondary',
															'aria-expanded': args.isOpen,
															'aria-haspopup': 'true',
															'aria-label': rs__( 'Select Playing Highlight Color', 'radio-station' )
														},
														( ('' != atts.playing_color) ? atts.playing_color : rs__( 'Select', 'radio-station' ) )
														),
														rs_el( Button, {
															onClick: () => {
																props.setAttributes( {playing_color: ''} );
																args.onClose();
															},
															isSmall: true,
															variant: 'tertiary',
															'aria-label': rs__( 'Clear Playing Color Selection', 'radio-station' )
														},
														rs__( 'Clear', 'radio-station' )
														),
														( ( '' != atts.playing_color ) &&
															rs_el( 'style', {}, '.components-button.is-secondary.color-dropdown-playing_color {background-color:'+atts.playing_color+'}' )
														)
													)
												)
											} ) 
										)
									),
									
									/* --- Buttons Color --- */
									rs_el( PanelRow, {},
										rs_el( BaseControl, {
											label: rs__( 'Buttons Highlight', 'radio-station' ),
											className: 'color-dropdown-control'
										},
											rs_el( Dropdown, {
												renderContent: () => (
													rs_el( ColorPicker, {
														defaultValue: '',
														onChangeComplete: color => {
															props.setAttributes( {buttons_color: color.hex} );
														},
														color: atts.buttons_color
													})
												),
												renderToggle: (args) => (
													rs_el( 'div', {className: 'color-dropdown-buttons'},
														rs_el( Button, {
															className: 'color-dropdown-buttons_color',
															onClick: args.onToggle,
															variant: 'secondary',
															'aria-expanded': args.isOpen,
															'aria-haspopup': 'true',
															'aria-label': rs__( 'Select Button Highlight Color', 'radio-station' )
														},
														( ('' != atts.buttons_color) ? atts.buttons_color : rs__( 'Select', 'radio-station' ) )
														),
														rs_el( Button, {
															onClick: () => {
																props.setAttributes( {buttons_color: ''} );
																args.onClose();
															},
															isSmall: true,
															variant: 'tertiary',
															'aria-label': rs__( 'Clear Button Highlight Color Selection', 'radio-station' )
														},
														rs__( 'Clear', 'radio-station' )
														),
														( ( '' != atts.buttons_color ) &&
															rs_el( 'style', {}, '.components-button.is-secondary.color-dropdown-buttons_color {background-color:'+atts.buttons_color+'}' )
														)
													)
												)
											} ) 
										)
									),
									
									/* --- Track Color --- */
									rs_el( PanelRow, {},
										rs_el( BaseControl, {
											label: rs__( 'Volume Track', 'radio-station' ),
											className: 'color-dropdown-control'
										},
											rs_el( Dropdown, {
												renderContent: () => (
													rs_el( ColorPicker, {
														defaultValue: '',
														onChangeComplete: color => {
															props.setAttributes( {track_color: color.hex} );
														},
														color: atts.track_color
													})
												),
												renderToggle: (args) => (
													rs_el( 'div', {className: 'color-dropdown-buttons'},
														rs_el( Button, {
															className: 'color-dropdown-track_color',
															onClick: args.onToggle,
															variant: 'secondary',
															'aria-expanded': args.isOpen,
															'aria-haspopup': 'true',
															'aria-label': rs__( 'Select Volume Track Color', 'radio-station' )
														},
														( ('' != atts.track_color) ? atts.track_color : rs__( 'Select', 'radio-station' ) )
														),
														rs_el( Button, {
															onClick: () => {
																props.setAttributes( {track_color: ''} );
																args.onClose();
															},
															isSmall: true,
															variant: 'tertiary',
															'aria-label': rs__( 'Clear Volume Track Color Selection', 'radio-station' )
														},
														rs__( 'Clear', 'radio-station' )
														),
														( ( '' != atts.track_color ) &&
															rs_el( 'style', {}, '.components-button.is-secondary.color-dropdown-track_color {background-color:'+atts.track_color+'}' )
														)
													)
												)
											} ) 
										)
									),
									
									/* --- Thumb Color --- */
									rs_el( PanelRow, {},
										rs_el( BaseControl, {
											label: rs__( 'Volume Thumb', 'radio-station' ),
											className: 'color-dropdown-control'
										},
											rs_el( Dropdown, {
												renderContent: () => (
													rs_el( ColorPicker, {
														defaultValue: '',
														onChangeComplete: color => {
															props.setAttributes( {thumb_color: color.hex} );
														},
														color: atts.thumb_color
													})
												),
												renderToggle: (args) => (
													rs_el( 'div', {className: 'color-dropdown-buttons'},
														rs_el( Button, {
															className: 'color-dropdown-thumb_color',
															onClick: args.onToggle,
															variant: 'secondary',
															'aria-expanded': args.isOpen,
															'aria-haspopup': 'true',
															'aria-label': rs__( 'Select Volume Thumb Color', 'radio-station' )
														},
														( ('' != atts.thumb_color) ? atts.thumb_color : rs__( 'Select', 'radio-station' ) )
														),
														rs_el( Button, {
															onClick: () => {
																props.setAttributes( {thumb_color: ''} );
																args.onClose();
															},
															isSmall: true,
															variant: 'tertiary',
															'aria-label': rs__( 'Clear Volume Thumb Color Selection', 'radio-station' )
														},
														rs__( 'Clear', 'radio-station' )
														),
														( ( '' != atts.thumb_color ) &&
															rs_el( 'style', {}, '.components-button.is-secondary.color-dropdown-thumb_color {background-color:'+atts.thumb_color+'}' )
														)
													)
												)
											} ) 
										)
									),
									/* --- end color options --- */
								)
							),

							/* === Advanced Options === */
							( ( atts.pro ) &&
								rs_el( PanelBody, { title: rs__( 'Advanced Options', 'radio-station' ), className: 'radio-block-controls', initialOpen: true },
									/* --- Current Show Display --- */
									rs_el( PanelRow, {},
										rs_el( SelectControl, {
											label: rs__( 'Current Show Display', 'radio-station' ),
											options : [
												{ label: rs__( 'Plugin Setting', 'radio-station' ), value: 'default' },
												{ label: rs__( 'On', 'radio-station' ), value: 'on' },
												{ label: rs__( 'Off', 'radio-station' ), value: 'off' },
											],
											onChange: ( value ) => {
												props.setAttributes( { currentshow: value } );
											},
											value: atts.currentshow
										})
									),
									/* ---Now Playing Display --- */
									rs_el( PanelRow, {},
										rs_el( SelectControl, {
											label: rs__( 'Now Playing Track Display', 'radio-station' ),
											options : [
												{ label: rs__( 'Plugin Setting', 'radio-station' ), value: 'default' },
												{ label: rs__( 'On', 'radio-station' ), value: 'on' },
												{ label: rs__( 'Off', 'radio-station' ), value: 'off' },
											],
											onChange: ( value ) => {
												props.setAttributes( { nowplaying: value } );
											},
											value: atts.nowplaying
										})
									),
									/* --- Track Animation --- */
									rs_el( PanelRow, {},
										rs_el( SelectControl, {
											label: rs__( 'Track Animation', 'radio-station' ),
											options : [
												{ label: rs__( 'Plugin Setting', 'radio-station' ), value: 'default' },
												{ label: rs__( 'No Animation', 'radio-station' ), value: 'none' },
												{ label: rs__( 'Left to Right Ticker', 'radio-station' ), value: 'lefttoright' },
												{ label: rs__( 'Right to Left Ticker', 'radio-station' ), value: 'righttoleft' },
												{ label: rs__( 'Back and Forth', 'radio-station' ), value: 'backandforth' },
												{ label: rs__( '', 'radio-station' ), value: 'off' },
											],
											onChange: ( value ) => {
												props.setAttributes( { animation: value } );
											},
											value: atts.animation
										})
									),
									/* --- Metadata URL --- */
									rs_el( PanelRow, {},
										rs_el( TextControl, {
											label: rs__( 'Metadata Source URL', 'radio-station' ),
											help: rs__( 'Defaults to Stream URL.', 'radio-station' ),
											onChange: ( value ) => {
												props.setAttributes( { metadata: value } );
											},
											value: atts.metadata
										})
									),
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
	}
})();

/* Load Player Controls Colors */
function radio_player_control_colors(id, atts) {
	container = jQuery('#'+id+' .radio-container'); /* console.log(container); */
	if (!container.length) { setTimeout(function() {radio_player_control_colors(id,atts);}, 1000); return; }
	instance = container.attr('id').replace('radio_container_','');
	url = radio_player.settings.ajaxurl+'?action=player_control_styles&instance='+instance+'&text='+encodeURIComponent(atts.text_color)+'&background='+encodeURIComponent(atts.background_color)+'&playing='+encodeURIComponent(atts.playing_color)+'&buttons='+encodeURIComponent(atts.buttons_color)+'&track='+encodeURIComponent(atts.track_color)+'&thumb='+encodeURIComponent(atts.thumb_color);
	jQuery.ajax({
		type: 'GET',
		url: url,
		data: {'action':'player_control_styles', 'instance':instance, 'text':atts.text_color, 'background':atts.background_color, 'playing':atts.playing_color, 'buttons':atts.buttons_color, 'track':atts.track_color, 'thumb':atts.thumb_color},
		processData: false,
		beforeSend: function(request, settings) {
			request._data = settings.data; 
		},
		success: function(data, success, request) {
			if (data.success) {
				console.log('Load Control Styles Success: '+data.message);
				if (jQuery('#radio-player-control-styles-'+data.instance).length) {jQuery('#radio-player-control-styles-'+data.instance).remove();}
				jQuery('body').append('<style id="radio-player-control-styles-'+data.instance+'">'+data.css+'</style>');
			} else {console.log('Load Control Styles Failed: '+data.message); console.log(request);}
		},
		fail: function(request, textStatus, errorThrown) {
			console.log(request); console.log(textStatus); console.log(errorThrown);
		}
	}).catch(function(error) {
		console.log(error); console.log(jQuery(this));
	});
}

