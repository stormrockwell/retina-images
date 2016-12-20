(function(){
	var date = new Date();
	date.setTime( date.getTime() + 3600000 );
	if( 'devicePixelRatio' in window && 1 < window.devicePixelRatio ) {
		document.cookie = 'device_pixel_ratio=' + window.devicePixelRatio + ';' +  ' expires=' + date.toUTCString() +'; path=/';
		// if cookies are not blocked, reload the page
		if( -1 !== document.cookie.indexOf('device_pixel_ratio') ) {
			window.location.reload();
		}
	} else {
		document.cookie = 'device_pixel_ratio=1;' +  ' expires=' + date.toUTCString() +'; path=/';
	}
})();