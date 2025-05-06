
/* === Radio Station Block Editor Scripts === */

const { Icon } = wp.components;

/* --- Subscribe to Block State --- */
/* ref: https://wordpress.stackexchange.com/a/358256/76440 */
( () => {
    let blocksState = wp.data.select( 'core/block-editor' ).getBlocks();
    wp.data.subscribe( _.debounce( ()=> {
        newBlocksState = wp.data.select( 'core/block-editor' ).getBlocks();
        if ( blocksState.length !== newBlocksState.length ) {

            /* --- recheck for needed scripts --- */
			schedule = player = archive = clock = false;
			s_multi = s_table = s_tabs = s_list = s_grid = s_calendar = false;
			for ( i = 0; i < newBlocksState.length; i++ ) {
				block = newBlocksState[i];
				if ( block.name == 'radio-station/clock' ) {
					clock = true;
				} else if ( block.name == 'radio-station/schedule' ) {

					if ( block.attributes.clock ) {clock = true;}

					/* --- Schedule Views --- */
					schedule = true;
					if ( !block.attributes.pro ) {
						if ( block.attributes.view == 'table' ) {s_table = true;}
						else if ( block.attributes.view == 'tabs' ) {s_tabs = true;}
						else if ( block.attributes.view == 'list' ) {s_list = true;}
						else if ( block.attributes.view == 'grid' ) {s_grid = true;}
						else if ( block.attributes.view == 'calendar' ) {s_calendar = true;}
					}
					
					/* --- [Pro] Multiple Views --- */
					if ( block.attributes.pro ) {
						s_multi = true;
						if ( block.attributes.views.includes( 'table' ) ) {s_table = true;}
						if ( block.attributes.views.includes( 'tabs' ) ) {s_tabs = true;}
						if ( block.attributes.views.includes( 'grid' ) ) {s_grid = true;}
						if ( block.attributes.views.includes( 'calendar' ) ) {s_calendar = true;}
						if ( block.attributes.views.includes( 'list' ) ) {s_list = true;}
					}

				} else if ( block.name == 'radio-station/player' ) {
					player = true;
				} else if ( block.name == 'radio-station/archive' ) {
					archive = true;
					if ( block.attributes.pagination ) {
						/* TODO: check pagination type */
					}
				}					
			}
			if (clock && !jQuery('#radio-clock-js').length) {radio_station_load_block_script('clock');}
			if (schedule) {
				/* --- schedule view scripts --- */
				if (s_multi && !jQuery('#radio-schedule-multiview-js').length) {
					radio_station_load_block_script('schedule-multiview');
				}
				if (s_table && !jQuery('#radio-schedule-table-js').length) {
					radio_station_load_block_script('schedule-table');
					var radio_load_table = setInterval(function() { if (typeof radio_table_initialize == 'function') {
						radio_table_initialize(); clearInterval(radio_load_table);
					} }, 1000);
				}
				if (s_tabs && !jQuery('#radio-schedule-tabs-js').length) {
					radio_station_load_block_script('schedule-tabs');
					var radio_load_tabs = setInterval(function() { if (typeof radio_tabs_initialize == 'function') {
						radio_tabs_init = false; radio_tabs_initialize(); clearInterval(radio_load_tabs);
					} }, 1000);
				}
				if (s_list && !jQuery('#radio-schedule-list-js').length) {
					radio_station_load_block_script('schedule-list');
					var radio_load_list = setInterval(function() { if (typeof radio_list_hightlight == 'function') {
						radio_list_hightlight(); clearInterval(radio_load_list);
					} }, 1000);
				}
				if (s_grid && !jQuery('#radio-schedule-grid-js').length) {
					radio_station_load_block_script('schedule-grid');
					var radio_load_grid = setInterval(function() { if (typeof radio_grid_initialize == 'function') {
						radio_grid_init = false; radio_grid_initialize(); radio_grid_time_spacing(); clearInterval(radio_load_grid);
					} }, 1000);
				}
				if (s_calendar && !jQuery('#radio-schedule-calendar-js').length) {
					radio_station_load_block_script('schedule-calendar');
					var radio_load_calendar = setInterval(function() { if (typeof radio_calendar_initialize == 'function') {
						radio_calendar_init = false; radio_calendar_initialize(); radio_sliders_check(); clearInterval(radio_load_calendar);
					} }, 1000);
				}
			}
			if (player) {
				radio_station_load_block_script('player');
				/* TODO: maybe initialize player ?
				var radio_load_player = setInterval(function() { if (typeof ??? == 'function') {
					radio_player_init = false; ???(); clearInterval(radio_load_player);
				} }, 1000); */
			}
			if (archive) {
				/* TODO: check for archive pagination type */
			}
        }
        blocksState = newBlocksState;
    }, 300 ) );
} )();

/* --- Load Block Script --- */
function radio_station_load_block_script(handle) {
	id = 'radio-'+handle+'-js';
	if (!document.getElementById(id)) {
		url = radio.ajax_url+'?action=radio_station_block_script&handle='+handle;
		jQuery('html head').append('<script id="'+id+'" src="'+url+'"></script>');
	}
	if (typeof radio_station_pro_block_scripts == 'function') {
		radio_station_pro_load_block_scripts(handle);
	}
}

