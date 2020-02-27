/* ------------------------------ */
/* Radio Station Countdown Script */
/* ------------------------------ */

/* Countdown Loop */
function radio_countdown() {

    if (radio.debug) {console.log( 'Current Time:'+(new Data()).toISOString() );}
    radio.current_time = Math.floor( (new Date()).getTime() / 1000 );

    /* Current Show Countdown */
    jQuery('.current-show-end').each(function() {
        diff = jQuery(this).val() - radio.current_time + radio.user_offset;
        if (radio.debug) {console.log('Current Show Ends in: '+diff);}
        if (diff < 1) {countdown = radio.label_showended; jQuery(this).removeClass('current-show-end');}
        else {countdown = radio_countdown_display(diff, radio.label_timeremaining);}
        jQuery(this).parent().find('.rs-countdown').html(countdown);
    });

    /* Upcoming Show Countdown */
    jQuery('.upcoming-show-times').each(function() {
        times = jQuery(this).val().split('-');
        if (radio.debug) {
            newdate = new Date( (times[0] + radio.user_offset) * 1000 );
            console.log('Next Show Start: '+newdate.getTime()+' -- '+newdate.toISOString());
            newdate = new Date( (times[1] + radio.user_offset) * 1000 );
            console.log('Next Show End: '+newdate.getTime()+' -- '+newdate.toISOString());
        }
        diffa = times[0] - radio.current_time + radio.user_offset;
        diffb = times[1] - radio.current_time + radio.user_offset;
        if (radio.debug) {console.log('Next Show Start in: '+diffa+' - Next Show End in:'+diffb);}
        if (diffa < 1) {
            if (diffb < 1) {countdown = radio.label_showended; jQuery(this).removeClass('upcoming-show-times');}
            else {countdown = radio.label_showstarted;}
        } else {countdown = radio_countdown_display(diffa, radio.label_timecommencing);}
        jQuery(this).parent().find('.rs-countdown').html(countdown);
    });

    /* Current Playlist Countdown */
    jQuery('.current-playlist-end').each(function() {
        diff = jQuery(this).val() - radio.current_time + radio.user_offset;
        if (radio.debug) {console.log('Current Playlist Ends in: '+diff);}
        if (diff < 1) {countdown = playlistended; jQuery(this).removeClass('current-playlist-end');}
        else {countdown = radio_countdown_display(diff, radio.label_timeremaining);}
        jQuery(this).parent().find('.rs-countdown').html(countdown);
    });
    if ( jQuery('.current-show-end') || jQuery('.upcoming-show-times') || jQuery('.current-playlist-end') ) {
        setTimeout('radio_countdown();', 1000);
    }
}

/* Get Countdown Display */
function radio_countdown_display(diff, label) {
    countdown = new Date(diff * 1000).toISOString();
    hours = countdown.substr(11, 2);
    if (hours.substr(0,1) == '0') {hours = hours.substr(1,1);}
    minutes = countdown.substr(14, 2);
    seconds = countdown.substr(17, 2);
    display = '<span class="rs-label">'+label+'</span>: <span class="rs-hours">'+hours+'</span><span "rs-separator">:</span>';
    display += '<span class="rs-minutes">'+minutes+'</span><span class="rs-separator">:</span><span class="rs-seconds">'+seconds+'</span>';
    return display;
}

/* Start Countdown on Pageload */
jQuery(document).ready(function() {
    radio_countdown();
});