define([
	'backbone',
	'backbone.marionette'
],
function(Backbone, Marionette){
    'use strict';
	return Marionette.AppRouter.extend({
		/* Backbone routes hash */
		appRoutes: {
			
			'.*': 'viewQuiz'
		}
	});
});
