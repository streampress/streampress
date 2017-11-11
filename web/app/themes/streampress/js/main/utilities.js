
var Stream = Stream || {};

Stream.utils = {

	bootstrap_breakpoint: function() {
		var breakpoint = 'xs';
		var width = jQuery( window ).width();

		switch( true ) {
			case ( width >= 544 && width < 768 ):
				breakpoint = 'sm';
				break;
			case ( width >= 768 &&  width < 992 ):
				breakpoint = 'md';
				break;
			case ( width >= 992 &&  width < 1200 ):
				breakpoint = 'lg';
				break;
			case ( width > 1200 ):
				breakpoint = 'xl';
		}

		return breakpoint;
	}
}
