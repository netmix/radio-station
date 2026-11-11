/* ------------------------- */
/* Radio Station Page Script */
/* ------------------------- */

/* Get Page Type */
function radio_page_type() {
	if (!document.getElementById('radio-page-type')) {return 'show';}
	return document.getElementById('radio-page-type').value;
}

/* Show/Hide Audio Player */
function radio_show_player() {
	prefix = radio_page_type();
    if (typeof jQuery == 'function') {jQuery('#'+prefix+'-player').fadeIn(1000);}
    else {document.getElementById(prefix+'-player').style.display = 'block';}
}

/* Switch Section Tabs */
function radio_show_tab(prefix,tab) {
	/* prefix = radio_page_type(); */
	if ( (typeof jQuery == 'function') && jQuery('#'+prefix+'-'+tab+'-tab') ) {
		jQuery('.'+prefix+'-tab').removeClass('tab-active').addClass('tab-inactive');
		jQuery('.'+prefix+'-section').removeClass('tab-active').addClass('tab-inactive');
		jQuery('#'+prefix+'-'+tab+'-tab').removeClass('tab-inactive').addClass('tab-active');
		jQuery('#'+prefix+'-'+tab).removeClass('tab-inactive').addClass('tab-active');
	} else if (document.getElementById(prefix+'-'+tab+'-tab')) {
		tabs = document.getElementsByClassName(prefix+'-tab');
		for (i = 0; i < tabs.length; i++) {
			tabs[i].className = tabs[i].className.replace('-tab-active', '-tab-inactive');
		}
		sections = document.getElementsByClassName(prefix+'-section');
		for (i = 0; i < sections.length; i++) {
			sections[i].className = sections[i].className.replace('-tab-active', '-tab-inactive');
		}
		button = document.getElementById(prefix+'-'+tab+'-tab');
		button.className = button.className.replace('-tab-inactive', '-tab-active');
		content = document.getElementById(prefix+'-'+tab);
		content.className = content.className.replace('-tab-inactive', '-tab-active');
	}
	/* 2.7.3: bring the selected tab fully into view if the tab row scrolls */
	radio_tab_arrows_reveal(prefix, tab);
}

/* Responsive Page */
function radio_page_responsive() {
	prefix = radio_page_type();
	if (!document.getElementById(prefix+'-content')) {return;}

    /* Check to Add Narrow Class */
    if (typeof jQuery == 'function') {
        content = jQuery('#'+prefix+'-content');
        if (content.width() < 500) {content.addClass('narrow');}
        else {content.removeClass('narrow');}
    } else {
        content = document.getElementById(prefix+'-content');
        if (content.offsetWidth < 500) {content.classList.add('narrow');}
        else {content.classList.remove('narrow');}
    }

    /* Maybe Match Heights for Info and Description */
	if (document.getElementById(prefix+'-content').className.indexOf('top-blocks') < 0) {
		info = document.getElementById(prefix+'-info');
		desc = document.getElementById(prefix+'-description');
		about = document.getElementById(prefix+'-section-about');
		if (info && desc) {
			descheight = info.offsetHeight - 30;
			if (about) {descheight = descheight - about.offsetHeight;}
			if (descheight > 30) {
				if (descheight < desc.style.minHeight) {desc.style.maxHeight = '';}
				else {desc.style.maxHeight = descheight+'px';}
			} else {desc.style.maxHeight = '';}
		}
	}

    /* Maybe Display Show More Button */
    descstate = document.getElementById('show-desc-state');
    if ( descstate && (descstate.value != 'expanded') ) {
        desc = document.getElementsByClassName(prefix+'-description')[0];
        if (desc.offsetHeight < desc.scrollHeight) {
            document.getElementById('show-more-overlay').style.display = 'block';
            document.getElementById('show-desc-buttons').style.display = 'block';
            desc.style.paddingBottom = '0';
        } else {
            document.getElementById('show-more-overlay').style.display = 'none';
            document.getElementById('show-desc-buttons').style.display = 'none';
            desc.style.paddingBottom = '30px';
        }
    }

    /* 2.7.3: refresh tab row arrows after the narrow class may have changed */
    radio_tab_arrows_update_all();
}

/* Description Show More/Less */
function radio_show_desc(moreless) {
	prefix = radio_page_type();
    if (moreless == 'more') {
        if (typeof jQuery == 'function') {jQuery('#'+prefix+'-description').addClass('expanded');}
        else {document.getElementById(prefix+'-description').classList.add('expanded');}
        document.getElementById('show-more-overlay').style.display = 'none';
        document.getElementById('show-desc-more').style.display = 'none';
        document.getElementById('show-desc-less').style.display = 'inline-block';
    }
    if (moreless == 'less') {
        if (typeof jQuery == 'function') {jQuery('#'+prefix+'-description').removeClass('expanded');}
        else {document.getElementById(prefix+'-description').classList.remove('expanded');}
        document.getElementById('show-more-overlay').style.display = 'block';
        document.getElementById('show-desc-less').style.display = 'none';
        document.getElementById('show-desc-more').style.display = '';
        radio_scroll_to(prefix+'-section-about');
    }
}

/* Section Scroll Link */
function radio_scroll_link(id) {
	prefix = radio_page_type();
    if (typeof jQuery == 'function') {
        section = jQuery('#'+prefix+'-section-'+id);
        scrolltop = section.offset().top - section.height() - 40;
        jQuery('html, body').animate({ 'scrollTop': scrolltop }, 1000);
    } else {
        radio_scroll_to(prefix+'-section-'+id);
    }
}

/* Responsive Load and Resizing */
if (typeof jQuery == 'function') {
    jQuery(document).ready(function() {
		prefix = radio_page_type();
		radio_page_responsive();
		jQuery(window).resize(function () {
			radio_resize_debounce(radio_page_responsive, 500, prefix+'page');
		});
	} );
} else {
    if (window.addEventListener) {
        document.body[addEventListener]('load', radio_page_responsive, false);
        document.body[addEventListener]('resize', radio_page_responsive, false);
    } else {
        document.body[attachEvent]('onload', radio_page_responsive, false);
        document.body[attachEvent]('onresize', radio_page_responsive, false);
    }
}

/* --- Tab Row Overflow Arrows --- */
/* 2.7.3: when the show tabs do not fit on one row (phones), the row scrolls
   sideways; these arrows show that more tabs exist and step to them, and each
   one hides once the first or last tab is reached (#107) */
function radio_tab_arrows_init() {
	var rows = document.querySelectorAll('.show-tabs');
	for (var i = 0; i < rows.length; i++) {
		var row = rows[i];
		if (row.parentNode.classList.contains('show-tabs-wrap')) {continue;}
		var wrap = document.createElement('div');
		wrap.className = 'show-tabs-wrap';
		row.parentNode.insertBefore(wrap, row);
		wrap.appendChild(row);
		var arrows = {'prev': '\u2039', 'next': '\u203A'};
		for (var dir in arrows) {
			var arrow = document.createElement('button');
			arrow.type = 'button';
			arrow.className = 'show-tabs-arrow ' + dir;
			arrow.setAttribute('aria-label', ('prev' == dir) ? 'Previous tabs' : 'More tabs');
			arrow.textContent = arrows[dir];
			arrow.addEventListener('click', radio_tab_arrows_click(row, ('prev' == dir) ? -1 : 1));
			wrap.appendChild(arrow);
		}
		row.addEventListener('scroll', radio_tab_arrows_listener(row), {passive: true});
		radio_tab_arrows_update(row);
	}
}
function radio_tab_arrows_click(row, dir) {
	return function() {radio_tab_arrows_step(row, dir);};
}
function radio_tab_arrows_listener(row) {
	return function() {radio_tab_arrows_update(row);};
}
function radio_tab_arrows_update(row) {
	var wrap = row.parentNode;
	if (!wrap || !wrap.classList.contains('show-tabs-wrap')) {return;}
	var overflow = (row.scrollWidth > (row.clientWidth + 1));
	wrap.classList.toggle('has-prev', overflow && (row.scrollLeft > 1));
	wrap.classList.toggle('has-next', overflow && ((row.scrollLeft + row.clientWidth) < (row.scrollWidth - 1)));
}
function radio_tab_arrows_update_all() {
	var rows = document.querySelectorAll('.show-tabs-wrap > .show-tabs');
	for (var i = 0; i < rows.length; i++) {radio_tab_arrows_update(rows[i]);}
}
function radio_tab_arrows_scroll(row, left) {
	left = Math.max(0, Math.min(left, row.scrollWidth - row.clientWidth));
	if (row.scrollTo) {row.scrollTo({left: left, behavior: 'smooth'});} else {row.scrollLeft = left;}
}
function radio_tab_arrows_step(row, dir) {
	/* room taken by an arrow, so a revealed tab is not hidden underneath it */
	var room = 32;
	var tabs = row.querySelectorAll('.show-tab');
	var left = row.scrollLeft, right = left + row.clientWidth;
	var i, tab;
	if (dir > 0) {
		for (i = 0; i < tabs.length; i++) {
			tab = tabs[i];
			if ((tab.offsetLeft + tab.offsetWidth) > (right - room + 1)) {
				radio_tab_arrows_scroll(row, tab.offsetLeft + tab.offsetWidth - row.clientWidth + room);
				return;
			}
		}
		radio_tab_arrows_scroll(row, row.scrollWidth);
	} else {
		for (i = tabs.length - 1; i >= 0; i--) {
			tab = tabs[i];
			if (tab.offsetLeft < (left + room - 1)) {
				radio_tab_arrows_scroll(row, tab.offsetLeft - room);
				return;
			}
		}
		radio_tab_arrows_scroll(row, 0);
	}
}
function radio_tab_arrows_reveal(prefix, tab) {
	var button = document.getElementById(prefix + '-' + tab + '-tab');
	if (!button) {return;}
	var row = button.parentNode;
	if (!row || (row.scrollWidth <= row.clientWidth)) {return;}
	var room = 32;
	if (button.offsetLeft < (row.scrollLeft + room)) {
		radio_tab_arrows_scroll(row, button.offsetLeft - room);
	} else if ((button.offsetLeft + button.offsetWidth) > (row.scrollLeft + row.clientWidth - room)) {
		radio_tab_arrows_scroll(row, button.offsetLeft + button.offsetWidth - row.clientWidth + room);
	}
}
if (document.readyState != 'loading') {radio_tab_arrows_init();}
else {document.addEventListener('DOMContentLoaded', radio_tab_arrows_init);}
