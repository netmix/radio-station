/* --------------------- */
/* Radio Station ScriptS */
/* --------------------- */

/* Convert Date Time to Time String */
function radio_time_string(datetime) {

	h = datetime.getHours(); m = datetime.getMinutes(); s = datetime.getSeconds();
	if (m < 10) {m = '0'+m;}
	if (s < 10) {s = '0'+s;}

	if (radio_clock_format == '12') {
		if ( h < 12 ) {mer = radio_am;}
		if ( h == 0 ) {h = '12';}	
		if ( h > 11 ) {mer = radio_pm;}
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
	datestring = radio_days[day]+' '+d+' '+radio_months[month];
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
	houroffset = parseInt(useroffset / 60);
	if (houroffset == 0) {userzone = '[UTC]';}
	else {
		if (houroffset > 0) {userzone = '[UTC+'+houroffset+']';}
		else {userzone = '[UTC'+houroffset+']';}
	}

	/* server date time */
	serverdatetime = new Date();
	servertime = serverdatetime.getTime()
	serveroffset = ( -(useroffset) + (radio_timezone_offset * 60) ) * 60;
	serverdatetime.setTime(userdatetime.getTime() + (serveroffset * 1000) );
	servertime = radio_time_string(serverdatetime);
	serverdate = radio_date_string(serverdatetime);

	/* server timezone code */
	if (typeof radio_timezone_code != 'undefined') {
		serverzone = '['+radio_timezone_code+']';
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
	setTimeout('radio_clock_date_time();', '1000');
	return true;
}

/* Start the Clock */
setTimeout('radio_clock_date_time(true);', '1000');
