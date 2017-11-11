
// A free solution to the EU Cookie Law
// https://github.com/insites/cookieconsent

/*
// Use "ready" event instead of "load" as image and ad content aren't needed for initialization
jQuery( document ).on( "ready", function() {

	var cookie_banner;

	window.cookieconsent.initialise({
		"palette": {
			"popup": {
				"background": "#000"
			},
			"button": {
				"background": "#f1d600"
			}
		},
		"theme": "edgeless",
		"dismissOnScroll": 500,
		"content": {
			"message": "By using the CLICKON site, you agree to our use of cookies and to our ",
			"link": "privacy policy",
			"href": "http://www.clickon.co/privacy/"
		}
	}, function ( popup ) {
		cookie_banner = popup;
	}, function ( err ) {
		console.error(err);
	});

	// Timeout dismiss needs to be set separately
	// Setting the initialize option does not work as documented
	setTimeout( function() {
		cookie_banner.setStatus( window.cookieconsent.status.dismiss );
		cookie_banner.close();
	}, 10000);
});
*/