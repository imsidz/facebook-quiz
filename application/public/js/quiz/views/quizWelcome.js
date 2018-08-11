define([
	'backbone',
	'hbs!templates/quizWelcome',
	'appMan'
],
function( Backbone, quizWelcomeTmpl, AppMan  ) {
    'use strict';

	/* Return a ItemView class definition */
	return Backbone.Marionette.ItemView.extend({

		initialize: function() {
			console.log("initialize a quizWelcome ItemView");
			AppMan.on('quiz:start', function(){
				(typeof ga == "function") && ga('send', 'event', 'quiz', 'start');
			});
		},
		className: 'quiz-welcome',
    	template: quizWelcomeTmpl,

    	/* ui selector cache */
    	ui: {
			startBtn: '.start-quiz-btn'
		},

		/* Ui events hash */
		events: {
			'click .start-quiz-btn' : 'startQuiz'
		},
		startQuiz: function(){
			AppMan.trigger('quiz:start');
		},
		/* on render callback */
		onRender: function() {
		},
		templateHelpers: function(){
			return {
				x : function(){

				},
				debug: function(optionalValue) {
					console.log(this.quizConfig());
					console.log("Current Context");
					console.log("====================");
					console.log(this);

					if (optionalValue) {
						console.log("Value");
						console.log("====================");
						console.log(optionalValue);
					}
					return true;
				},
				startQuizText: function () {
					var startQuizText = __("startQuiz");
					if(this.settings.hasOwnProperty('customStartQuizText') && this.settings.customStartQuizText.trim().length > 0)
						startQuizText = this.settings.customStartQuizText;
					return startQuizText;
				}
			}
		}
	});

});
