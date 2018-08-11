define([
	'backbone',
	'hbs!templates/tabLink'
],
function( Backbone, TabLinkTmpl  ) {
    'use strict';

	/* Return a ItemView class definition */
	return Backbone.Marionette.ItemView.extend({

		initialize: function() {
			console.log("initialize a Answer ItemView");
		},
		
    	template: TabLinkTmpl,

    	/* ui selector cache */
    	ui: {},

		/* Ui events hash */
		events: {},

		/* on render callback */
		onRender: function() {}
	});

});
