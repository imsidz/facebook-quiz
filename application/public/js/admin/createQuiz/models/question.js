define([
	'backbone'
],
function( Backbone ) {
    'use strict';

	/* Return a model class definition */
	return Backbone.Model.extend({
		initialize: function() {
			console.log("initialize a Question model");
			if(!this.get('id')) {
				this.set('id', guid());
			}
		},
		idAttribute: 'id',

		defaults: {},

    });
});
