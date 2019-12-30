/* --------------------- */
/* Radio Station ScriptS */
/* --------------------- */

/* Scrolling Function */
function radio_scroll_to(elem) {
	var jump = parseInt((elem.getBoundingClientRect().top - 50) * .2);
	document.body.scrollTop += jump;
	document.documentElement.scrollTop += jump;
	if (!elem.lastjump || elem.lastjump > Math.abs(jump)) {
		elem.lastjump = Math.abs(jump);
		setTimeout(function() { radio_scroll_to(elem);}, 100);
	} else {elem.lastjump = null;}
}

/* Convert Date Time to Time String */
function radio_time_string(datetime) {

	h = datetime.getHours();
	m = datetime.getMinutes();
	s = datetime.getSeconds();
	if (m < 10) {m = '0'+m;}
	if (s < 10) {s = '0'+s;}

	if (radio.clock_format == '12') {
		if ( h < 12 ) {mer = radio.am;}
		if ( h == 0 ) {h = '12';}	
		if ( h > 11 ) {mer = radio.pm;}
		if ( h > 12 ) {h = h - 12;}
	} else {
		mer = '';
		if ( h < 10 ) {h = '0'+h;}
	}

	timestring = h+':'+m+':'+s+' '+mer;
	return timestring;
}

/* Convert Date Time to Date String */
function radio_date_string(datetime) {

	month = datetime.getMonth(); day = datetime.getDay(); d = datetime.getDate();
	datestring = radio.days[day]+' '+d+' '+radio.months[month];
	return datestring;
}

/* Update Current Time Clock */
function radio_clock_date_time(init) {

	/* user date time */
	userdatetime = new Date();
	useroffset  = -(userdatetime.getTimezoneOffset());
	usertime = radio_time_string(userdatetime);
	userdate = radio_date_string(userdatetime);

	/* timezone offset */	
	houroffset = parseInt(useroffset) / 60;
	if (houroffset == 0) {userzone = '[UTC]';}
	else {
		if (houroffset > 0) {userzone = '[UTC+'+houroffset+']';}
		else {userzone = '[UTC'+houroffset+']';}
	}

	/* server date time */
	serverdatetime = new Date();
	servertime = serverdatetime.getTime();
	serveroffset = ( -(useroffset) + (radio.timezone_offset * 60) ) * 60;
	serverdatetime.setTime(userdatetime.getTime() + (serveroffset * 1000) );
	servertime = radio_time_string(serverdatetime);
	serverdate = radio_date_string(serverdatetime);

	/* server timezone code */
	if (typeof radio.timezone_code != 'undefined') {
		serverzone = '['+radio.timezone_code+']';
	} else {serverzone = '';}

	/* update server clocks */
	clocks = document.getElementsByClassName('radio-station-server-clock');
	for (i = 0; i < clocks.length; i++ ) {
		if (clocks[i]) {
			spans = clocks[i].children;
			for (j = 0; j < spans.length; j++) {
				if (spans[j].className == 'server-time') {spans[j].innerHTML = servertime;}
				if (spans[j].className == 'server-date') {spans[j].innerHTML = serverdate;}
				if (init) {
					if (spans[j].className == 'server-zone') {spans[j].innerHTML = serverzone;}
				}
			}
		}
	}

	/* update user clocks */
	clocks = document.getElementsByClassName('radio-station-user-clock');
	for (i = 0; i < clocks.length; i++ ) {
		if (clocks[i]) {
			spans = clocks[i].children;
			for (j = 0; j < spans.length; j++) {
				if (spans[j].className == 'user-time') {spans[j].innerHTML = usertime;}
				if (spans[j].className == 'user-date') {spans[j].innerHTML = userdate;}
				if (init) {
					if (spans[j].className == 'user-zone') {spans[j].innerHTML = userzone;}
				}
			}
		}
	}

	/* clock loop */
	setTimeout('radio_clock_date_time();', 1000);
	return true;
}

/* Start the Clock */
setTimeout('radio_clock_date_time(true);', 1000);

/* Debounce Delay Callback */
var radio_resize_debounce = (function () {
	var debounce_timers = {};
	return function (callback, ms, uniqueId) {
		if (!uniqueId) {uniqueId = "nonuniqueid";}
		if (debounce_timers[uniqueId]) {clearTimeout (debounce_timers[uniqueId]);}
		debounce_timers[uniqueId] = setTimeout(callback, ms);
	};
})();
