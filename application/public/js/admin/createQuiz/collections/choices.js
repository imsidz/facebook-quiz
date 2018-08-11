define([
	'app',
	'backbone',
		'models/choice'
],
function( App, Backbone, ChoiceModel ) {
    'use strict';

	/* Return a collection class definition */
	return Backbone.Collection.extend({
		initialize: function() {
			console.log('initialize a Choices collection');
		},
		model: ChoiceModel
	});
});
