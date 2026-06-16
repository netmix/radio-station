/* =============================== */
/* === Radio Player Javascript === */
/* --------- Version 1.0.5 ------- */
/* =============================== */

/* === Debounce Delay Callback === */
var radio_player_debounce = (function () {
	var player_debouncers = {};
	return function (callback, ms, uniqueId) {
		if (!uniqueId) {uniqueId = "nonuniqueid";}
		if (player_debouncers[uniqueId]) {clearTimeout (player_debouncers[uniqueId]);}
		player_debouncers[uniqueId] = setTimeout(callback, ms);
	};
})();

/* === Cookie Value Function === */
radio_player_cookie = {
	set: function (name, value, days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
			var expires = '; expires=' + date.toUTCString();
		} else {var expires = '';}
		document.cookie = 'radio_player_' + name + '=' + JSON.stringify(value) + expires + '; path=/';
	},
	get: function(name) {
		var nameeq = 'radio_player_' + name + '=', ca = document.cookie.split(';');
		for (var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1, c.length);
				if (c.indexOf(nameeq) == 0) {
					/* 1.0.1: fix for possible empty value */
					value = c.substring(nameeq.length, c.length).trim();
					if (value == '') {return null;}
					return JSON.parse(value);
				}
			}
		}
		return null;
	},
	delete: function(name) {
		radio_player_cookie.set(name,'',-1);
	}
}

/* === Load Audio Functions === */

/* --- check data for format/script --- */
function radio_player_check_format(data) {

	if (radio_player.debug) {console.log(data);}
	script = false; scripts = radio_player.scripts;
	url = data.url; fallback = data.fallback; format = fformat = '';
	if (typeof data.format != 'undefined') {format = data.format.toLowerCase();}
	if (typeof data.fformat != 'undefined') {fformat = data.fformat.toLowerCase();}

	/* attempt to get format from URL */
	/* TODO: add more possible formats for detection ? */
	formats = ['mp3','aac','m4a','mp4','ogg','oga','webm','rtmpa','wav','flac'];
	if (format == '') {
		if (radio_player.debug) {console.log('Detecting stream format from URL '+url);}
		for (i = 0; i < formats.length; i++) {
			length = formats[i].length;
			if (url.substr(-length,length) == formats[i]) {format = formats[i];}
		}
		/* 2.5.18: added partial path match URL checking */
		if (format == '') {
			for (i = 0; i < formats.length; i++) {
				checkurl = new URL(url); urlpath = checkurl.pathname + checkurl.search;
				if (urlpath.indexOf(formats[i]) > -1) {format = formats[i];}
			}
		}
	}
	if (fformat == '') {
		if (radio_player.debug) {console.log('Detecting fallback format from URL '+url);}
		for (i = 0; i < formats.length; i++) {
			length = formats[i].length;
			if (fallback.substr(-length,length) == formats[i]) {fformat = formats[i];}
		}
		/* 2.5.18: added partial path match URL checking */
		if (fformat = '') {
			for (i = 0; i < formats.length; i++) {
				checkurl = new URL(fallback); urlpath = checkurl.pathname + checkurl.search;
				if (urlpath.indexOf(formats[i]) > -1) {fformat = formats[i];}
			}
		}
	}

	/* --- check against default script setting */
	if (radio_player.settings.script) {
		defaultscript = radio_player.settings.script;
		if (defaultscript in scripts) {
			if (format in radio_player.formats[defaultscript]) {script = defaultscript;}
			else if (fformat in radio_player.formats[defaultscript]) {
				/* switch to fallback format as recognized */
				/* TODO: could test/improve this process ? */
				script = defaultscript; a = url; b = format;
				url = fallback; format = fformat; fallback = a; fformat = b;
			}
		}
	}

	/* check formats against available scripts */
	if (!script) {
		if (('amplitude' in scripts) && (format in radio_player.formats.amplitude)) {script = 'amplitude';}
		else if (('jplayer' in scripts) && (format in radio_player.formats.jplayer)) {script = 'jplayer';}
		/* else if (('audio5' in scripts) && (format in radio_player.formats.audio5)) {script = 'audio5';}
		else if (('howler' in scripts) && (format in radio_player.formats.howler)) {script = 'howler';}
		else if (('mediaelements' in scripts) && (format in radio_player.formats.mediaelements)) {script = 'mediaelements';} */
		if (!script) {
			if (('amplitude' in scripts) && (fformat in radio_player.formats.amplitude)) {script = 'amplitude';}
			else if (('jplayer' in scripts) && (fformat in radio_player.formats.jplayer)) {script = 'jplayer';}
			/* else if (('audio5' in scripts) && (fformat in radio_player.formats.audio5)) {script = 'audio5';}
			else if (('howler' in scripts) && (fformat in radio_player.formats.howler)) {script = 'howler';}
			else if (('mediaelements' in scripts) && (fformat in radio_player.formats.mediaelements)) {script = 'mediaelements';} */
			if (script) {a = url; b = format; url = fallback; format = fformat; fallback = a; fformat = b;}
		}
		if (!script) {
			if ('amplitude' in scripts) {script = 'amplitude';}
			else if ('jplayer' in scripts) {script = 'jplayer';}
			/* else if ('audio5' in scripts) {script = 'audio5';}
			else if ('howler' in scripts) {script = 'howler';} */
		}
	}

	data.script = script; data.url = url; data.format = format; data.fallback = fallback; data.fformat = fformat;
	return data;
}

/* --- load default radio stream --- */
function radio_player_load_radio(start) {
	script = radio_player.settings.script; instance = radio_player_data.state.instance;
	data = radio_player.stream_data; data.type = 'stream';
	player = radio_player_load_stream(script, instance, data, start);
}

/* --- load HLS source --- */
function radio_player_load_hls(script, instance, data, start) {
	if (!instance) {instance = radio_player_default_instance();}
	data.type = 'hls'; radio_player_data.data[instance] = data;
	jQuery('#radio_player_'+instance).attr('data-channel', data.channel);
	player = radio_player_load_audio(script, instance, data, false);
	radio_player_hls_source(instance, data.url, start);
}

/* --- load a station --- */
function radio_player_load_station(script, instance, data, start) {

	if (!instance) {instance = radio_player_default_instance();}
	data.type = 'station'; radio_player_data.data[instance] = data;
	jQuery('#radio_player_'+instance).attr('data-station', data.station);

	data = radio_player_check_format(data); script = data.script;
	player = radio_player_load_audio(script, instance, data, start);
	if (radio_player.debug) {console.log('Load Station '+station+' - Script: '+script+' - Instance: '+instance+' URL: '+data.url+' Format: '+data.format+' Start:'+start);}
	if (player && start) {
		setTimeout(function() {radio_player_play_on_load(player, script, instance);}, 50);
	}
}

/* --- load a channel --- */
function radio_player_load_channel(script, instance, data, start) {

	if (!instance) {instance = radio_player_default_instance();}
	data.type = 'channel'; radio_player_data.data[instance] = data;
	jQuery('#radio_player_'+instance).attr('data-channel', data.channel);

	data = radio_player_check_format(data); script = data.script;
	player = radio_player_load_audio(script, instance, data, start);
	if (radio_player.debug) {console.log('Load Channel '+channel+' - Script: '+script+' - Instance: '+instance+' URL: '+data.url+' Format: '+data.format+' Start:'+start);}
	if (player && start) {
		setTimeout(function() {radio_player_play_on_load(player, script, instance);}, 50);
	}
}

/* --- load a stream --- */
function radio_player_load_stream(script, instance, data, start) {

	if (!instance) {instance = radio_player_default_instance();}
	data.type = 'stream'; radio_player_data.data[instance] = data;

	data = radio_player_check_format(data); script = data.script;
	if (radio_player.debug) {console.log('Load Stream - Script: '+script+' - Instance: '+instance+' URL: '+data.url+' Format: '+data.format+' Start:'+start);}
	player = radio_player_load_audio(script, instance, data, start);
	if (player && start) {radio_player_play_on_load(player, script, instance);}
}

/* --- load a file --- */
function radio_player_load_file(script, instance, data, start) {

	if (!instance) {instance = radio_player_default_instance();}
	data.type = 'file'; radio_player_data.data[instance] = data;

	data = radio_player_check_format(data); script = data.script;
	player = radio_player_load_audio(script, instance, data, start);
	if (radio_player.debug) {console.log('Load File - Script: '+script+' - Instance: '+instance+' URL: '+data.url+' Format: '+data.format+' Start:'+start);}
	if (player && start) {radio_player_play_on_load(player, script, instance);}
}

/* --- load audio in player --- */
function radio_player_load_audio(script, instance, data, start) {
	if (typeof radio_player_cancel_autoresume == 'function') {radio_player_cancel_autoresume({message:'Audio Loading'});} /* ??? */
	url = data.url; format = data.format; fallback = data.fallback; fformat = data.fformat; type = data.type;
	radio_player_set_data_state(script, instance, data, start);
	loaded = radio_player_check_script(script);
	if (loaded) {
		if (radio_player.hasOwnProperty('delayed_player')) {clearInterval(radio_player.delayed_player);}
		/* initialize the player if script is already loaded */
		if (script == 'amplitude') {player =  radio_player_amplitude(instance, url, format, fallback, fformat);}
		else if (script == 'jplayer') {player = radio_player_jplayer(instance, url, format, fallback, fformat);}
		/* else if (script == 'audio5') {player = radio_player_audio5(instance, url, format, fallback, fformat);}
		else if (script == 'howler') {player = radio_player_howler(instance, url, format, fallback, fformat);}
		else if (script == 'mediaelements') {player = radio_player_mediaelements(instance, url, format, fallback, fformat);} */
		if (player) {
			detail = {script:script, instance:instance, url:url, format:format, fallback:fallback, fformat:fformat, type:type, start:start}
			radio_player_event_handler('loading', detail);
			/* note: radio_player_play_on_load called outside this function */
		}
		return player;
	} else {
		/* delay initialization until script is loaded */
		if (radio_player.debug) {console.log('Script not ready, initializing Delayed Player');}
		radio_player.delayed_data = {'time':0, 'start':start, 'script':script, 'instance':instance, 'url':url, 'format':format, 'fallback':fallback, 'fformat':fformat};
		if (radio_player.hasOwnProperty('delayed_player')) {clearInterval(radio_player.delayed_player);}
		radio_player.delayed_player = setInterval(function() {
			radio_player.delayed_data.time++; data = radio_player.delayed_data; script = data.script; player = false;
			if (data.time > 10) {console.log('Script load timed out. Please try again...'); clearInterval(radio_player.delayed_player);}
			if ((script == 'amplitude') && (typeof window.Amplitude != 'undefined') && (typeof radio_player_amplitude != 'undefined')) {
				clearInterval(radio_player.delayed_player);
				player = radio_player_amplitude(data.instance, data.url, data.format, data.fallback, data.fformat);
			} else if ((script == 'jplayer') && (typeof jQuery.jPlayer != 'undefined') && (typeof radio_player_jplayer != 'undefined')) {
				clearInterval(radio_player.delayed_player);
				player = radio_player_jplayer(data.instance, data.url, data.format, data.fallback, data.fformat);
			} /* else if ((script == 'audio5') && (typeof window.audio5 != 'undefined') && (typeof radio_player_audio5 != 'undefined')) {
				clearInterval(radio_player.delayed_player);
				player = radio_player_audio5(data.instance, data.url, data.format, data.fallback, data.fformat);
			} else if ((script == 'howler') && (typeof window.Howl != 'undefined') && (typeof radio_player_howler != 'undefined')) {
				clearInterval(radio_player.delayed_player);
				player = radio_player_howler(data.instance, data.url, data.format, data.fallback, data.fformat);
			} else if ((script == 'mediaelement') && (typeof window.???? != 'undefined') && (typeof radio_player_mediaelement != 'undefined')) {
				clearInterval(radio_player.delayed_player);
				player = radio_player_mediaelements(instance, url, format, fallback, fformat);}
			*/
			if (player) {
				radio_player_event_handler('loading', data);
				radio_player_set_data_state(script, instance, data, data.start);
				/* note: radio_player_play_on_load must be called inside delayed function */
				if (data.start) {radio_player_play_on_load(player, data.script, data.instance);}
			}
		}, 1000);
		return false;
	}
}

/* --- auto start on player load ---*/
function radio_player_play_on_load(player, script, instance) {
	if (typeof player != 'undefined') {
		if (radio_player.debug) {console.log('Loaded '+script+' script: '+(typeof player));}
		detail = radio_player_data.data[instance]; detail.script = script, detail.instance = instance;
		if ((script == 'amplitude') || (script == 'howler')) {
			try {console.log(script+': Play'); player.play();
				radio_player_custom_event('rp-play', detail);
			} catch(e) {console.log(script+' error: could not play stream.'); console.log(e);}
		} else if (script == 'jplayer') {
			if (radio_player.jplayer_ready) {
				try {console.log(script+': Play'); player.jPlayer('play');
					radio_player_custom_event('rp-play', detail);
				} catch(e) {console.log(script+' error: could not play stream.'); console.log(e);}
			} else {
				/* jPlayer not ready, wait until ready to play */
				if (radio_player.debug) {console.log('jPlayer is not yet ready...');}
				if (radio_player.hasOwnProperty('jplayer_load')) {clearInterval(radio_player.jplayer_load);}
				radio_player.jplayer_load = setInterval(function() {
					if (radio_player.jplayer_ready) {
						clearInterval(radio_player.jplayer_load);
						if (radio_player.debug) {console.log('Triggering jPlayer play.');}
						try {player = radio_player_data.players[radio_player.jplayer_instance];
							player.jPlayer('play'); radio_player_custom_event('rp-play', detail);
						} catch(e) {console.log(script+' error: jPlayer could not play stream.'); console.log(e);}
					}
				}, 250);
			}
		} else if (script == 'audio5') {
			try {console.log(script+': Play'); player.play();
				radio_player_custom_event('rp-play', detail);
			} catch(e) {console.log(script+' error: could not play stream.'); console.log(e);}
		}
	} else if (radio_player.debug) {
		 console.log(script+' script not yet loaded...'); console.log(player);
	}
}

// --- check/load a player script ---
function radio_player_check_script(script) {
	loading = false; head = document.getElementsByTagName('head')[0];
	if (script == 'amplitude') {
		if (typeof window.Amplitude == 'undefined') {
			if (radio_player.debug) {console.log('Dynamically Loading Amplitude Player Script...');}
			el = document.createElement('script'); el.src = radio_player.scripts.amplitude; head.appendChild(el); loading = true;
		}
	} else if (script == 'jplayer') {
		if (typeof jQuery.jPlayer == 'undefined') {
			if (radio_player.debug) {console.log('Dynamically Loading jPlayer Script...');}
			el = document.createElement('script'); el.src = radio_player.scripts.jplayer; head.appendChild(el); loading = true;
		}
	} /* else if (script == 'audio5') {
		if (typeof window.audio5 == 'undefined') {
			if (radio_player.debug) {console.log('Dynamically Loading audio5 Player Script...');}
			el = document.createElement('script'); el.src = radio_player.scripts.audio5; head.appendChild(el); loading = true;
		}
	} else if (script == 'howler') {
		if (typeof window.Howl == 'undefined') {
			if (radio_player.debug) {console.log('Dynamically Loading Howler Player Script...');}
			el = document.createElement('script'); el.src = radio_player.scripts.howler; head.appendChild(el); loading = true;
		}
	} else if ( ( script == 'mediaelement') && (typeof mejs == 'undefined' ) ) {
		el = document.createElement('script'); el.src = radio_player.scripts.media; head.appendChild(el);
		el = document.createElement('script'); el.src = radio_player.scripts.elements; head.appendChild(el);
		el = document.createElement('script'); el.src = funcs; head.appendChild(el);
		loading = true;
	} */
	if (loading) {return false;}
	return true;
}

/* --- player failed fallback to another script --- */
function radio_player_player_fallback(instance, script) {
	if (typeof radio_player.delayed_player != 'undefined') {clearInterval(radio_player.delayed_player);}
	if (typeof radio_player_data.failed[instance] != 'undefined') {j = radio_player_data.failed[instance].length;}
	else {radio_player_data.failed[instance] = []; j = 0;}

	scripta = script+'-a'; scriptb = script+'-b';
	if (!(scripta in radio_player_data.failed[instance])) {
		radio_player_data.failed[instance][j] = scripta;
	} else if (!(scriptb in radio_player_data.failed[instance])) {
		radio_player_data.failed[instance][j] = scriptb;
	}
	jQuery('#radio_container_'+instance).removeClass('playing').removeClass('loaded');
	data = radio_player_data.data[instance]; data.instance = instance;
	radio_player_event_handler('failed', data);

	/* retry different script with stored player instance data */
	newscript = twoscript = false;
	if (radio_player.scripts.length) {
		for (k in radio_player.scripts) {
			founda = foundb = false;
			for (j = 0; j < radio_player_data.failed[instance].length; j++) {
				if (radio_player_data.failed[instance][j] == k+'-a') {founda = true;}
				if (radio_player_data.failed[instance][j] == k+'-b') {foundb = true;}
			}
			if (!founda || !foundb) {
				if (!newscript) {newscript = k;} else {twoscript = k;}
			}
		}
	}
	
	/* maybe swap to fallback stream data to retry */
	if (newscript && !foundb) {
		if (data.fallback != '') {
			if (radio_player.debug) {console.log('Switching to Fallback Stream');}
			tmpa = data.url; data.url = data.fallback; data.fallback = tmpa;
			tmpb = data.format; data.fformat = data.format; data.fformat = tmpb;
			data.switched = true; radio_player_data.data[instance] = data;
			radio_player_load_audio(newscript,instance,data,data.start); return;
		} else {
			/* no fallback, set as failed and try next script */
			j++; radio_player_data.failed[instance][j] = scriptb;
			if (twoscript) {newscript = twoscript;} else {newscript = false;}
		}
	}

	/* try new script */
	if (newscript) {
		if ((typeof data.switched != 'undefined') && data.switched) {
			/* switch data back to original for new script retry */
			tmpa = data.url; data.url = data.fallback; data.fallback = tmpa;
			tmpb = data.format; data.fformat = data.format; data.fformat = tmpb;
			data.switched = false; radio_player_data.data[instance] = data;
		}
		radio_player_data.data[instance].script = newscript;
		if (radio_player.debug) {console.log('Trying New Player Script: '+newscript); console.log(data);}
		radio_player_load_audio(newscript,instance,data,data.start);
	} else {
		if (radio_player.debug) {console.log('Exhausted All Player Script Type Attempts');}
		radio_player_data.failed = []; /* reset */
	}
}

/* --- show manual script switcher --- */
function radio_player_show_switcher(instance) {
	jQuery('#radio_container_'+instance+' .rp-show-switcher').hide();
	jQuery('#radio_container_'+instance+' .rp-script-select').show();
}

/* --- switch player script --- */
function radio_player_switch_script(instance, script) {
	radio_player.loading = true;
	data = radio_player_data.data[instance];
	if ((typeof radio_player_data.players[instance] != 'undefined') && (typeof radio_player_data.scripts[instance] != script)) {radio_player_stop_instance(instance,true);}
	player = radio_player_load_audio(script,instance,data,data.start);
	if (player && data.start) {radio_player_play_on_load(player,script,instance);}
}

/* === Player Functions and Event Callbacks === */

/* --- get player source --- */
function radio_player_get_source(player, script) {
	src = false;
	if (script == 'amplitude') {src = player.getAudio().src;}
	else if (script == 'jplayer') {src = player.data('jPlayer').status.src;}
	/* else if (script == 'audio5') {src = player.???;} */
	/* else if (script == 'howler') {src = player.???;} */
	return src;
}

/* --- get instance audio element --- */
function radio_player_audio_element(instance) {
	if (typeof radio_player_data.players[instance] == 'undefined') {return false;}
	player = radio_player_data.players[instance];
	script = radio_player_data.scripts[instance];
	if (script == 'amplitude') {audio = player.getAudio();}
	else if (script == 'jplayer') {audio = player.data('jPlayer').htmlElement.audio;}
	/* else if (script == 'howler') {audio = ???;} */
	return audio;
}

/* --- get event source --- */
function radio_player_event_source(e) {
	src = false; instance = e.detail.instance;
	if (e.detail.player != null) {
		player = e.detail.player; script = e.detail.script;
		src = radio_player_get_source(player,script);
	} else if (typeof radio_player_data.players[instance] != 'undefined') {
		player = radio_player_data.players[instance];
		script = radio_player_data.scripts[instance];
		src = radio_player_get_source(player,script);
	} else if (typeof radio_player_data.data[instance] != 'undefined') {
		src = radio_player_data.data[instance].url;
	}
	return src;
}
	
/* --- get data for channel id --- */
function radio_player_channel_data(channel) {
	for (i in radio_player_data.channels) {
		data = radio_player_data.channels[i];
		if (parseInt(data.id) == parseInt(channel)) {return data;}
	}
	return false;
}

/* --- get data for station id --- */
function radio_player_station_data(station) {
	for (i in radio_player_data.stations) {
		data = radio_player_data.stations[i];
		if (parseInt(data.id) == parseInt(station)) {return data;}
	}
	return false;
}

/* --- play source --- */
function radio_player_play_source(instance) {
	container = jQuery('#radio_container_'+instance);
	script = radio_player.settings.script;
	if (typeof container.attr('data-script') != 'undefined') {script = container.attr('data-script');}
	
	data = radio_player_get_data(instance);
	if (data.type == 'file') {
		radio_player_load_file(script, instance, data, true);
	} else if (data.type == 'channel') {
		radio_player_load_channel(script, instance, data, true);
	} else if (data.type == 'station') {
		radio_player_load_station(script, instance, data, true);
	} else if (data.type == 'stream') {
		radio_player_load_stream(script, instance, data, true);
	} else if (data.type == 'hls') {
		radio_player_load_hls(script, instance, data, true);
	}
}

/* --- get data to play --- */
function radio_player_get_data(instance) {
	container = jQuery('#radio_container_'+instance);
	data = false; /* ensure reset */
	if (typeof container.attr('data-href') != 'undefined') {
		href = container.attr('data-href');
		if (href.trim() != '') {
			format = container.attr('data-format');
			fallback = container.attr('data-fallback');
			fformat = container.attr('data-fformat');
			data = {url:href, format:format, fallback:fallback, fformat:fformat, type:'file'};
			if (radio_player.debug) {console.log('File Source Data:');}
		}
	}
	if (!data) {
		/* get HLS data */
		if ((typeof container.attr('data-hls') != 'undefined') && (container.attr('data-hls') != '')) {
			url = container.attr('data-hls');
			channel = parseInt(container.attr('data-channel'));
			if (Number.isNaN(channel)) {channel = 0;}
			data = {url:url, format:'hls', fallback:false, fformat:false, channel:channel, type:'hls'};
			if (radio_player.debug) {console.log('HLS Source Data:');}
		}
	}
	if (!data) {
		/* get channel data */
		channel = parseInt(container.attr('data-channel'));
		if (!Number.isNaN(channel)) {
			data = radio_player_channel_data(channel); data.type = 'channel'; data.channel = channel;
			if (radio_player.debug) {console.log('Channel Source Data:');}
		}
	}
	if (!data) {
		/* get station data */
		station = parseInt(container.attr('data-station'));
		if (!Number.isNaN(station)) {
			data = radio_player_station_data(station); data.type = 'station'; data.station = station;
			if (radio_player.debug) {console.log('Station Source Data:');}
		}		
	}
	/* get default stream data */
	if (!data) {
		data = radio_player.stream_data; data.type = 'stream';
		if (radio_player.debug) {console.log('Stream Source Data:');}
	}
	if (radio_player.debug) {console.log(data);}
	return data;
}
				
/* --- play player instance --- */
var radio_player_retry = {}
function radio_player_play_instance(instance) {
	if (typeof radio_player_autoresume == 'object') {radio_player_autoresume.cancelled = true;}
	if (radio_player_is_playing(instance)) {return;}
	radio_player.loading = true;
	container = jQuery('#radio_container_'+instance);
	if (container.hasClass('rewound') || container.hasClass('timeshift')) {
		audio = radio_player_audio_element(instance);
		if (typeof hls != 'undefined') {
			hls.config.autoStartLoad = true; hls.startLoad(); hls.attachMedia(audio);
		}
		audio.play().catch( () => { radio_player_hls_source(instance,false,true); });
	} /* else { */
		player = radio_player_data.players[instance]; script = radio_player_data.scripts[instance];
		if (script == 'amplitude') {player.play();}
		else if (script == 'jplayer') {player.jPlayer('play');}
		else if ((script == 'audio5') || (script == 'howler')) {player.play();}
		if (radio_player.debug) {console.log('Playing '+script+' Player Instance '+instance);}
	/* } */
	radio_player_custom_event('rp-play', {player:player, script:script, instance:instance});
}

/* --- pause player instance --- */
function radio_player_pause_instance(instance) {
	/* if (!radio_player_is_loading(instance) && !radio_player_is_playing(instance)) {return;} */
	player = radio_player_data.players[instance]; script = radio_player_data.scripts[instance];
	detail = {player:player, script:script, instance:instance};
	radio_player_custom_event('rp-before-pause', detail);
	if (radio_player.debug) {console.log('Pausing '+script+' Player Instance '+instance);}
	container = jQuery('#radio_container_'+instance);
	audio = radio_player_audio_element(instance);
	if (script == 'amplitude') {
		if (container.hasClass('rewound') || container.hasClass('timeshift')) {audio.pause();}
		else {src = player.getAudio().src;}
	}
	if ((script == 'amplitude') || (script == 'audio5') || (script == 'howler')) {player.pause();}
	else if (script == 'jplayer') {player.jPlayer('pause');}
	if (script == 'amplitude') {
		/* amplitude empty src on pause error fix! */
		if (container.hasClass('rewound') || container.hasClass('timeshift')) {
			if (typeof hls != 'undefined') {hls.config.autoStartLoad = false; hls.stopLoad();}
			audio.pause();
		} else {player.getAudio().src = src;}
	}
	radio_player_custom_event('rp-pause', detail);
}

/* --- stop player instance --- */
function radio_player_stop_instance(instance) {
	player = radio_player_data.players[instance]; script = radio_player_data.scripts[instance];
	if (radio_player.debug) {console.log('Stopping '+script+' Player Instance '+instance);}
	if (script == 'amplitude') {
		/* ? amplitude bug: 'stop is not a function'! ? */
		try {player.stop();} catch(e) {player.pause();}
	} else if (script == 'jplayer') {player.jPlayer('stop');}
	else if (script == 'audio5') {player.destroy();}
	else if (script == 'howler') {player.unload();}
	radio_player_custom_event('rp-stop', {player:player, script:script, instance:instance});
}

/* --- mute or unmute player --- */
function radio_player_mute_instance(instance, player, script, mute) {
	container = jQuery('#radio_container_'+instance);
	if (script == 'amplitude') {
		volume = player.getVolume();
		if (mute) {
			container.attr('data-volume',volume);
			if (radio_player.debug) {console.log('Set Pre-muted Volume: '+volume);}
			player.setVolume(0);
		} else if (volume == 0) {
			if (typeof container.attr('data-volume') != 'undefined') {
				volume = container.attr('data-volume');
				if (radio_player.debug) {console.log('Restore Pre-muted Volume: '+volume);}
				try {player.setVolume(volume);} catch(e) {}
			}
		}
	} else if (script == 'audio5') {
		volume = player.volume();
		if (mute) {
			container.attr('data-volume',(volume * 100));
			if (radio_player.debug) {console.log('Set Pre-muted Volume: '+volume);}
			player.volume(0);
		} else if (volume == 0) {
			if (typeof container.attr('data-volume') != 'undefined') {
				volume = parseInt(container.attr('data-volume')) / 100;
				if (radio_player.debug) {console.log('Restore Pre-muted Volume: '+volume);}
				try {player.volume(volume);} catch(e) {}
			}
		}
	} else if (script == 'jplayer') {
		if (mute) {player.jPlayer('mute');} else {player.jPlayer('unmute');}
	} else if (script == 'howler') {
		if (mute) {player.mute(true);} else {player.mute(false);}
	}
}

/* --- check if player is playing */
function radio_player_is_playing(instance) {
	if (typeof jQuery('#radio_container_'+instance).attr('data-hls') != 'undefined') {
		audio = radio_player_audio_element(instance)
		playing = (audio && !audio.paused && !audio.ended && (audio.currentTime > 0) && (audio.readyState > 2));
		script = 'HLS';
	} else {
		instances = Object.keys(radio_player_data.players);
		if (!instances.includes(instance)) {return false;}
		player = radio_player_data.players[instance]; script = radio_player_data.scripts[instance];
		if (script == 'amplitude') {
			state = player.getPlayerState();
			playing = (state == 'playing');
		} else if (script == 'jplayer') {
			/* ? possible bug: get status not working ? */
			try {playing = !player.data('jPlayer').status.paused;}
			catch(e) {playing = !player.data().jPlayer.status.paused;}
		} else if (script == 'audio5') {
			playing = player.playing;
		} else if (script == 'howler') {
			playing = player.playing();
		}
	}
	if (radio_player.debug) {
		if (playing) {console.log('Player Instance '+instance+' ('+script+') is playing.');}
		else {console.log('Player Instance '+instance+' ('+script+') is not playing.');}
	}
	return playing;
}

/* --- check if player instance is loading --- */
function radio_player_is_loading(instance) {
	player = jQuery('#radio_player_'+instance);
	if (player.hasClass('loading')) {return true;}
	return false;	
}
	
/* --- change player volume --- */
function radio_player_change_volume(instance, volume) {
	container = jQuery('#radio_container_'+instance);
	container.attr('data-volume',volume);
	if ((volume == 100) && !container.hasClass('maxed')) {container.addClass('maxed');}
	else {container.removeClass('maxed');}
	slider = jQuery('#radio_container_'+instance+' .rp-volume-slider');
	if (!slider.hasClass('changed')) {slider.addClass('changed');}

	if (typeof radio_player_data.players[instance] != 'undefined') {
		player = radio_player_data.players[instance]; script = radio_player_data.scripts[instance];
		radio_player_set_volume(instance,volume);
		detail = {player:player, script:script, instance:instance, volume:volume};
		radio_player_custom_event('rp-volume', detail);
	} else {
		detail = {player:null, script: null, instance: instance, volume: volume};
	}
	radio_player_custom_event('rp-volume',detail);
}

/* --- set player volume --- */
function radio_player_set_volume(instance, volume) {
	player = radio_player_data.players[instance]; script = radio_player_data.scripts[instance];
	if (script == 'amplitude') {
		player.setVolume(volume); newvolume = player.getVolume();
		if (radio_player.debug) {console.log('Amplitude New Volume: '+volume+' - Now: '+newvolume);}
	} else if (script == 'jplayer') {
		volume = parseFloat(volume / 100);
		/* note: this catches (browser?) error "cannot set to non-finite value" in jPlayer _html_setProperty */
		try {player.jPlayer('volume', volume); newvolume = player.jPlayer('volume');} catch(e) {}
		if (radio_player.debug) {console.log('jPlayer New Volume: '+volume+' - Now: '+newvolume);}
	} else if (script == 'audio5') {
		volume = parseFloat(volume / 100);
		player.volume(volume); newvolume = player.volume();
		if (radio_player.debug) {console.log('audio5 New Volume: '+volume+' - Now: '+newvolume);}
	} else if (script == 'howler') {
		volume = parseFloat(volume / 100);
		player.volume(volume); newvolume = player.volume();
		if (radio_player.debug) {console.log('Howler New Volume: '+volume+' - Now: '+newvolume);}
	}
}

/* --- get player volume --- */
function radio_player_get_volume(instance) {
	player = radio_player_data.players[instance]; script = radio_player_data.scripts[instance];
	if (script == 'amplitude') {volume = player.getVolume();}
	else if (script == 'jplayer') {volume = (player.jPlayer('volume') * 100);}
	else if ((script == 'audio5') || (script == 'howler')) {volume = (player.volume() * 100);}
	return volume;
}

/* --- set all volume sliders --- */
function radio_player_volume_sliders(volume) {
	jQuery('.radio-container').each(function() {
		instance = jQuery(this).attr('data-instance');
		radio_player_volume_slider(instance,volume);
	});
}

/* --- set slider volume with background div width fix --- */
function radio_player_volume_slider(instance, volume) {
	jQuery('#radio_container_'+instance+', #rp_container_'+instance).each(function() {
		slider = jQuery(this).find('.rp-volume-slider');
		slider.addClass('changed');
		sliderbg = jQuery(this).find('.rp-volume-slider-bg');
		thumb = jQuery(this).find('.rp-volume-thumb');
		if (slider.length) {
			sliderbg.hide(); slider.val(volume); swidth = slider.width();
			thumb.show(); twidth = thumb.width(); thumb.hide();
			mwidth = parseInt(sliderbg.css('margin-left').replace('px',''));
			bgwidth = parseInt((swidth - twidth) * (volume / 100)) - mwidth;
			sliderbg.attr('style', 'width: '+bgwidth+'px !important;').show();
			/* if (radio_player.debug) {
				newwidth = parseInt(sliderbg.css('width')); console.log('Volume Slider BF: Slider '+swidth+' : Thumb '+twidth+' : Margin '+mwidth+' : BG '+bgwidth+' : Now '+newwidth);
			} */
			if (volume == 0) {jQuery(this).addClass('minned');} else {jQuery(this).removeClass('minned');}
			if (volume == 100) {jQuery(this).addClass('maxed');} else {jQuery(this).removeClass('maxed');}	
		}
	});
}

/* --- mute or unmute a player -- */
function radio_player_mute_unmute(instance, mute) {
	jQuery('#radio_container_'+instance+', #rp_container_'+instance).each(function() {
		if (mute) {jQuery(this).addClass('muted').removeClass('maxed');} else {jQuery(this).removeClass('muted');}
	});
	detail = {player:null, script:null, instance:instance, mute:mute};
	if (typeof radio_player_data.players[instance] != 'undefined') {
		player = radio_player_data.players[instance]; script = radio_player_data.scripts[instance];
		radio_player_mute_instance(instance,player,script,mute);
		detail = {player:player, script:script, instance:instance, mute:mute};
	}
	if (mute) {radio_player_custom_event('rp-muted', detail);}
	else {radio_player_custom_event('rp-unmuted', detail);}
}
	
/* --- get page default instance --- */
function radio_player_default_instance() {
	instance = false;
	jQuery('.radio-player').each(function() {
		if (!instance && jQuery(this).hasClass('default-player')) {
			instance = parseInt(jQuery(this).attr('id').replace('radio_player_', ''));
			return instance;
		}
	});
	return instance;
}

/* --- pause all other instances */
function radio_player_pause_others(instance) {
	if (!radio_player.settings.singular) {return;}
	if (typeof radio_player_toggle_current == 'function') {return;} /* skip for pro */
	/* broadcast playing message to other windows */
	radio_player_broadcast_playing(instance);
}

/* --- get instance from event target --- */
function radio_player_event_instance(e, name, script) {
	instance = false;
	if (radio_player.debug) {
		console.log(script+' Player Event: '+name);
		if ((script == 'jplayer') && (typeof e.jPlayer.error != 'undefined')) {console.log(e.jPlayer.error);}
		else if ((typeof e.target.error != 'undefined') && (e.target.error != null)) {console.log(e.target.error);}
	}
	if ((typeof e.target != 'undefined') && (e.target != null)) {
		if (radio_player.debug) {console.log('Event Target'); console.log(e.target);}
		if (e.target.hasAttribute('instance-id')) {
			instance = e.target.getAttribute('instance-id');
		} else if (jQuery(e.target)) {
			instance = parseInt(jQuery(e.target).attr('id').replace('radio_player_',''));
		}
	}
	/* if (instance && radio_player.debug) {console.log('Event Player Instance: '+instance);} */
	return instance;
}

/* --- match instance from player object --- */
function radio_player_match_instance(obj, e, script) {
	instance = false;
	for (i = 0; i < radio_player_data.players.length; i++) {
		if (obj == radio_player_data.players[i]) {instance = i;}
	}
	return instance;
}

/* --- player event handler --- */
function radio_player_event_handler(action, detail) {
	if (typeof detail != 'object') {
		if (radio_player.debug) {console.log('Event Handler Error for Action: '+action); console.log(detail);}
		return;
	}
	instance = detail.instance; script = detail.script;
	if (radio_player.debug) {console.log(script+' Player Instance '+instance+' : '+action);}
	jQuery('#radio_container_'+instance+', #rp_container_'+instance).each(function() {
		if (action == 'loading') {jQuery(this).removeClass('loaded playing stopped paused error');}
		else if (action == 'loaded') {jQuery(this).removeClass('loading error');}
		else if (action == 'playing') {jQuery(this).removeClass('paused stopped error');}
		else if (action == 'paused') {jQuery(this).removeClass('playing stopped'); }
		else if (action == 'stopped') {jQuery(this).removeClass('playing paused loaded loading'); radio_player_set_state('playing',false);}
		else if (action == 'error') {jQuery(this).removeClass('playing paused loaded loading');}
		if (!jQuery(this).hasClass(action)) {jQuery(this).addClass(action);}
	});
	if (action == 'playing') {radio_player_set_state('playing',true);}
	if ((action == 'paused') || (action == 'stopped')) {radio_player_set_state('playing',false);}
	radio_player_custom_event('rp-'+action, detail);
}

/* --- player volume changed --- */
function radio_player_volume_changed(instance, script, volume) {
	if (radio_player.debug) {console.log(script+' Player Instance '+instance+': Changed Volume to '+volume);}
	slider = jQuery('#radio_container_'+instance+' .rp-volume-slider');
	if (!slider.hasClass('changed')) {jQuery('#radio_container_'+instance+' .rp-volume-slider').addClass('changed');}

	radio_player_custom_event('rp-volume-changed', {instance:instance, script:script, volume:volume});
	radio_player_set_state('volume', volume);
}

/* --- sleep delay function (blocking!) --- */
function radio_player_sleep_delay(sleep_ms) {
	var radio_player_sleep_start = new Date().getTime();
	while (sleep_ms > (new Date().getTime() - radio_player_sleep_start)) {}
}

/* === User Player State Functions === */

/* --- load player state --- */
function radio_player_load_state() {
	if (typeof radio_player_data.state.checked == 'undefined') {
		playing = radio_player_cookie.get('playing');
		if (playing) {radio_player_data.state.playing = playing;}
		channel = radio_player_cookie.get('channel');
		if (channel != null) {radio_player_data.state.channel = channel;}
		station = radio_player_cookie.get('station');
		if (station != null) {radio_player_data.state.station = station;}
		volume = radio_player_cookie.get('volume');
		if (volume != null) {radio_player_data.state.volume = volume;}
		mute = radio_player_cookie.get('mute');
		if (mute != null) {radio_player_data.state.mute = mute;}
		if (radio_player.debug) {
			console.log('Loaded User Player State - Playing: '+playing+' - Station: '+station+' - Volume: '+volume+ ' - Muted: '+mute+' - Data: ');
		}
		data = radio_player_cookie.get('data');
		if ((data != null) && (data.url != '')) {
			radio_player_data.state.data = data;
			if ((data.instance == false) || (data.instance == 'undefined')) {data.instance = 1;}
			radio_player_data.data[data.instance] = data;
			if (radio_player.debug) {console.log('Radio State Data:'); console.log(radio_player_data.data);}
		}
		if ((volume != null) && (data != null)) {radio_player_volume_sliders(volume);}
		
		/* set sync debug cookie switch */
		if (typeof radio_player.sync_debug != 'undefined') {
			if (radio_player.sync_debug == 'on') {
				radio_player.sync_debug = true;
				radio_player_cookie.set('sync_debug',1,7);
			} else if (radio_player.sync_debug == 'off') {
				radio_player.sync_debug = false;
				radio_player_cookie.delete('sync_debug');
			} else {
				sync_debug = radio_player_cookie.get('sync_debug');
				if (sync_debug != null) {radio_player.sync_debug = true;}
				else {radio_player.sync_debug = false;}
			}
		} else {radio_player.sync_debug = false;}
		radio_player_data.state.checked = true;
	}
}

/* --- store user player state --- */
function radio_player_set_state(key, value) {
	changed = false;
	if ((key == 'playing') && (value != radio_player_data.state.playing)) {
		radio_player_cookie.set('playing', value, 7);
		radio_player_data.state.playing = value; changed = true;
	} else if ((key == 'channel') && value && (value > 0) && (value != radio_player_data.state.channel)) {
		radio_player_cookie.set('channel', value, 30);
		radio_player_data.state.channel = value; changed = true;
	} else if ((key == 'station') && value && (value > 0) && (value != radio_player_data.state.station)) {
		radio_player_cookie.set('station', value, 30);
		radio_player_data.state.station = value; changed = true;
	} else if ((key == 'volume') && (value != radio_player_data.state.volume)) {
		radio_player_cookie.set('volume', value, 365);
		radio_player_data.state.volume = value; changed = true;
	} else if ((key == 'mute') && (value != radio_player_data.state.mute)) {
		radio_player_cookie.set('mute', value, 1);
		radio_player_data.state.mute = value; changed = true;
	}
	if (changed) {radio_player_data.state.changed = true;}
	detail = {'state': radio_player_data.state}
	radio_player_custom_event('rp-set-state', detail);
}

/* --- store player instance data */
function radio_player_set_data_state(script, instance, data, start) {
	url = data.url; format = data.format; fallback = data.fallback; fformat = data.fformat; type = data.type;
	if (typeof radio_player_data.data[instance] != 'undefined') {
		cdata = radio_player_data.data[instance];
		if ( (cdata.script != script) || (cdata.url != url) || (cdata.format != format) || (cdata.fallback != fallback) || (cdata.fformat != fformat) || (cdata.start != start) ) {
			radio_player_data.failed[instance] = new Array();
		}
		radio_player.previous_data = data;
	}
	data = {'script':script, 'instance':instance, 'url':url, 'format':format, 'fallback':fallback, 'fformat':fformat, 'type':type, 'start':start};
	if (radio_player.debug) {console.log('Setting Data State:'); console.log(data);}
	radio_player_data.data[instance] = data;
	if (radio_player_data.state.data != data) {
		radio_player_data.state.data = data;
		radio_player_cookie.set('data', data, 7);
		radio_player_data.state.changed = true;
	}
}

/* --- save user meta values --- */
function radio_player_save_user_state() {
	if (radio_player_data.state.changed) {
		state = radio_player_data.state;
		if (state.loggedin && !state.saving) {
			if (state.playing) {playing = '1';} else {playing = '0';}
			if (state.station) {station = state.station;} else {station = '0';}
			if (state.channel) {channel = state.channel;} else {channel = '0';}
			if (state.volume) {volume = state.volume;} else {volume = '';}
			if (state.mute) {mute = '1';} else {mute = '0';}
			timestamp = Math.floor( (new Date()).getTime() / 1000 );
			url = radio_player.settings.ajaxurl+'?action=radio_player_state';
			/* ? TODO: instance ? */
			url += '&playing='+playing+'&station='+station+'&channel='+channel+'&volume='+volume+'&mute='+mute+'&timestamp='+timestamp;
			if (radio_player.debug) {url += '&player-debug=1';}
			/* document.getElementById('radio-player-state-iframe').src = url; */
			radio_player_data.state.saving = true;
			jQuery.get(url, function(data) {
				if (radio_player.debug) {console.log(data);}
				radio_player_data.state.saving = false;
			});
		}
	}
	radio_player_data.state.changed = false;
}

/* --- volume change audio test --- */
/* ref: https://stackoverflow.com/a/62094756/5240159 */
/* 2.5.6: fix radio.debug to radio_player.debug */
/* 2.5.13: append testaudio to document body for more robust test */
function radio_player_volume_test() {
	isIOS = ['iPad Simulator','iPhone Simulator','iPod Simulator','iPad','iPhone','iPod'].includes(navigator.platform);
	if (radio_player.debug && isIOS) {console.log('iOS Mobile Device Detected. ');}
    isAppleDevice = navigator.userAgent.includes('Macintosh');
	if (radio_player.debug && isAppleDevice) {console.log('Apple Device Detected.');}
    isTouchScreen = navigator.maxTouchPoints >= 1;
	if (radio_player.debug && isTouchScreen) {console.log('Touch Screen Detected. ');}
	iosAudioFailure = false; testaudio = new Audio(); document.body.appendChild(testaudio);
	try {testaudio.volume = 0.5;} catch(e) {if (radio_player.debug) {console.log('Caught Volume Change Error.');} iosAudioFailure = true;}
	if (testaudio.volume === 1) {if (radio_player.debug) {console.log('Volume could not be changed.');} iosAudioFailure = true;}
    return isIOS || (isAppleDevice && (isTouchScreen || iosAudioFailure));
}

/* === Multi-Window/Tab Support === */

/* --- set/get window tab unique ID --- */
function radio_player_window_guid() {
	if (!window.tabId) {
		if (window.sessionStorage.tabId) {
			window.tabId = window.sessionStorage.tabId;
			window.sessionStorage.removeItem('tabId');
		} else {
			window.tabId = Math.floor(Math.random() * 10000000);
		}
	}
	/* console.log('Window Guid:'+window.tabId); */
	return window.tabId;
}

/* --- broadcast pause all message to other windows --- */
function radio_player_broadcast_playing(instance) {
	if ((instance === false) || (typeof sysend == 'undefined')) {return;}
	if (!radio_player.settings.singular) {return;}

	action = 'pauseothers';
	player = radio_player_data.players[instance];
	script = radio_player_data.scripts[instance]
	url = radio_player_get_source(player,script);
	windowid = radio_player_window_guid();
	
	message = windowid+'::'+instance+'::'+action+'::'+url;
	if (radio_player.sync_debug) {console.log('Broadcast Message: '+message);}
	sysend.broadcast('radio-play', {message: message});
}

/* --- check to pause player on receiving message --- */
function radio_player_check_to_pause(broadcast) {
	if (!radio_player.settings.singular) {return;}
	parts = broadcast.message.split('::');
	winid = parseInt(parts[0]); winstance = parseInt(parts[1]); action = parts[2]; url = parts[3];
	if (radio_player.sync_debug) {console.log('Received Message from Window ID '+winid+' Instance '+winstance+' Type '+action+' URL '+url);}
	windowid = radio_player_window_guid();
	if (!radio_player_data.players.length) {return;}
	for (instance in radio_player_data.players) {
		if ( (winid != windowid) || ((winid == windowid) && (winstance != instance)) ) {
			if (radio_player_is_playing(instance)) {
				if (radio_player.sync_debug) {console.log('Pausing Window '+windowid+' Instance '+instance);}
				radio_player_pause_instance(instance);
			}
		}
	}
}


/* === Audio Player Scripts === */

/* Amplitude Player Script */
var radio_player_amp_doc_listeners = false;
function radio_player_amplitude(instance, url, format, fallback, fformat) {

	container = jQuery('#radio_container_'+instance);
	if (url == '') {url = radio_player.settings.url;}
	if (url == '') {return;}
	if (!format || (format == '')) {format = 'aac';}
	/* if (fallback == '') {fallback = radio_player.settings.fallback;} */
	if (!fallback || !fformat || (fformat == '')) {fallback = ''; fformat = '';}

	/* set song streams */
	songs = new Array();
	songs[0] = {'name': '',	'artist': '', 'album': '', 'url': url, 'cover_art_url': '',	'live': true};
	/* if ('' != fallback) {songs[1] = {'name': '', 'artist': '', 'album': '', 'url': fallback, 'cover_art_url': '', 'live': true};} */

	/* set volume */
	if (container.find('.rp-volume-slider').length && container.find('.rp-volume-slider').hasClass('changed')) {
		volume = container.find('.rp-volume-slider').val();
	} else if (typeof container.attr('data-volume') != 'undefined') {
		volume = container.attr('data-volume');
	} else if (typeof radio_player_data.state.volume != 'undefined') {
		volume = radio_player_data.state.volume;
	} else {volume = radio_player.settings.volume;}
	radio_player_volume_slider(instance, volume);

	/* initialize player */
	if (radio_player.debug) {console.log('Init Amplitude: '+instance+' : '+url+' : '+format+' : '+fallback+' : '+fformat+' : '+volume);}
	radio_player_instance = Amplitude;
	radio_player_instance.init({
		'debug': radio_player.debug,
		'songs': songs,
		'volume': volume,
		'volume_increment': 5,
		'volume_decrement': 5,
		'continue_next': false,
		'preload': 'none',
	});
	radio_player_data.players[instance] = radio_player_instance;
	radio_player_data.scripts[instance] = 'amplitude';

	/* set instance on audio source */
	audio = radio_player_instance.getAudio();
	if (radio_player.debug) {console.log('Amplitude Audio Element:'); console.log(audio);}
	audio.setAttribute('instance-id', instance);

	/* amp 5.0.3 bind loaded to canplay event (as initialized callback not firing!) */
	/* amp 5.3.2 initialized callback is now firing */
	audio.addEventListener('canplay', function(e) {
		radio_player.loading = false;
		instance = radio_player_event_instance(e, 'Loaded', 'amplitude');
		radio_player_event_handler('loaded', {instance:instance, script:'amplitude'});
	}, false);

	/* amp 5.0.3: bind play(ing) event (as play callback not firing!) */
	/* amp 5.3.2: play callback is now firing */
	audio.addEventListener('playing', function(e) {
		radio_player.loading = false;
		instance = radio_player_event_instance(e, 'Playing', 'amplitude');
		radio_player_event_handler('playing', {instance:instance, script:'amplitude'});
		radio_player_pause_others(instance);
	}, false);

	/* bind volume change event */
	audio.addEventListener('volumechange', function(e) {
		instance = radio_player_event_instance(e, 'Volume', 'amplitude');
		if (instance && (radio_player_data.scripts[instance] == 'amplitude')) {
			volume = radio_player_data.players[instance].getConfig().volume;
			radio_player_volume_changed(instance, 'amplitude', volume);
		}
	}, false);
	
	/* bind error event (as event not being passed in callback) */
	/* warning: triggered if audio src is not reset after pause! */
	audio.addEventListener('error', function(e) {
		if ((typeof e.target.error.code != 'undefined') && e.target.error.code == 4) {
			if (radio_player.debug) {console.log('Skipping Amplitude Empty Audio Source Bug');}
			return;
		}
		instance = radio_player_event_instance(e, 'Error', 'amplitude');
		if (radio_player.debug) {console.log('Amplitude Error Event'); console.log(e);}
		radio_player_event_handler('error', {instance:instance, script:'amplitude'});
		radio_player_player_fallback(instance, 'amplitude', 'Amplitude Error');
	}, false);

	/* add document listeners only once */
	if (!radio_player_amp_doc_listeners) {
		radio_player_amp_doc_listeners = true;

		/* listen for pause event */
		document.addEventListener('rp-pause', function(e) {
			instance = e.detail.instance;
			if (radio_player_data.scripts[instance] == 'amplitude') {
				radio_player_event_handler('paused', {instance:instance, script:'amplitude'});
			}
		}, false);
		
		/* listen for stop event */
		document.addEventListener('rp-stop', function(e) {
			instance = e.detail.instance;
			if (radio_player_data.scripts[instance] == 'amplitude') {
				radio_player_event_handler('stopped', {instance:instance, script:'amplitude'});
			}
		}, false);
	}

	/* match script select dropdown value */
	if (container.find(' .rp-script-select').length) {container.find('.rp-script-select').val('amplitude');}

	return radio_player_instance;
}


/* jPlayer Player Script */
function radio_player_jplayer(instance, url, format, fallback, fformat) {

	player_id = 'radio_player_'+instance;
	container_id = 'radio_container_'+instance;
	container = jQuery('#'+container_id);
	if (url == '') {url = radio_player.settings.url;}
	if (url == '') {return;}
	if (!format || (format == '') || (format == 'aac')) {format = 'm4a';}
	/* if (fallback == '') {fallback = radio_player.settings.fallback;} */
	if (!fallback || !fformat || (fformat == '')) {fallback = ''; fformat = '';}
	if (fformat == 'aac') {fformat = 'm4a';}

	/* set volume */
	if (container.find('.rp-volume-slider').length && container.find('.rp-volume-slider').hasClass('changed')) {
		volume = container.find('.rp-volume-slider').val();
	} else if (typeof container.attr('data-volume') != 'undefined') {
		volume = container.attr('data-volume');
	} else if (typeof radio_player_data.state.volume != 'undefined') {
		volume = radio_player_data.state.volume;
	} else {volume = radio_player.settings.volume;}
	radio_player_volume_slider(instance,volume);
	volume = parseFloat(volume / 100);
	if (radio_player.debug) {console.log('jPlayer init Volume: '+volume);}

	media = {}; /* media.title = ''; */ media[format] = url; supplied = format;
	/* if (fallback && fformat) {media[fformat] = fallback; supplied += ', '+fformat;} */
	radio_player.jplayer_media = media;
	radio_player.jplayer_ready = false;
	radio_player.jplayer_instance = instance;

	/* load jplayer */
	if (radio_player.debug) {console.log('Init jPlayer: '+instance+' : '+url+' : '+format+' : '+fallback+' : '+fformat+' : '+volume);}
	radio_player_instance = jQuery('#'+player_id).jPlayer({
		ready: function () {
			console.log(radio_player.jplayer_media);
			jQuery(this).jPlayer('setMedia', radio_player.jplayer_media);
			radio_player.jplayer_ready = true;
		},
		supplied: supplied,
		cssSelectorAncestor: '#'+container_id,
		swfPath: '', /* radio_player.settings.swf_path, */
		idPrefix: 'rp',
		preload: 'none',
		volume: volume,
		globalVolume: true,
		useStateClassSkin: true,
		autoBlur: false,
		smoothPlayBar: true,
		keyEnabled: true,
		remainingDuration: false,
		toggleDuration: false,
		backgroundColor: 'transparent',
	});
	radio_player_data.players[instance] = radio_player_instance;
	radio_player_data.scripts[instance] = 'jplayer';

	audio = radio_player_instance.data('jPlayer').htmlElement.audio;
	if (radio_player.debug) {console.log('jPlayer Audio Element:'); console.log(audio);}
	audio.setAttribute('instance-id', instance);

	/* bind load event */
	jQuery('#'+player_id).bind(jQuery.jPlayer.event.load, function(e) {
		radio_player.loading = false;
		instance = radio_player_event_instance(e, 'Loaded', 'jplayer');
		radio_player_event_handler('loaded', {instance:instance, script:'jplayer'});
	});

	/* bind play event */
	jQuery('#'+player_id).bind(jQuery.jPlayer.event.play, function(e) {
		radio_player.loading = false;
		instance = radio_player_event_instance(e, 'Playing', 'jplayer');
		radio_player_event_handler('playing', {instance:instance, script:'jplayer'});
		radio_player_pause_others(instance);
	});

	/* bind pause and stop events */
	jQuery('#'+player_id).bind(jQuery.jPlayer.event.pause, function(e) {
		instance = radio_player_event_instance(e, 'Paused', 'jplayer');
		radio_player_event_handler('paused', {instance:instance, script:'jplayer'});
	});
	jQuery('#'+player_id).bind(jQuery.jPlayer.event.stop, function(e) {
		instance = radio_player_event_instance(e, 'Stopped', 'jplayer');
		radio_player_event_handler('stopped', {instance:instance, script:'jplayer'});
	});

	/* bind volume change event */
	jQuery('#'+player_id).bind(jQuery.jPlayer.event.volumechange, function(e) {
		instance = radio_player_event_instance(e, 'Volume', 'jplayer');
		if (instance && (radio_player_data.scripts[instance] == 'jplayer')) {
			radio_player_volume_changed(instance, 'jplayer', volume);
		}
	});

	/* bind can play debug message */
	jQuery('#'+player_id).bind(jQuery.jPlayer.event.canplay, function(e) {
		instance = radio_player_event_instance(e, 'CanPlay', 'jplayer');
		console.log('jPlayer Instance '+instance+' Can Play');
	});

	/* bind player error event to fallback scripts */
	jQuery('#'+player_id).bind(jQuery.jPlayer.event.error, function(e) {
		radio_player.jplayer_ready = false;
		instance = radio_player_event_instance(e, 'Error', 'jplayer');
		radio_player_event_handler('error', {instance:instance, script:'jplayer'});
		radio_player_player_fallback(instance, 'jplayer', 'jPlayer Error');
	});

	/* match script select dropdown value */
	if (container.find('.rp-script-select').length) {container.find('.rp-script-select').val('jplayer');}

	return radio_player_instance;
}

/* Howler Player Script */
function radio_player_howler(instance, url, format, fallback, fformat) {

	container = jQuery('#radio_container_'+instance);
	if (url == '') {url = radio_player.settings.url;}
	if (url == '') {return;}
	if (!format || (format == '')) {format = 'aac';}
	/* if (fallback == '') {fallback = radio_player.settings.fallback;} */
	if (!fallback || !fformat || (fformat == '')) {fallback = ''; fformat = '';}

	/* set sources */
	sources = new Array(); formats = new Array();
	sources[0] = url; /* if (fallback != '') {sources[1] = fallback;} */
	formats[0] = format; /* if ((fallback != '') && (fformat != '')) {formats[1] = fformat;} */

	/* set volume */
	if (container.find('.rp-volume-slider').length && container.find('.rp-volume-slider').hasClass('changed')) {
		volume = container.find('.rp-volume-slider').val();
	} else if (typeof container.attr('data-volume') != 'undefined') {
		volume = container.attr('data-volume');
	} else if (typeof radio_player_data.state.volume != 'undefined') {
		volume = radio_player_data.state.volume;
	} else {volume = radio_player.settings.volume;}
	radio_player_volume_slider(instance,volume);
	volume = parseFloat(volume / 100);

	/* intialize player */
	if (radio_player.debug) {console.log('Init Howler: '+instance+' : '+url+' : '+format+' : '+fallback+' : '+fformat+' : '+volume);}
	radio_player_instance = new Howl({
		src: sources,
		format: formats,
		html5: false,
		autoplay: false,
		preload: false,
		volume: volume,
		onload: function(e) {
			/* possible bug: maybe not always being triggered ? */
			radio_player.loading = false;
			instance = radio_player_match_instance(this, e, 'howler');
			radio_player_event_handler('loaded', {instance:instance, script:'howler'});
		},
		onplay: function(e) {
			radio_player.loading = false;
			instance = radio_player_match_instance(this, e, 'howler');
			radio_player_event_handler('playing', {instance:instance, script:'howler'});
			radio_player_pause_others(instance);
		},
		onpause: function(e) {
			instance = radio_player_match_instance(this, e, 'howler');
			radio_player_event_handler('paused', {instance:instance, script:'howler'});
		},
		onstop: function(e) {
			instance = radio_player_match_instance(this, e, 'howler');
			radio_player_event_handler('stopped', {instance:instance, script:'howler'});
		},
		onvolume: function(e) {
			instance = radio_player_match_instance(this, e, 'howler');
			if (instance && (radio_player_data.scripts[instance] == 'howler')) {
				volume = this.volume() * 100;
				if (volume > 100) {volume = 100;}
				radio_player_volume_changed(instance, 'howler', volume);
			}
		},
		onloaderror: function(id,e) {
			instance = radio_player_match_instance(this, e, 'howler');
			radio_player_event_handler('error', {instance:instance, script:'howler'});
			if (radio_player.debug) {console.log('Load Error, Howler Instance: '+instance+', Sound ID: '+id);}
			radio_player_player_fallback(instance, 'howler', 'Howler Load Error');
		},
		onplayerror: function(id,e) {
			instance = radio_player_match_instance(this, e, 'howler');
			radio_player_event_handler('error', {instance:instance, script:'howler'});
			if (radio_player.debug) {console.log('Play Error, Howler Instance: '+instance+', Sound ID: '+id);}
			radio_player_player_fallback(instance, 'howler', 'Howler Play Error');
		},
	});
	radio_player_data.players[instance] = radio_player_instance;
	radio_player_data.scripts[instance] = 'howler';

	/* match script select dropdown value */
	if (container.find('.rp-script-select').length) {container.find('.rp-script-select').val('howler');}

	return radio_player_instance;
}


/* === Document Ready Functions === */

/* --- add player event listeners --- */
var radio_player_initializing;
jQuery(document).ready(function() {

	/* --- set window unique ID --- */
	radio_player_window_guid();

	/* --- preserve window tab ID on tab reload --- */
	window.addEventListener('beforeunload', function (e) {
	    window.sessionStorage.tabId = window.tabId; return null;
	});

	/* --- prevent conflict on duplicate script load --- */
	if (radio_player_initializing) {return;}
	radio_player_initializing = true;

	/* --- preload pause image --- */
	/* src = jQuery('.rp-pause-image-preload').attr('src');	image = new Image(); image.src = src; */

	/* listen for window broadcast messages */
	if (typeof sysend != 'undefined') {
		sysend.on('radio-play', function(message) {radio_player_check_to_pause(message);} );
		/* sysend.on('radio-request', function(message) {radio_player_broadcast_action(message);} ); */
		radio_player_custom_event('rp-sysend', false);
	}

	/* --- hide all volume controls if no support (iOS) --- */
	novolumesupport = radio_player_volume_test();
	if (novolumesupport) {
		jQuery('.rp-volume-controls').each(function() {
			jQuery(this).hide();
			jQuery('.rp-volume-button').hide();
			container = jQuery(this).closest('.radio-container');
			if (container.length) {container.addClass('no-volume-controls');}
			container = jQuery(this).closest('.rp-container');
			if (container.length) {container.addClass('no-volume-controls');}
		});
		jQuery('.rp-volume-button').hide();
	}

	/* --- bind pause/play button clicks --- */
	jQuery('.rp-play-pause-button').on('click', function(e) {
		e.stopImmediatePropagation(); e.preventDefault();
		container = jQuery(this).closest('.radio-container');
		if (!container.length) {container = jQuery(this).closest('.rp-container');}
		instance = container.attr('data-instance');
		if (radio_player.debug) {console.log('Play/Pause Button Click on Instance '+instance);}

		/* maybe toggle currently playing radio */
		inst = instance;
		if (typeof radio_player_toggle_current == 'function') {
			if (radio_player.debug) {console.log('Trigger Toggle of Player. New Instance '+instance);}
			done = radio_player_toggle_current(instance);
			if (done) {return;}
		}
		instance = inst; /* <- this is a fix */
		
		/* check to pause or play */
		if (radio_player_is_loading(instance) || radio_player_is_playing(instance)) {
			if (radio_player.debug) {console.log('Trigger Pause of Player Instance '+instance);}
			radio_player_pause_instance(instance);
		} else {
			if (radio_player.debug) {console.log('Trigger Play of Player Instance '+instance);}
			instances = Object.keys(radio_player_data.players);
			jQuery('#radio_player_'+instance).addClass('loading');
			if (instances.includes(instance)) {
				radio_player_play_instance(instance);
			} else {
				radio_player_play_source(instance);
			}
		}
	});

	/* --- bind volume slider background changes --- */
	jQuery('.rp-volume-slider').on('mousemove', function() {
		container = jQuery(this).closest('.radio-container, .rp-container');
		instance = container.attr('data-instance');
		volume = parseInt(jQuery(this).val());
		radio_player_volume_slider(instance,volume);
	});

	/* --- bind volume slider changes --- */
	jQuery('.rp-volume-slider').on('change', function() {
		container = jQuery(this).closest('.radio-container, .rp-container');
		instance = container.attr('data-instance');
		if (radio_player.debug) {console.log('Volume Click Instance '+instance+': '+volume);}
		volume = parseInt(jQuery(this).val());
		if (volume == 0) {mute = true;} else {mute = false;}
		radio_player_volume_slider(instance,volume);
		radio_player_change_volume(instance,volume);
		radio_player_set_state('volume',volume);
	});

	/* ---- bind mute clicks --- */
	jQuery('.rp-mute').on('click', function() {
		container = jQuery(this).closest('.radio-container, .rp-container');
		instance = container.attr('data-instance');
		if (radio_player.debug) {console.log('Mute Click Instance '+instance);}
		if (typeof radio_player_toggle_mute == 'function') {
			done = radio_player_toggle_mute(instance); if (done) {return;}
		}
		mute = (!container.hasClass('muted'));
		radio_player_mute_unmute(instance,mute);
		radio_player_set_state('mute',mute);
	});

	/* --- bind max volume clicks --- */
	jQuery('.rp-volume-max').on('click', function() {
		container = jQuery(this).closest('.radio-container, .rp-container');
		instance = container.attr('data-instance');
		if (!container.hasClass('maxed')) {container.addClass('maxed');}
		container.find('.rp-volume-slider').val(100);
		radio_player_volume_slider(instance,100);
		radio_player_change_volume(instance,100);
		radio_player_mute_unmute(instance,false);
		radio_player_set_state('mute',false);
	});

	/* --- bind volume decrease/increase clicks --- */
	jQuery('.rp-volume-down, .rp-volume-up').on('click', function() {
		container = jQuery(this).closest('.radio-container, .rp-container');
		instance = container.attr('data-instance');
		slider = container.find('.rp-volume-slider');
		oldvolume = parseInt(slider.val());
		if (jQuery(this).hasClass('rp-volume-down')) {
			volume = oldvolume - 5; if (volume < 0) {volume = 0;} slider.val(volume);
			if (radio_player.debug) {console.log('Volume Down Instance '+instance+': '+oldvolume+' to '+volume);}
		} else if (jQuery(this).hasClass('rp-volume-up')) {
			volume = oldvolume + 5; if (volume > 100) {volume = 100;} slider.val(volume);
			if (radio_player.debug) {console.log('Volume Up Instance '+instance+': '+oldvolume+' to '+volume);}
		}
		radio_player_volume_slider(instance,volume);
		radio_player_change_volume(instance,volume);
		radio_player_set_state('volume',volume);
		/* if (volume > 0) {mute = false;} else {mute = true;}
		radio_player_mute_unmute(instance,mute);
		radio_player_set_state('mute',mute); */
	});

	/* --- detect script selection switcher change --- */
	jQuery('.rp-script-select').change(function() {
		container = jQuery(this).closest('.radio-container, .rp-container');
		instance = container.attr('data-instance');
		script = jQuery(this).val(); radio_player_switch_script(instance,script);
	});

	/* --- add player listener to cancel loading flag --- */
	document.addEventListener('rp-play', function(e) {
		instance = e.detail.instance;
		jQuery('#radio_player_'+instance).addClass('loading');
	}, false);
	document.addEventListener('rp-playing', function(e) {
		instance = e.detail.instance;
		jQuery('#radio_player_'+instance).removeClass('loading');
	}, false);
	document.addEventListener('rp-pause', function(e) {
		instance = e.detail.instance;
		jQuery('#radio_player_'+instance).removeClass('loading');
	}, false);
	document.addEventListener('rp-stop', function(e) {
		instance = e.detail.instance;
		jQuery('#radio_player_'+instance).removeClass('loading');
	}, false);

	/* --- pause on media elements player play --- */
	jQuery('audio.mejs__player').on('play', function(e) {
		if (!radio_player.settings.singular) {return;}
		jQuery('.radio-container').each(function() {
			instance = jQuery(this).attr('data-instance');
			radio_player_pause_instance(instance);
			radio_player_broadcast_playing(instance);
		});
	});
	
	/* --- pause media elements on player play --- */
	document.addEventListener('rp-play', function(e) {
		if (!radio_player.settings.singular) {return;}
		jQuery('audio.mejs__player').each(function() {
			el = jQuery(this)[0];
			if (!el.paused) {
				el.pause();
				if (radio_player.debug) {console.log( 'Paused MediaElement Audio: '+el.src);}
			}
		});
	}, false);
	
});


/* === Custom Events === */

/* --- Trigger Custom Event --- */
function radio_player_custom_event(name, detail) {
	params = {bubbles: false, cancelable: false, detail: detail}
	var event = new CustomEvent(name, params); document.dispatchEvent(event);
    if (radio_player.debug) {console.log('Radio Player Custom Event: '+name);}
}

/* --- CustomEvent support polyfill --- */
(function () {
	if (typeof window.CustomEvent === 'function') {return false;}
	function CustomEvent(event, params) {
		params = params || {bubbles: false, cancelable: false, detail: undefined};
		var evt = document.createEvent('CustomEvent');
		evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);
		return evt;
	}
	CustomEvent.prototype = window.Event.prototype;
	window.CustomEvent = CustomEvent;
})();
