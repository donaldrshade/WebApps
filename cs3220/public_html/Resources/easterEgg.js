$(document).ready(function() {
	var keys     = [];
	var konami  = '83,72,65,68,69';
	$(document)
		.keydown(
			function(e) {
				keys.push( e.keyCode );
				if ( keys.toString().indexOf( konami ) >= 0 ){
					// do something when the konami code is executed
					change_bg();
					// empty the array containing the key sequence entered by the user
					keys = [];
				}
			}
		);
	});

function change_bg() {
		alert("You found the easter egg!")
}
