/* ------------------ */
/* Radio Clock Script */
/* ------------------ */

/* Convert Date Time to Time String */
function radio_time_string(datetime, hours, seconds, override) {

	if (override) {
		isostring = datetime.toISOString();
		h = parseInt(isostring.substr(11,2));
		m = parseInt(isostring.substr(14,2));
	} else {
		h = datetime.getHours();
		m = datetime.getMinutes();
	}

	if (seconds) {
		if (override) {s = parseInt(isostring.substr(17,2));}
		else {s = datetime.getSeconds();}
		if (s < 10) {s = '0'+s;}
	}

	if (m < 10) {m = '0'+m;}
	if (hours == 12) {
		if ( h < 12 ) {mer = radio.units.am;}
		if ( h > 11 ) {mer = radio.units.pm;}
		if ( h == 0 ) {h = '12'; mer = radio.units.am;}
		if ( h > 12 ) {h = h - 12;}
	} else {
		mer = '';
		if ( h < 10 ) {h = '0'+h;}
	}

	timestring = '<span class="rs-hour">'+h+'</span>';
	timestring += '<span class="rs-sep rs-time-sep">'+radio.sep+'</span>';
	timestring += '<span class="rs-minutes">'+m+'</span>';
	if (seconds) {
		timestring += '<span class="rs-sep rs-time-sep">'+radio.sep+'</span>';
		timestring += '<span class="rs-seconds">'+s+'</span>';
	}
	if (mer != '') {timestring += ' <span class="rs-meridiem">'+mer+'</span>';}
	return timestring;
}

/* Convert Date Time to Date String */
function radio_date_string(datetime, day, date, month, override) {
	if (override) {
		isostring = datetime.toISOString();
		m = parseInt(isostring.substr(5,2)) - 1;
		dd = parseInt(isostring.substr(8,2));
		d = datetime.getDay(); /* TODO */
	} else {
		d = datetime.getDay();
		m = datetime.getMonth();
		dd = datetime.getDate();
	}
	datestring = '';
	if (day != '') {
		if (day == 'short') {datestring = radio.labels.sdays[d];}
		else {datestring += radio.labels.days[d];}
	}
	if (date) {datestring += ' '+dd;}
	if (month != '') {
		if (month == 'short') {datestring += ' '+radio.labels.smonths[m];}
		else {datestring += ' '+radio.labels.months[m];}
	}
	return datestring;
}

/* Update Current Time Clock */
function radio_clock_date_time() {

	if (radio_clock_init) {init = false;}
	else {init = true; radio_clock_init = true;}

	/* user datetime / timezone */
	userdatetime = new Date();
	useroffset  = -(userdatetime.getTimezoneOffset());
	if (typeof jstz == 'function') {userzone = jstz.determine().name();}
	else {userzone = Intl.DateTimeFormat().resolvedOptions().timeZone;}

	/* server datetime / offset */
	serverdatetime = new Date();
	serveroffset = ( -(useroffset) * 60) + radio.timezone.offset;
	serverdatetime.setTime(userdatetime.getTime() + (serveroffset * 1000));

	/* get timezone override */
	override = false;
	if (typeof radio_timezone_override == 'function') {
		override = radio_timezone_override();
		if (radio.clock_debug) {console.log('User Timezone Selection Override: '+override);}
		if (override) {
			userzone = override;
			offset = radio_offset_override(false);
			userdatetime.setTime(userdatetime.getTime() + (offset * 60 * 1000));
			useroffset = offset;
		}
	}

	/* loop clock instances */
	clock = document.getElementsByClassName('radio-station-clock');
	for (i = 0; i < clock.length; i++) {
		if (clock[i]) {
			classes = clock[i].className;
			seconds = false; day = ''; date = false; month = ''; zone = false;
			if (classes.indexOf('format-24') > -1) {hours = 24;} else {hours = 12;}
			if (classes.indexOf('seconds') > -1) {seconds = true;}
			if (classes.indexOf('day') > -1) {
				if (classes.indexOf('day-short') > -1) {day = 'short';} else {day = 'full';}
			}
			if (classes.indexOf('date') > -1) {date = true;}
			if (classes.indexOf('month') > -1) {
				if (classes.indexOf('month-short') > -1) {month = 'short';} else {month = 'full';}
			}
			if (classes.indexOf('zone') > -1) {zone = true;}
			servertime = radio_time_string(serverdatetime, hours, seconds, false);
			serverdate = radio_date_string(serverdatetime, day, date, month, false);
			usertime = radio_time_string(userdatetime, hours, seconds, override);
			userdate = radio_date_string(userdatetime, day, date, month, override);

			/* loop server / user clocks */
			clocks = clock[i].children;
			for (j = 0; j < clocks.length; j++) {
				if (clocks[j]) {
					classes = clocks[j].className;

					/* update server clock */
					if (classes.indexOf('radio-station-server-clock') > -1) {
						divs = clocks[j].children;
						for (k = 0; k < divs.length; k++) {
							if ((divs[k].className == 'radio-server-time') && (divs[k].innerHTML != servertime)) {divs[k].innerHTML = servertime;}
							else if ((divs[k].className == 'radio-server-date') && (divs[k].innerHTML != serverdate)) {divs[k].innerHTML = serverdate;}
							else if (init && zone && (divs[k].className == 'radio-server-zone')) {
								/* server timezone */
								format = divs[k].getAttribute('data-format'); f = format.split('-');
								serverzone = radio.timezone.location; szone = '';
								serverzone = serverzone.replace('_',' '); parts = serverzone.split('/');
								if (typeof radio.timezone.code != 'undefined') {code = radio.timezone.code} else {code = '';}
								if ((f[0] == '1') && (code != '')) {szone += code+' ';}
								if ((f[3] == '1') && radio.timezone.utczone) {
									szone += ' [UTC'+radio.timezone.utc+']';
								} else {
									if ((f[1] == '1') || (f[2] == '1')) {
										if ((f[0] == '1') && (code != '')) {szone += '(';}
										if (f[2] == '1') {szone += parts[1];}
										if ((f[1] == '1') && (f[2] == '1')) {szone += ', ';}
										if (f[1] == '1') {szone += parts[0];}
										if ((f[0] == '1') && (code != '')) {szone += ')';}
									}
									if (f[3] == '1') {szone += ' ['+radio.timezone.utc+']';}
								}
								if (divs[k].innerHTML != szone) {divs[k].innerHTML = szone;}
							}
						}
					}

					/* update user clock */
					if (classes.indexOf('radio-station-user-clock') > -1) {
						divs = clocks[j].children;
						for (k = 0; k < divs.length; k++) {
							if ((divs[k].className == 'radio-user-time') && (divs[k].innerHTML != usertime)) {divs[k].innerHTML = usertime;}
							else if ((divs[k].className == 'radio-user-date') && (divs[k].innerHTML != userdate)) {divs[k].innerHTML = userdate;}
							else if (init && zone && (divs[k].className == 'radio-user-zone')) {
								/* user timezone offset */
								format = divs[k].getAttribute('data-format'); f = format.split('-');
								code = radio_timezone_code(userzone); /* console.log(code); */
								/* console.log(radio.timezone.zonename_override); */
								if ((typeof radio.timezone.zonename_override != 'undefined') && radio.timezone.zonename_override) {code = radio.timezone.zonename_override;}
								uzone = userzone.replace('_',' '); parts = uzone.split('/');
								uzone = '';
								if ((f[0] == '1') && (code != '')) {uzone += code+' ';}
								if ((f[1] == '1') || (f[2] == '1')) {
									if ((f[0] == '1') && (code != '')) {uzone += '(';}
									if (f[2] == '1') {uzone += parts[1];}
									if ((f[1] == '1') && (f[2] == '1')) {uzone += ', ';}
									if (f[1] == '1') {uzone += parts[0];}
									if ((f[0] == '1') && (code != '')) {uzone += ')';}
								}
								if (f[3] == '1') {
									houroffset = parseInt(useroffset);
									if (houroffset == 0) {uzone += ' [UTC]';}
									else {
										houroffset = houroffset / 60;
										if (houroffset > 0) {uzone += ' [UTC+'+houroffset+']';}
										else {uzone += ' [UTC'+houroffset+']';}
									}
								}
								if (divs[k].innerHTML != uzone) {divs[k].innerHTML = uzone;}
							}
						}
					}
				}
			}
		}
	}

	/* clock loop */
	setTimeout(radio_clock_date_time, 1000);
	return true;
}

/* Start the Clock */
var radio_clock_init = false;
setTimeout(radio_clock_date_time, 1000);
