/* =============================== */
/* === Radio Player Javascript === */
/* --------- Version 1.0.2 ------- */
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
		setCookie('radio_player_' + name, '', -1);
	}
}

/* === Load Audio Functions === */

/* --- check data for format/script --- */
function radio_player_check_format(data) {

	script = false; scripts = radio_player.scripts;
	url = data.url; fallback = data.fallback; format = fformat = '';
	if (typeof data.format != 'undefined') {format = data.format.toLowerCase();}
	if (typeof data.fformat != 'undefined') {fformat = data.fformat.toLowerCase();}

	/* attempt to get format from URL */
	/* TODO: add more possible formats for detection ? */
	formats = ['mp3','aac','m4a','mp4','ogg','oga','webm','rtmpa','wav','flac'];
	if (format == '') {
		if (radio_player.debug) {console.log('Detecting stream format from URL.');}
		for (i = 0; i < formats.length; i++) {
			length = formats[i].length;
			if (url.substr(-length,length) == formats[i]) {format = formats[i];}
		}
	}
	if (fformat == '') {
		if (radio_player.debug) {console.log('Detecting fallback format from URL.');}
		for (i = 0; i < formats.length; i++) {
			length = formats[i].length;
			if (fallback.substr(-length,length) == formats[i]) {fformat = formats[i];}
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
		if ((format in radio_player.formats.amplitude) && ('amplitude' in scripts)) {script = 'amplitude';}
		else if ((format in radio_player.formats.jplayer) && ('jplayer' in scripts)) {script = 'jplayer';}
		else if ((format in radio_player.formats.howler) && ('howler' in scripts)) {script = 'howler';}
		/* else if ((format in radio_player.formats.mediaelements) && ('mediaelements' in scripts)) {script = 'mediaelements';} */
		if (!script) {
			if ((fformat in radio_player.formats.amplitude) && ('amplitude' in scripts)) {script = 'amplitude';}
			else if ((fformat in radio_player.formats.jplayer) && ('jplayer' in scripts)) {script = 'jplayer';}
			else if ((fformat in radio_player.formats.howler) && ('howler' in scripts)) {script = 'howler';}
			/* else if ((fformat in radio_player.formats.mediaelements) && ('mediaelements' in scripts)) {script = 'mediaelements';} */
			if (script) {a = url; b = format; url = fallback; format = fformat; fallback = a; fformat = b;}
		}
		if (!script) {
			if ('amplitude' in scripts) {script = 'amplitude';}
			else if ('jplayer' in scripts) {script = 'jplayer';}
			else if ('howler' in scripts) {script = 'howler';}
		}
	}

	data.script = script; data.url = url; data.format = format; data.fallback = fallback; data.fformat = fformat;
	return data;
}

/* --- load default radio stream --- */
function radio_player_load_radio(start) {
	script = radio_player.settings.script; instance = radio_player_data.state.instance;
	data = radio_player.stream_data;
	player = radio_player_load_stream(script, instance, data, start);
}

/* --- load a station --- */
function radio_player_load_station(instance, station, data, start) {

	/* set channel ID for instance */
	if (!instance) {instance = radio_player_default_instance();}
	radio_player_data.types[instance] = 'station';
	channel = station; radio_player_data.channels[instance] = channel;
	/* jQuery('#radio_player_'+instance).attr('station-id', station); */
	if (radio_player.debug) {console.log('Set Station Channel ID '+channel+' on Instance '+instance);}

	data = radio_player_check_format(data); script = data.script;
	player = radio_player_load_audio(script, instance, data, start);
	if (player && start) {
		setTimeout(function() {radio_player_play_on_load(player, script, instance);}, 250);
	}
}

/* --- load a stream --- */
function radio_player_load_stream(script, instance, data, start) {

	/* set channel ID for instance */
	if (!instance) {instance = radio_player_default_instance();}
	radio_player_data.types[instance] = 'stream'; radio_player_data.channels[instance] = data;
	if (radio_player.debug) {console.log('Set Stream Data '+data+' on Instance '+instance);}

	/* load the audio stream */
	data = radio_player_check_format(data); script = data.script;
	player = radio_player_load_audio(script, instance, data, start);
	if (player && start) {radio_player_play_on_load(player, script, instance);}
}

/* --- load a file --- */
function radio_player_load_file(script, instance, data, start) {

	/* set channel ID for instance */
	if (!instance) {instance = radio_player_default_instance();}
	radio_player_data.types[instance] = 'file'; radio_player_data.channels[instance] = data;
	if (radio_player.debug) {console.log('Set File Data '+data+' on Instance '+instance);}

	/* load the audio stream */
	data = radio_player_check_format(data); script = data.script;
	player = radio_player_load_audio(script, instance, data, start);
	if (player && start) {radio_player_play_on_load(player, script, instance);}
}

/* --- load audio in player --- */
function radio_player_load_audio(script, instance, data, start) {
	if (typeof radio_player_cancel_autoresume == 'function') {radio_player_cancel_autoresume({message:'Audio Loading'});}
	url = data.url; format = data.format; fallback = data.fallback; fformat = data.fformat;
	radio_player_set_data_state(script, instance, data, start);
	loaded = radio_player_check_script(script);
	if (loaded) {
		if (radio_player.delayed_player) {clearInterval(radio_player.delayed_player);}
		/* initialize the player if script is already loaded */
		if (script == 'amplitude') {player =  radio_player_amplitude(instance, url, format, fallback, fformat);}
		else if (script == 'jplayer') {player = radio_player_jplayer(instance, url, format, fallback, fformat);}
		else if (script == 'howler') {player = radio_player_howler(instance, url, format, fallback, fformat);}
		/* else if (script == 'mediaelements') {player = radio_player_mediaelements(instance, url, format, fallback, fformat);} */
		if (player) {
			detail = {script: script, instance: instance, url: url, format: format, fallback: fallback, fformat: fformat, start: start}
			radio_player_event_handler('loading', detail);
			/* note: radio_player_play_on_load called outside this function */
		}
		return player;
	} else {
		/* delay initialization until script is loaded */
		if (radio_player.debug) {console.log('Script not ready, initializing Delayed Player');}
		radio_player.delayed_data = {'time':0, 'start':start, 'script':script, 'instance':instance, 'url':url, 'format':format, 'fallback':fallback, 'fformat':fformat};
		radio_player.delayed_player = setInterval(function() {
			radio_player.delayed_data.time++; data = radio_player.delayed_data; player = false;
			if (data.time > 10) {console.log('Script load timed out. Please try again...'); clearInterval(radio_player.delayed_player);}
			if ((data.script == 'amplitude') && (typeof window.Amplitude != 'undefined') && (typeof radio_player_amplitude != 'undefined')) {
				clearInterval(radio_player.delayed_player);
				player = radio_player_amplitude(data.instance, data.url, data.format, data.fallback, data.fformat);
			} else if ((data.script == 'jplayer') && (typeof jQuery.jPlayer != 'undefined') && (typeof radio_player_jplayer != 'undefined')) {
				clearInterval(radio_player.delayed_player);
				player = radio_player_jplayer(data.instance, data.url, data.format, data.fallback, data.fformat);
			} else if ((data.script == 'howler') && (typeof window.Howl != 'undefined') && (typeof radio_player_howler != 'undefined')) {
				clearInterval(radio_player.delayed_player);
				player = radio_player_howler(data.instance, data.url, data.format, data.fallback, data.fformat);
			} /* else if ((script == 'mediaelement') && (typeof window.???? != 'undefined') && (typeof radio_player_mediaelement != 'undefined')) {
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
				radio_player.jplayer_load = setInterval(function() {
					if (radio_player.jplayer_ready) {
						clearInterval(radio_player.jplayer_load);
						if (radio_player.debug) {console.log('jPlayer is ready.');}
						try {console.log(script+': Play'); player.jPlayer('play');
							radio_player_custom_event('rp-play', detail);
						} catch(e) {console.log(script+' error: could not play stream.'); console.log(e);}
					}
				}, 250);
			}
		}
		/* if (!jQuery('#radio_container_'+instance).hasClass('playing')) {jQuery('#radio_container_'+instance).addClass('playing');} */
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
	} else if (script == 'howler') {
		if (typeof window.Howl == 'undefined') {
			if (radio_player.debug) {console.log('Dynamically Loading Howler Player Script...');}
			el = document.createElement('script'); el.src = radio_player.scripts.howler; head.appendChild(el); loading = true;
		}
	} /* else if ( ( script == 'mediaelement') && (typeof mejs == 'undefined' ) ) {
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
	if (typeof radio_player.delayer_player != 'undefined') {clearInterval(radio_player.delayed_player);}
	if (typeof radio_player_data.failed[instance] != 'undefined') {j = radio_player_data.failed[instance].length;}
	else {radio_player_data.failed[instance] = new Array(); j = 0;}
	if (!(script in radio_player_data.failed[instance])) {radio_player_data.failed[instance][j] = script;}
	jQuery('#radio_container_'+instance).removeClass('playing').removeClass('loaded');
	radio_player_event_handler('failed', radio_player_data.data[instance]);

	/* retry different script with stored player instance data */
	newscript = false;
	if (radio_player.scripts.length) {
		for (k in radio_player.scripts) {
			if (!newscript) {
				found = false;
				for (j = 0; j < radio_player_data.failed[instance].length; j++) {
					if (radio_player_data.failed[instance][j] == k) {found = true;}
				}
				if (!found) {newscript = k;}
			}
		}
	}
	if (!newscript) {
		if (radio_player.debug) {console.log('Exhausted All Player Script Type Attempts');}
		radio_player_data.failed = new Array(); /* reset */
		/* maybe swap to fallback stream data to retry */
		if (data.fallback != '') {
			if (radio_player.debug) {console.log('Switching to Fallback Stream');}
			tmpa = data.url; data.url = data.fallback; data.fallback = tmpa;
			tmpb = data.format; data.fformat = data.format; data.fformat = tmpb;
			radio_player_load_audio(script, instance, data, data.start);
		}
	} else {
		radio_player_data.data[instance].script = newscript; data = radio_player_data.data[instance];
		if (radio_player.debug) {console.log('Trying New Player Script: '+newscript); console.log(data);}
		radio_player_load_audio(newscript, instance, data, data.start);
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
	if ((typeof radio_player_data.players[instance] != 'undefined') && (typeof radio_player_data.scripts[instance] != script)) {
		radio_player_stop_instance(instance, false);
	}
	player = radio_player_load_audio(script, instance, data, data.start);
	if (player && data.start) {radio_player_play_on_load(player, script, instance);}
}

/* === Player Functions and Event Callbacks === */

/* --- play player instance --- */
// 2.5.13: add retry cycle for missed
var radio_player_retry = {}
function radio_player_play_instance(instance) {
	radio_player.loading = true;
	player = radio_player_data.players[instance]; script = radio_player_data.scripts[instance];
	console.log(player); console.log(script);
	if (script == 'amplitude') {
		player.play(); radio_player_retry.player = player;
		radio_player_retry.cycle = setInterval(function() {
			player = radio_player_retry.player;
			if (player.getPlayerState() == 'stopped') {player.play();}
			else {clearInterval(radio_player_retry.cycle); radio_player_retry = {};}
		}, 250);
	} else if (script == 'howler') {player.play();}
	else if (script == 'jplayer') {player.jPlayer('play');}
	if (radio_player.debug) {console.log('Playing '+script+' Player Instance '+instance); console.log(radio_player_is_playing(instance));}
	radio_player_custom_event('rp-play', {player: player, script: script, instance: instance});
}

/* --- pause player instance --- */
function radio_player_pause_instance(instance) {
	radio_player.loading = false;
	player = radio_player_data.players[instance]; script = radio_player_data.scripts[instance];
	if (radio_player.debug) {console.log('Pausing '+script+' Player Instance '+instance); radio_player_is_playing(instance);}
	if (radio_player_retry.hasOwnProperty('cycle')) {clearInterval(radio_player_retry.cycle); radio_player_retry = {};}
	if ((script == 'amplitude') || (script == 'howler')) {player.pause();}
	else if (script == 'jplayer') {player.jPlayer('pause');}
	radio_player_custom_event('rp-pause', {player:player, script:script, instance: instance});
}

/* --- stop player instance --- */
function radio_player_stop_instance(instance, fadeout) {
	radio_player.loading = false;
	player = radio_player_data.players[instance]; script = radio_player_data.scripts[instance];
	if (radio_player.debug) {console.log('Stopping '+script+' Player Instance '+instance); radio_player_is_playing(instance);}
	if (radio_player_retry.hasOwnProperty('cycle')) {clearInterval(radio_player_retry.cycle); radio_player_retry = {};}
	if (fadeout) {radio_player_fade_volume(instance, fadeout, 0, 'stop');}
	else {
		if (script == 'amplitude') {
			/* ? amplitude (.min?) bug: 'stop is not a function'! ? */
			audio = player.getAudio();
			try {player.stop();} catch(e) {player.pause();}
			audio.remove();
		} else if (script == 'howler') {player.unload();}
		else if (script == 'jplayer') {player.jPlayer('stop');}
	}
	radio_player_custom_event('rp-stop', {player:player, script:script, instance: instance});
}

/* --- fade volume for player instance --- */
function radio_player_fade_volume(instance, fadetime, target, complete) {
	volume = radio_player_get_volume(instance);
	if (radio_player.debug) {console.log('Fade Instance '+instance+' Volume from '+volume+' to '+target);}
	if (target == volume) {return;}
	if (target > volume) {updown = 'up'; oldinstance = 'fadedown-'+instance;}
	else if (volume > target) {updown = 'down'; oldinstance = 'fadeup-'+instance;}
	if (typeof radio_player_data.faders[oldinstance] != 'undefined') {
		clearInterval(radio_player_data.faders[oldinstance]); radio_player_data.faders.splice(oldinstance, 1);
	}
	finstance = 'fade'+updown+'-'+instance;
	if (typeof radio_player_data.faders[finstance] != 'undefined') {clearInterval(radio_player_data.faders[finstance]);}
	player = radio_player_data.players[finstance] = radio_player_data.players[instance];
	script = radio_player_data.scripts[finstance] = radio_player_data.scripts[instance];
	steps = Math.ceil(volume / 5); steptime = Math.floor(fadetime / steps);
	radio_player_data.faders[finstance] = setInterval(function(finstance, updown, target, complete) {
		player = radio_player_data.players[finstance]; script = radio_player_data.scripts[finstance];
		volume = radio_player_get_volume(finstance);
		if (updown == 'up') {volume = volume + 5; if (volume > target) {volume = target;} }
		else if (updown == 'down') {volume = volume - 5;	if (volume < target) {volume = target;} }
		if (script == 'amplitude') {player.setVolume(volume);}
		else if (script == 'howler') {volume = parseFloat(volume / 100); player.volume(volume);}
		else if (script == 'jplayer') {volume = parseFloat(volume / 100); try {player.jPlayer('volume', volume);} catch(e) {} }
		if (radio_player.debug) {console.log('Fade '+finstance+' Volume to '+volume);}
		if (volume == target) {
			if (complete == 'pause') {radio_player_pause_instance(finstance, false);}
			else if (complete == 'stop') {radio_player_stop_instance(finstance, false);}
			radio_player_data.players.splice(finstance, 1); radio_player_data.scripts.splice(finstance, 1);
			clearInterval(radio_player_data.faders[finstance]);
		}
	}, steptime);
}

/* --- check if player is playing */
function radio_player_is_playing(instance) {
	if (!(instance in radio_player_data.players)) {return false;}
	player = radio_player_data.players[instance]; script = radio_player_data.scripts[instance];
	if (radio_player.debug) {console.log(player);}
	if (script == 'amplitude') {
		state = player.getPlayerState();
		if (state == 'playing') {playing = true;} else {playing = false;}
	} else if (script == 'howler') {
		playing = player.playing();
	} else if (script == 'jplayer') {
		/* ? possible bug: get status not working ? */
		try {playing = !player.jPlayer.status.paused;}
		catch(e) {playing = !player.data().jPlayer.status.paused;}
	}
	if (radio_player.debug) {
		if (playing) {console.log('Player Instance '+instance+' ('+script+') is playing.');}
		else {console.log('Player Instance '+instance+' ('+script+') is not playing.');}
	}
	return playing;
}

/* --- change player volume --- */
function radio_player_change_volume(instance, volume) {
	player = radio_player_data.players[instance]; script = radio_player_data.scripts[instance];
	container = jQuery('#radio_container_'+instance);
	if (volume == 100) {if (!container.hasClass('maxed')) {container.addClass('maxed');}} else {container.removeClass('maxed');}
	slider = jQuery('#radio_container_'+instance+' .rp-volume-slider');
	if (!slider.hasClass('changed')) {slider.addClass('changed');}
	if (script == 'amplitude') {
		player.setVolume(volume); newvolume = player.getVolume();
		if (radio_player.debug) {console.log('Amplitude New Volume: '+volume+' : Now '+newvolume);}
	} else if (script == 'howler') {
		volume = parseFloat(volume / 100);
		player.volume(volume); newvolume = player.volume();
		if (radio_player.debug) {console.log('Howler New Volume: '+volume+' : Now '+newvolume);}
	} else if (script == 'jplayer') {
		volume = parseFloat(volume / 100);
		/* note: this catches (browser?) error "cannot set to non-finite value" in jPlayer _html_setProperty */
		try {player.jPlayer('volume', volume); newvolume = player.jPlayer('volume');} catch(e) {}
		if (radio_player.debug) {console.log('jPlayer New Volume: '+volume+' : Now '+newvolume);}
	}
	detail = {player:player, script: script, instance: instance, volume: volume}
	radio_player_custom_event('rp-volume', detail);
}

/* --- get player volume --- */
function radio_player_get_volume(instance) {
	player = radio_player_data.players[instance]; script = radio_player_data.scripts[instance];
	if (script == 'amplitude') {volume = player.getVolume();}
	else if (script == 'howler') {volume = (player.volume() * 100);}
	else if (script == 'jplayer') {volume = (player.jPlayer('volume') * 100);}
	return volume;
}

/* --- set slider volume with background div width fix --- */
function radio_player_volume_slider(instance, volume) {
	container = jQuery('#radio_container_'+instance);
	slider = jQuery('#radio_container_'+instance+' .rp-volume-slider');
	sliderbg = jQuery('#radio_container_'+instance+' .rp-volume-slider-bg');
	thumb = jQuery('#radio_container_'+instance+' .rp-volume-thumb');
	if (slider.length) {
		sliderbg.hide(); slider.val(volume); swidth = slider.width();
		thumb.show(); twidth = thumb.width(); thumb.hide();
		mwidth = parseInt(sliderbg.css('margin-left').replace('px',''));
		bgwidth = parseInt((swidth - twidth) * (volume / 100)) - mwidth;
		sliderbg.attr('style', 'width: '+bgwidth+'px !important;').show();
		if (radio_player.debug) {
			newwidth = parseInt(sliderbg.css('width')); console.log('Volume Slider BF: Slider '+swidth+' : Thumb '+twidth+' : Margin '+mwidth+' : BG '+bgwidth+' : Now '+newwidth);
		}
		if (volume == 100) {container.addClass('maxed');} else {container.removeClass('maxed');}
	}
}

/* --- mute or unmute a player -- */
function radio_player_mute_unmute(instance, mute) {
	player = radio_player_data.players[instance]; script = radio_player_data.scripts[instance];
	container = jQuery('#radio_container_'+instance);
	if (mute) {container.addClass('muted'); eventprefix = '';}
	else {container.removeClass('muted'); eventprefix = 'un';}
	if (script == 'amplitude') {
		volume = player.getVolume();
		if (mute) {
			container.attr('pre-muted-volume', volume);
			if (radio_player.debug) {console.log('Set Pre-muted Volume: '+volume);}
			player.setVolume(0);}
		else if (volume == 0) {
			volume = container.attr('pre-muted-volume');
			if (volume != 'undefined') {
				if (radio_player.debug) {console.log('Get Pre-muted Volume: '+volume);}
				try {player.setVolume(volume);} catch(e) {}
			}
		}
	} else if (script == 'howler') {
		if (mute) {player.mute(true);} else {player.mute(false);}
	} else if (script == 'jplayer') {
		if (mute) {player.jPlayer('mute');} else {player.jPlayer('unmute');}
	}
	if (typeof window.top.current_radio == 'undefined') {radio_player_set_state('mute', mute);}

	detail = {player:player, instance:instance, script:script, mute:mute}
	radio_player_custom_event('rp-'+eventprefix+'muted', detail);
}

/* --- get page default instance --- */
function radio_player_default_instance() {
	/* ? TODO: maybe also match with default script ? */
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
	if (radio_player.settings.singular && radio_player_data.players.length) {
		if (radio_player.debug) {console.log(radio_player_data.players);}
		for (i in radio_player_data.players) {
			if (i != instance) {
				/* TODO: if the stream is the same, maybe swap-fade players ? */
				if (radio_player.debug) {console.log('Pausing Player Instance '+i);}
				/* temporarily disabled as conflicting with multiple instances usage */
				/* player = radio_player_data.players[i]; script = radio_player_data.scripts[i];
				if ((script == 'amplitude') || (script == 'howler')) {player.pause();}
				else if (script == 'jplayer') {player.jPlayer('pause');} */
			}
		}
	}
	/* broadcast playing message to other windows */
	radio_player_broadcast_playing(instance);
}

/* --- get instance from event target --- */
function radio_player_event_instance(e, name, script) {
	instance = false;
	if (radio_player.debug) {
		console.log(script+' Player Event: '+name);
		if ((script == 'jplayer') && (typeof e.jPlayer.error != 'undefined')) {console.log(e.jPlayer.error);}
		else {console.log(e);}
	}
	if ((typeof e.target != 'undefined') && (e.target != null)) {
		if (radio_player.debug) {console.log('Event Target'); console.log(e.target);}
		if (e.target.hasAttribute('instance-id')) {
			instance = e.target.getAttribute('instance-id');
		} else if (jQuery(e.target)) {
			instance = parseInt(jQuery(e.target).attr('id').replace('radio_player_',''));
		}
	}
	if (instance && radio_player.debug) {console.log('Event Player Instance: '+instance);}
	return instance;
}

/* --- match instance from player object --- */
function radio_player_match_instance(obj, e, script) {
	instance = false;
	/* if (radio_player.debug) {console.log(script+' Player Event'); console.log(e);} */
	for (i = 0; i < radio_player_data.players.length; i++) {
		if (obj == radio_player_data.players[i]) {instance = i;}
	}
	return instance;
}

/* --- player event handler --- */
function radio_player_event_handler(action, detail) {
	instance = detail.instance; script = detail.script;
	if (radio_player.debug) {console.log(script+' Player Instance '+instance+' : '+action);}
	container = jQuery('#radio_container_'+instance);
	if (action == 'loading') {container.removeClass('loaded playing stopped paused error');}
	else if (action == 'loaded') {container.removeClass('loading error');}
	else if (action == 'playing') {container.removeClass('paused stopped error'); radio_player_set_state('playing', true);}
	else if (action == 'paused') {container.removeClass('playing stopped'); radio_player_set_state('playing',false);}
	else if (action == 'stopped') {container.removeClass('playing paused loaded loading'); radio_player_set_state('playing',false);}
	else if (action == 'error') {container.removeClass('playing paused loaded loading');}
	if (!container.hasClass(action)) {container.addClass(action);}
	radio_player_custom_event('rp-'+action, detail);
}

/* --- player volume change --- */
function radio_player_player_volume(instance, script, volume) {
	if (radio_player.debug) {console.log(script+' Player Instance '+instance+' : Change Volume to '+volume);}
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
		/* radio_player_data.state.transition = false; */
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
		if ((volume != null) && (data != null)) {
			radio_player_volume_slider(data.instance, volume);
		}
		radio_player_data.state.checked = true;
	}
}

/* --- store user player state --- */
function radio_player_set_state(key, value) {
	changed = false;
	if ((key == 'playing') && (value != radio_player_data.state.playing)) {
		radio_player_cookie.set('playing', value, 7);
		radio_player_data.state.playing = value;	changed = true;
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
	url = data.url; format = data.format; fallback = data.fallback; fformat = data.fformat; /* 2.5.6: fix to fallback format */
	if (typeof radio_player_data.data[instance] != 'undefined') {
		cdata = radio_player_data.data[instance];
		if ( (cdata.script != script) || (cdata.url != url) || (cdata.format != format) || (cdata.fallback != fallback) || (cdata.fformat != fformat) || (cdata.start != start) ) {
			radio_player_data.failed[instance] = new Array();
		}
		radio_player.previous_data = data;
	}
	data = {'script': script, 'instance': instance, 'url': url, 'format': format, 'fallback': fallback, 'fformat': fformat, 'start': start};
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
			if (state.volume) {volume = state.volume;} else {volume = '';}
			if (state.mute) {mute = '1';} else {mute = '0';}
			timestamp = Math.floor( (new Date()).getTime() / 1000 );
			url = radio_player.settings.ajaxurl+'?action=radio_player_state';
			/* ? TODO: instance ? */
			url += '&playing='+playing+'&station='+station+'&volume='+volume+'&mute='+mute+'&timestamp='+timestamp;
			if (radio_player.debug) {url += '&debug=1';}
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
	return window.tabId;
}

/* --- broadcast pause all message to other windows --- */
function radio_player_broadcast_playing(instance) {
	if (typeof sysend != 'undefined') {
		if (typeof radio_player_data.types[instance] == 'undefined') {type = 0;}
		else {type = radio_player_data.types[instance];}
		if (typeof radio_player_data.channels[instance] == 'undefined') {channel = 0;}
		else {
			channel = radio_player_data.channels[instance];
			if (typeof channel == 'object') {console.log('Channel Data:'); console.log(channel);}
			data = channel;
		}
		windowid = radio_player_window_guid();
		if (!instance) {instance = 0;}
		message = windowid+'::'+instance+'::'+type+'::'+data;
		console.log('Broadcast Message: '+message);
		sysend.broadcast('radio-play', {message: message});
	}
}

/* --- check to pause player on receiving message --- */
function radio_player_check_to_pause(broadcast) {
	console.log('Received Message: '); console.log(broadcast);
	if (!radio_player.settings.singular) {return;}
	parts = broadcast.message.split('::');
	winid = parseInt(parts[0]); var radio_player_id = parseInt(parts[1]);
	type = parseInt(parts[2]); data = parseInt(parts[3]);
	windowid = radio_player_window_guid();
	if (radio_player_data.players.length) {
		for (i = 0; i < radio_player_data.players.length; i++) {
			if ( (winid != windowid) || ((winid == windowid) && (radio_player_id != instance)) ) {
				/* TODO: if the station is the same, swap/fade player volumes gracefully? */
				console.log('Pausing Window '+windowid+' Instance '+instance);
				radio_player_pause_instance(instance);
			}
		}
	}
	if (data != '0') {
		// TODO: get channel data ?
	}
}

/* --- send radio broadcast request --- */
function radio_player_broadcast_request(windowid, instance, action, data) {
	message = windowid+'::'+instance+'::'+action+'::'+data;
	console.log('Broadcast Message: '+message);
	sysend.broadcast('radio-request', {message: message});
}

/* --- respond to broadcast with radio action --- */
function radio_player_broadcast_action(broadcast) {
	parts = broadcast.message.split('::');
	winid = parseInt(parts[0]); parseInt(instance = parts[1]);
	action = parts[2]; data = parseInt(parts[3]);
	windowid = radio_player_window_guid();
	if (windowid != winid) {return;}
	if (instance in radio_player_data.players) {
		if (action == 'play') {radio_player_play_instance(instance);}
		else if (action == 'pause') {radio_player_pause_instance(instance);}
		else if (action == 'stop') {radio_player_stop_instance(instance, false);}
		else if (action == 'volume') {radio_player_change_volume(instance, data);}
		else if (action == 'mute') {
			if (data == 1) {mute = true;} else {mute = false;}
			radio_player_mute_unmute(instance, mute);
		}
	}
}


/* === Audio Player Scripts === */
/* Amplitude Player Script */
function radio_player_amplitude(instance, url, format, fallback, fformat) {

	player_id = 'radio_player_'+instance;
	container_id = 'radio_container_'+instance;
	if (url == '') {url = radio_player.settings.url;}
	if (url == '') {return;}
	if (!format || (format == '')) {format = 'aac';}
	if (fallback == '') {fallback = radio_player.settings.fallback;}
	if (!fallback || !fformat || (fformat == '')) {fallback = ''; fformat = '';}

	/* set song streams */
	songs = new Array();
	songs[0] = {'name': '',	'artist': '', 'album': '', 'url': url, 'cover_art_url': '',	'live': true};
	/* if ('' != fallback) {songs[1] = {'name': '', 'artist': '', 'album': '', 'url': fallback, 'cover_art_url': '', 'live': true};} */

	/* set volume */
	if (jQuery('#'+container_id+' .rp-volume-slider').hasClass('changed')) {
		volume = jQuery('#'+container_id+' .rp-volume-slider').val();
	} else if (typeof radio_player_data.state.volume != 'undefined') {volume = radio_player_data.state.volume;}
	else {volume = radio_player.settings.volume;}
	radio_player_volume_slider(instance, volume);
	if (radio_player.debug) {console.log('Amplitude init Volume: '+volume);}

	/* initialize player */
	if (radio_player.debug) {console.log('Init Amplitude: '+instance+' : '+url+' : '+format+' : '+fallback+' : '+fformat);}
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

		channel = radio_player_data.channels[instance];
		if (channel) {radio_player_set_state('channel', channel);}
		station = jQuery('#radio_player_'+instance).attr('station-id');
		if (station) {radio_player_set_state('station', station);}
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
			radio_player_player_volume(instance, 'amplitude', volume);
		}
	}, false);
	
	/* bind error event (as event not being passed in callback) */
	audio.addEventListener('error', function(e) {
		instance = radio_player_event_instance(e, 'Error', 'amplitude');
		if (radio_player.debug) {console.log(e);}
		radio_player_event_handler('error', {instance:instance, script:'amplitude'});
		radio_player_player_fallback(instance, 'amplitude', 'Amplitude Error');
	}, false);
	
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

	/* match script select dropdown value */
	if (jQuery('#'+container_id+' .rp-script-select').length) {
		jQuery('#'+container_id+' .rp-script-select').val('amplitude');
	}

	return radio_player_instance;
}

/* jPlayer Player Script */

/* Howler Player Script */
function radio_player_howler(instance, url, format, fallback, fformat) {

	player_id = 'radio_player_'+instance;
	container_id = 'radio_container_'+instance;
	if (url == '') {url = radio_player.settings.url;}
	if (url == '') {return;}
	if (!format || (format == '')) {format = 'aac';}
	if (fallback == '') {fallback = radio_player.settings.fallback;}
	if (!fallback || !fformat || (fformat == '')) {fallback = ''; fformat = '';}

	/* set sources */
	sources = new Array(); formats = new Array();
	sources[0] = url; /* if (fallback != '') {sources[1] = fallback;} */
	formats[0] = format; /* if ((fallback != '') && (fformat != '')) {formats[1] = fformat;} */

	/* set volume */
	if (jQuery('#'+container_id+' .rp-volume-slider').hasClass('changed')) {
		volume = jQuery('#'+container_id+' .rp-volume-slider').val();
	} else if (typeof radio_player_data.state.volume != 'undefined') {volume = radio_player_data.state.volume;}
	else {volume = radio_player.settings.volume;}
	radio_player_volume_slider(instance, volume);
	volume = parseFloat(volume / 100);
	if (radio_player.debug) {console.log('Howler init Volume: '+volume);}

	/* intialize player */
	if (radio_player.debug) {console.log('Init Howler: '+instance+' : '+url+' : '+format+' : '+fallback+' : '+fformat);}
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

			channel = radio_player_data.channels[instance];
			if (channel) {radio_player_set_state('channel', channel);}
			station = jQuery('#radio_player_'+instance).attr('station-id');
			if (station) {radio_player_set_state('station', station);}
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
				radio_player_player_volume(instance, 'howler', volume);
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
	if (jQuery('#'+container_id+' .rp-script-select').length) {
		jQuery('#'+container_id+' .rp-script-select').val('howler');
	}

	return radio_player_instance;
}

/* jPlayer Player Script */
function radio_player_jplayer(instance, url, format, fallback, fformat) {

	player_id = 'radio_player_'+instance;
	container_id = 'radio_container_'+instance;
	if (url == '') {url = radio_player.settings.url;}
	if (url == '') {return;}
	if (!format || (format == '') || (format == 'aac')) {format = 'm4a';}
	if (fallback == '') {fallback = radio_player.settings.fallback;}
	if (!fallback || !fformat || (fformat == '')) {fallback = ''; fformat = '';}
	if (fformat == 'aac') {fformat = 'm4a';}

	/* set volume */
	if (jQuery('#'+container_id+' .rp-volume-slider').hasClass('changed')) {
		volume = jQuery('#'+container_id+' .rp-volume-slider').val();
	} else if (typeof radio_player_data.state.volume != 'undefined') {volume = radio_player_data.state.volume;}
	else {volume = radio_player.settings.volume;}
	radio_player_volume_slider(instance, volume);
	volume = parseFloat(volume / 100);
	if (radio_player.debug) {console.log('jPlayer init Volume: '+volume);}

	media = {}; /* media.title = ''; */ media[format] = url; supplied = format;
	/* if (fallback && fformat) {media[fformat] = fallback; supplied += ', '+fformat;} */
	radio_player.jplayer_media = media;
	console.log(radio_player.jplayer_media);
	radio_player.jplayer_ready = false;

	/* load jplayer */
	if (radio_player.debug) {console.log('Init jPlayer: '+instance+' : '+url+' : '+format+' : '+fallback+' : '+fformat);}
	radio_player_instance = jQuery('#'+player_id).jPlayer({
		ready: function () {
			console.log('jPlayer Ready.');
			console.log(radio_player.jplayer_media);
			jQuery(this).jPlayer('setMedia', radio_player.jplayer_media);
			radio_player.jplayer_ready = true;
		},
		supplied: supplied,
		cssSelectorAncestor: '#'+container_id,
		swfPath: radio_player.settings.swf_path,
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

	/* bind load event */
	jQuery('#'+player_id).bind(jQuery.jPlayer.event.load, function(e) {
		radio_player.loading = false;
		instance = radio_player_event_instance(e, 'Loaded', 'jplayer');
		radio_player_event_handler('loaded', {instance:instance, script:'jplayer'});

		channel = radio_player_data.channels[instance];
		if (channel) {radio_player_set_state('channel', channel);}
		/* station = jQuery('#radio_player_'+instance).attr('station-id');
		if (station) {radio_player_set_state('station', station);} */
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
			radio_player_player_volume(instance, 'jplayer', volume);
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
	if (jQuery('#'+container_id+' .rp-script-select').length) {
		jQuery('#'+container_id+' .rp-script-select').val('jplayer');
	}

	return radio_player_instance;
}


/* === Document Ready Functions === */

/* --- add events after document loaded --- */
jQuery(document).ready(function() {

	/* set window unique ID */
	radio_player_window_guid();

	/* preserve window tab ID on tab reload */
	window.addEventListener('beforeunload', function (e) {
	    window.sessionStorage.tabId = window.tabId; return null;
	});

	/* listen for window broadcast messages */
	if (typeof sysend != 'undefined') {
		sysend.on('radio-play', function(message) {radio_player_check_to_pause(message);} );
		sysend.on('radio-request', function(message) {radio_player_broadcast_action(message);} );
		radio_player_custom_event('rp-sysend', false);
	}
});

/* === Pageload Functions === */

jQuery(document).ready(function() {

	/* --- hide all volume controls if no support (iOS) --- */
	novolumesupport = radio_player_volume_test();
	if (novolumesupport) {
		jQuery('.rp-volume-controls').each(function() {
			jQuery(this).hide();
			container = jQuery(this).closest('.radio-container');
			container.addClass('no-volume-controls');
			container.find('.rp-play-pause-button-bg').css('margin-right','0');
		});
	}

	/* --- bind pause/play button clicks --- */
	jQuery('.rp-play-pause-button').on('click', function() {
		container = jQuery(this).parents('.radio-container');
		instance = container.attr('id').replace('radio_container_','');
		/* radio_player.debug = true; */
		if (radio_player.debug) {console.log('Play/Pause Button Click:'); console.log(jQuery(this));}
		if (radio_player_is_playing(instance)) {
			if (radio_player.debug) {console.log('Trigger Pause of Player Instance '+instance);}
			radio_player_pause_instance(instance);
		} else {
			/* maybe toggle currently playing radio */
			inst = instance;
			if (typeof radio_player_toggle_current == 'function') {
				if (radio_player.debug) {console.log('Trigger Toggle of Player. New Instance '+instance);}
				done = radio_player_toggle_current(instance);
				if (done) {return;}
			}
			instance = inst; /* <- this is a fix */
			if (radio_player.debug) {console.log('Trigger Play of Player Instance '+instance);}
			if (instance in radio_player_data.players) {
				radio_player_play_instance(instance);
			} else {
				source = container.attr('data-href');
				if (source != '') {
					/* play the specified URL */
					format = container.attr('data-format');
					fallback = container.attr('data-fallback');
					fformat = container.attr('data-fformat');
					script = radio_player.settings.script;
					data = {url: source, format: format, fallback: '', fformat: ''};
					radio_player_load_file(script, instance, data, true);
				} else {
					/* play default radio stream */
					if (radio_player.debug) {console.log('Stream Data:'); console.log(radio_player.stream_data);}
					data = radio_player.stream_data; script = radio_player.settings.script;
					radio_player_load_stream(script, instance, data, true);
				}
			}
		}
	});

	/* --- bind volume slider background changes --- */
	jQuery('.rp-volume-slider').on('mousemove', function() {
		container = jQuery(this).parents('.radio-container')
		instance = container.attr('id').replace('radio_container_','');
		volume = parseInt(jQuery(this).val());
		radio_player_volume_slider(instance, volume);
	});

	/* --- bind volume slider changes --- */
	jQuery('.rp-volume-slider').on('change', function() {
		container = jQuery(this).parents('.radio-container')
		instance = container.attr('id').replace('radio_container_','');
		volume = parseInt(jQuery(this).val());
		if (volume == 0) {mute = true;} else {mute = false;}
		radio_player_volume_slider(instance, volume);
		if (typeof radio_player_data.players[instance] != 'undefined') {
			if (radio_player.debug) {console.log('Volume Click Change: '+volume);}
			radio_player_change_volume(instance, volume);
			if (typeof window.top.current_radio != 'object') {radio_player_set_state('volume', volume);}
		}
		if (typeof radio_player_sync_volume == 'function') {radio_player_sync_volume(instance, volume, mute);}
	});

	/* ---- bind mute clicks --- */
	jQuery('.rp-mute').on('click', function() {
		container = jQuery(this).parents('.radio-container');
		instance = container.attr('id').replace('radio_container_','');
		if (radio_player.debug) {console.log('mute click '+instance);}
		if (container.hasClass('muted')) {mute = false;} else {mute = true;}
		if (typeof radio_player_data.players[instance] != 'undefined') {
			if (radio_player.debug) {console.log('Mute/Unmute Player '+instance);}
			radio_player_mute_unmute(instance, mute);
			if (typeof window.top.current_radio != 'object') {radio_player_set_state('mute', mute);}
		} else {
			if (mute) {container.addClass('muted').removeClass('maxed');} else {container.removeClass('muted');}
		}
		if (typeof radio_player_sync_volume == 'function') {radio_player_sync_volume(instance, null, mute);}
	});

	/* --- bind max volume clicks --- */
	jQuery('.rp-volume-max').on('click', function() {
		container = jQuery(this).parents('.radio-container');
		instance = container.attr('id').replace('radio_container_','');
		console.log('max volume click '+instance);
		if (!container.hasClass('maxed')) {container.addClass('maxed');}
		container.find('.rp-volume-slider').val(100);
		radio_player_volume_slider(instance, 100);
		radio_player_change_volume(instance, 100);
		if (typeof window.top.current_radio != 'object') {radio_player_set_state('mute', false);}
		if (typeof radio_player_sync_volume == 'function') {radio_player_sync_volume(instance, 100, 0);}
	});

	/* --- bind volume decrease/increase clicks --- */
	jQuery('.rp-volume-down, .rp-volume-up').on('click', function() {
		container = jQuery(this).parents('.radio-container');
		instance = container.attr('id').replace('radio_container_','');
		slider = jQuery('#radio_container_'+instance+' .rp-volume-slider');
		oldvolume = parseInt(slider.val());
		if (jQuery(this).hasClass('rp-volume-down')) {
			volume = oldvolume - 5; if (volume < 0) {volume = 0;} slider.val(volume);
			if (radio_player.debug) {console.log('Volume Down: '+oldvolume+' to '+volume);}
		} else if (jQuery(this).hasClass('rp-volume-up')) {
			volume = oldvolume + 5; if (volume > 100) {volume = 100;} slider.val(volume);
			if (radio_player.debug) {console.log('Volume Up: '+oldvolume+' to '+volume);}
		}
		radio_player_volume_slider(instance, volume);
		if (volume > 0) {mute = false;} else {mute = true;}
		if (typeof radio_player_data.players[instance] != 'undefined') {radio_player_change_volume(instance, volume);}
		if (typeof window.top.current_radio != 'object') {radio_player_set_state('volume', volume); radio_player_set_state('mute', mute);}
		if (typeof radio_player_sync_volume == 'function') {radio_player_sync_volume(instance, volume, mute);}
	});

	/* --- detect script selection switcher change --- */
	jQuery('.rp-script-select').change(function() {
		instance = jQuery(this).parents('.radio-container').attr('id').replace('radio_container_','');
		script = jQuery(this).val(); radio_player_switch_script(instance, script);
	});

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
