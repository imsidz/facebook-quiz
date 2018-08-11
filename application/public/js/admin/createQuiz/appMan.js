define([
	'backbone',
	'backbone.marionette'
],
function( Backbone ) {
    'use strict';

	var Appman = Backbone.Marionette.Controller.extend({
		initialize: function( options ) {
			console.log("initialize the Appman");

			// create a pub sub
			//Pubsub is built in to the Controller

			//create a req/res
			this.reqres = new Backbone.Wreqr.RequestResponse();

			// create commands
			this.command = new Backbone.Wreqr.Commands();
		}
	});
	window.AppMan = window.AppMan || new Appman();
	return window.AppMan;
});