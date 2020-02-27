/* ------------------------------ */
/* Radio Station Show Page Script */
/* ------------------------------ */

/* Show/Hide Audio Player */
function radio_show_player() {
    if (typeof jQuery == 'function') {jQuery('#show-player').fadeIn(1000);}
    else {document.getElementById('show-player').style.display = 'block';}
}

/* Switch Section Tabs */
function radio_show_tab(tab) {
    if (typeof jQuery == 'function') {
        jQuery('.show-tab').removeClass('tab-active').addClass('tab-inactive');
        jQuery('#show-'+tab+'-tab').removeClass('tab-inactive').addClass('tab-active');
        jQuery('#show-'+tab).removeClass('tab-inactive').addClass('tab-active');
    } else {
        tabs = document.getElementsByClassName('show-tab');
        for (i = 0; i < tabs.length; i++) {
            tabs[i].className = tabs[i].className.replace('-tab-active', '-tab-inactive');
        }
        button = document.getElementById('show-'+tab+'-tab');
        button.className = button.className.replace('-tab-inactive', '-tab-active');
        content = document.getElementById('show-'+tab);
        content.className = content.className.replace('-tab-inactive', '-tab-active');
    }
}

/* Responsive Page */
function radio_show_responsive() {

    /* Check to Add Narrow Class */
    if (typeof jQuery == 'function') {
        showcontent = jQuery('#show-content');
        if (showcontent.width() < 500) {showcontent.addClass('narrow');}
        else {showcontent.removeClass('narrow');}

    } else {
        showcontent = document.getElementById('show-content');
        if (showcontent.offsetWidth < 500) {showcontent.classList.add('narrow');}
        else {showcontent.classList.remove('narrow');}
    }

    /* Maybe Display Show More Button */
    descstate = document.getElementById('show-desc-state');
    if ( descstate && (descstate.value != 'expanded') ) {
        showdesc = document.getElementsByClassName('show-description')[0];
        if (showdesc.offsetHeight < showdesc.scrollHeight) {
            document.getElementById('show-more-overlay').style.display = 'block';
            document.getElementById('show-desc-buttons').style.display = 'block';
            showdesc.style.paddingBottom = '0';
        } else {
            document.getElementById('show-more-overlay').style.display = 'none';
            document.getElementById('show-desc-buttons').style.display = 'none';
            showdesc.style.paddingBottom = '30px';
        }
    }
}

/* Description Show More/Less */
function radio_show_desc(moreless) {
    if (moreless == 'more') {
        if (typeof jQuery == 'function') {jQuery('#show-description').addClass('expanded');}
        else {document.getElementById('show-description').classList.add('expanded');}
        document.getElementById('show-more-overlay').style.display = 'none';
        document.getElementById('show-desc-more').style.display = 'none';
        document.getElementById('show-desc-less').style.display = 'inline-block';
    }
    if (moreless == 'less') {
        if (typeof jQuery == 'function') {jQuery('.show-description').removeClass('expanded');}
        else {document.getElementById('show-description').classList.remove('expanded');}
        document.getElementById('show-more-overlay').style.display = 'block';
        document.getElementById('show-desc-less').style.display = 'none';
        document.getElementById('show-desc-more').style.display = '';
        radio_scroll_to('show-section-about');
    }
}

/* Section Scroll Link */
function radio_scroll_link(id) {
    if (typeof jQuery == 'function') {
        section = jQuery('#show-section-'+id);
        scrolltop = section.offset().top - section.height() - 40;
        jQuery('html, body').animate({ 'scrollTop': scrolltop }, 1000);
    } else {
        radio_scroll_to('show-section-'+id);
    }
}

/* Responsive Load and Resizing */
if (typeof jQuery == 'function') {
    jQuery(document).ready(function() {radio_show_responsive();} );
    jQuery(window).resize(function () {
        radio_resize_debounce(radio_show_responsive, 500, 'showpage');
    });
} else {
    if (window.addEventListener) {
        document.body[addEventListener]('load', radio_show_responsive, false);
        document.body[addEventListener]('resize', radio_show_responsive, false);
    } else {
        document.body[attachEvent]('onload', radio_show_responsive, false);
        document.body[attachEvent]('onresize', radio_show_responsive, false);
    }
}
