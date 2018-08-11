define([
	'backbone',
	'appMan'
],

function( Backbone, AppMan) {
	AppMan.command.setHandler('fb', function(callback){
		if(typeof FB !== "undefined") {
			callback();
		} else {
			$('body').on('fb-api-loaded', function(){
				callback();
			});
		}
	});
	return true;
});