/* --------------------- */
/* Radio Station ScriptS */
/* --------------------- */

/* Scrolling Function */
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

/* Debounce Delay Callback */
var radio_resize_debounce = (function () {
	var debounce_timers = {};
	return function (callback, ms, uniqueId) {
		if (!uniqueId) {uniqueId = "nonuniqueid";}
		if (debounce_timers[uniqueId]) {clearTimeout (debounce_timers[uniqueId]);}
		debounce_timers[uniqueId] = setTimeout(callback, ms);
	};
})();

