/* -------------------- */
/* Radio Station Script */
/* -------------------- */

/* Shortened Helpers */
function radio_el(id) {return document.getElementById(id);}
function radio_els(classname) {return document.getElementsByClassName(classname);}

/* Smooth Scrolling */
function radio_scroll_to(id) {
	elem = document.getElementById(id);
	var jump = parseInt((elem.getBoundingClientRect().top - 50) * .2);
	document.body.scrollTop += jump;
	document.documentElement.scrollTop += jump;
	if (!elem.lastjump || elem.lastjump > Math.abs(jump)) {
		elem.lastjump = Math.abs(jump);
		setTimeout(function() { radio_scroll_to(id);}, 100);
	} else {elem.lastjump = null;}
}

/* Get Day of Week */
function radio_get_weekday(d) {
	if (d == '0') {day = 'sunday';}
	else if (d == '1') {day = 'monday';}
	else if (d == '2') {day = 'tuesday';}
	else if (d == '3') {day = 'wednesday';}
	else if (d == '4') {day = 'thursday';}
	else if (d == '5') {day = 'friday';}
	else if (d == '6') {day = 'saturday';}
	return day;
}

/* Cookie Value Function */
/* since @2.3.2 */
radio_cookie = {
	set: function (name, value, days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
			var expires = '; expires=' + date.toUTCString();
		} else {var expires = '';}
		document.cookie = 'radio_' + name + '=' + JSON.stringify(value) + expires + '; path=/';
	},
	get : function(name) {
		var nameeq = 'radio_' + name + '=', ca = document.cookie.split(';');
		for(var i=0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1,c.length);
				if (c.indexOf(nameeq) == 0) {
					/* 2.5.0: fix for possible empty value */
					value = c.substring(nameeq.length, c.length).trim();
					if ((value == '') || (value == 'undefined') || (typeof value == 'undefined')) {return null;}
					return JSON.parse(value);
				}
			}
		}
		return null;
	},
	delete : function(name) {
		document.cookie = 'radio_' + name +  '=; expires=-1; path=/';
	}
}

/* Debounce Delay Callback */
var radio_resize_debounce = (function () {
	var debounce_timers = {};
	return function (callback, ms, uniqueId) {
		if (!uniqueId) {uniqueId = "nonuniqueid";}
		if (debounce_timers[uniqueId]) {clearTimeout (debounce_timers[uniqueId]);}
		debounce_timers[uniqueId] = setTimeout(callback, ms);
	};
})();

/* User Timezone Display */
function radio_timezone_display() {
	if (typeof radio_display_override == 'function') {
		override = radio_display_override();
		if (override) {return;}
	}
	/* 2.5.18: display for both clock and timezone shortcodes */
	if (jQuery('.radio-user-zone').length || jQuery('.radio-user-timezone').length) {
		userdatetime = new Date();
		useroffset = -(userdatetime.getTimezoneOffset());
		/* if ((useroffset * 60) == radio.timezone.offset) {return;} */
		if (typeof jstz == 'function') {tz = jstz.determine().name();}
		else {tz = Intl.DateTimeFormat().resolvedOptions().timeZone;}
		if (tz.indexOf('/') > -1) {
			tz = tz.replace('_',' '); parts = tz.split('/');
			region = parts[0]; loc = parts[1];
			code = radio_timezone_code(tz); /* console.log(code); */
			/* console.log(radio.timezone.zonename_override); */
			if ((typeof radio.timezone.zonename_override != 'undefined') && radio.timezone.zonename_override) {code = radio.timezone.zonename_override;}
			if (jQuery('.radio-user-zone').length) {
				jQuery('.radio-user-zone-title').css('display','inline-block');
				jQuery('.radio-user-zone').each(function() {
					format = jQuery(this).attr('data-format');
					formatted = radio_timezone_format(format,code,region,loc,useroffset);
					jQuery(this).html(formatted).css('display','inline-block');					
				});
			}			
			if (jQuery('.radio-user-timezone').length) {
				jQuery('.radio-user-timezone-title').css('display','inline-block');
				jQuery('.radio-user-timezone').each(function() {
					format = jQuery(this).attr('data-format');
					formatted = radio_timezone_format(format,code,region,loc,useroffset);
					jQuery(this).html(formatted).css('display','inline-block');
				});
				
			}
		}
	}
}

/* Format Timezone Display */
function radio_timezone_format(format,code,region,loc,offset) {
	houroffset = parseInt(offset);
	if (houroffset == 0) {userzone = ' [UTC]';}
	else {
		houroffset = houroffset / 60;
		if (houroffset > 0) {userzone = ' [UTC+'+houroffset+']';}
		else {userzone = ' [UTC'+houroffset+']';}
	}
	f = format.split('-'); formatted = '';
	if ((f[0] == '1') && (code != '')) {formatted += code+' ';}
	if ((f[1] == '1') || (f[2] == '1')) {
		if ((f[0] == '1') && (code != '')) {formatted += '(';}
		if (f[2] == '1') {formatted += loc;}
		if ((f[1] == '1') && (f[2] == '1')) {formatted += ', ';}	
		if (f[1] == '1') {formatted += region;}
		if ((f[0] == '1') && (code != '')) {formatted += ')';}
	}
	if (f[3] == '1') {formatted += ' '+userzone;}
	return formatted;
}

/* Get Timezone Code */
function radio_timezone_code(timezone) {
	datetime = new Date();
	if ((typeof moment == 'function') && (typeof moment.tz == 'function')) {
		zonetimedate = moment(datetime.toISOString()).tz(timezone);
		code = zonetimedate.format('z');
	} else {
		locale = 'en-US';
		formatter = new Intl.DateTimeFormat(locale, {timeZone: timezone, timeZoneName: 'short'});
		timezoneparts = formatter.formatToParts(datetime).find(part => part.type === 'timeZoneName');
		if (timezoneparts) {code = timezoneparts.value;} else {code = '';}
		if (code.indexOf('GMT') > -1) {code = '';}
	}
	return code;
}

/* Retrigger Responsive Schedules */
function radio_responsive_schedules() {
	if (jQuery('.master-program-schedule').length) {radio_table_responsive(false,false);}
	if (jQuery('.master-schedule-tabs').length) {radio_tabs_responsive(false,false);}
	if (jQuery('.master-schedule-grid').length) {radio_grid_responsive(false);}
	if (jQuery('.master-schedule-calendar').length) {radio_calendar_responsive(false);}
}

/* Update Time Displays */
function radio_convert_times() {
	/* schedule: .show-time; widgets: .current-shift, .upcoming-show-schedule; show page: .show-shift-time, .override-time, subscribe: .show-list-time */
	jQuery('.show-time, .current-shift, .upcoming-show-shift, .show-shift-time, .override-time, .show-list-time').each(function() {
		/* console.log(jQuery(this)); */
		if (radio.convert_show_times || jQuery(this).hasClass('show-list-time')) {
			start = jQuery(this).find('.rs-start-time');
			end = jQuery(this).find('.rs-end-time');
			starthtml = start.html(); starttime = start.attr('data'); startformat = start.attr('data-format');
			endhtml = end.html(); endtime = end.attr('data'); endformat = end.attr('data-format');
			startdisplay = radio_user_time(starttime, startformat);
			enddisplay = radio_user_time(endtime, endformat);
			if (jQuery(this).find('.rs-day').length) {
				dayhtml = jQuery(this).find('.rs-day').html();
				dayformat = jQuery(this).find('.rs-day').attr('data-format');
				userday = radio_user_time(starttime, dayformat);
			}
			if ((starthtml != startdisplay) || (endhtml != enddisplay)) {
				if (radio.debug) {console.log('Start: '+starthtml+' => '+startdisplay+' - End: '+endhtml+' => '+enddisplay);}
				showusertime = jQuery(this).parent().find('.show-user-time').show();
				showusertime.find('.rs-start-time').html(startdisplay);
				showusertime.find('.rs-end-time').html(enddisplay);
				if (showusertime.find('.rs-day').length) {
					if (dayhtml != userday) {daydisplay = userday;} else {daydisplay = '';}
					if (radio.debug) {console.log('Day: '+dayhtml+' => '+daydisplay);}
					showusertime.find('.rs-day').html(daydisplay);
				}
			} else {jQuery(this).parent().find('.show-user-time').hide();}
			if (jQuery(this).find('.rs-start-date').length) {
				date = jQuery(this).find('.rs-start-date');
				datehtml = date.html(); datetime = date.attr('data'); dateformat = date.attr('data-format');
				datedisplay = radio_user_time(datetime, dateformat);
				if (datehtml == datedisplay) {datedisplay = '';}
				if (radio.debug) {console.log('Date: '+datehtml+' => '+datedisplay);}
				showusertime = jQuery(this).parent().find('.show-user-time');
				showusertime.find('.rs-start-date').html(datedisplay);
			}
		}
	});
	if (radio.convert_show_times && jQuery('.show-time').length) {radio_responsive_schedules();}
}

/* Convert To Time Display */
function radio_user_time(time, format) {
	if (typeof radio_user_override == 'function') {
		override = radio_user_override(time, format);
		if (override) {return override;}
	}
	datetime = new Date(time * 1000);
	zonetime = moment(datetime.toISOString());
	formatted = radio_convert_time(zonetime, format);
	return formatted;
}

/* Convert Time to Formatted Time */
function radio_convert_time(zonetime, format) {
	formatted = '';
	for (var i = 0; i < format.length; i++) {
	    k = format.charAt(i);
	    v = radio_format_key(zonetime, k);
	    formatted += v;
	}
	return formatted;
}

/* Format Time Key */
function radio_format_key(zonetime, key) {
	v = key;
	for (i in radio.moment_map) {
		if (i == key) {
			k = radio.moment_map[i];
			v = zonetime.format(k);
			if (((i == 'd') || (i == 'm') || (i == 'h') || (i == 'H') || (i == 'i') || (i == 's')) && (v < 10)) {v = '0'+v;}
			else if (i == 'D') {v = radio.labels.sdays[v];}
			else if (i == 'l') {v = radio.labels.days[v];}
			else if (i == 'N') {v++;}
			else if (i == 'S') {d = zonetime.format('D'); v.replace(d, '');}
			else if (i == 'F') {v = radio.labels.months[v];}
			else if (i == 'M') {v = radio.labels.smonths[v];}
			else if ((i == 'a') || (i == 'A')) {
				if (v.substr(0,1) == 'a') {v = radio.units.am;}
				if (v.substr(0,1) == 'p') {v = radio.units.pm;}
				if (i == 'A') {v = v.toUpperCase();}
			}
		}
	}
	return v;
}

/* Pageload Function */
if (typeof jQuery == 'function') {
	jQuery(document).ready(function() {
		radio_timezone_display();
		radio_convert_times();
	});
}
