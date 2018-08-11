define([
	'backbone',
	'hbs!templates/correctAnswers'
],
function( Backbone, CorrectAnswersTmpl  ) {
    'use strict';

	/* Return a ItemView class definition */
	return Backbone.Marionette.ItemView.extend({

		initialize: function() {
			console.log("initialize a CorrectAnswers ItemView");
		},
		
    	template: CorrectAnswersTmpl,

    	/* ui selector cache */
    	ui: {},

		/* Ui events hash */
		events: {},

		/* on render callback */
		onRender: function() {}
	});

});
