define([
	'backbone',
	'hbs!templates/quizHeader',
	'appMan'
],
function( Backbone, QuizHeaderTmpl, AppMan ) {
    'use strict';

	/* Return a ItemView class definition */
	return Backbone.Marionette.ItemView.extend({

		initialize: function() {
			console.log("initialize a quizHeader ItemView");
			var self = this;
			AppMan.on('quiz:started', function(){
				self.$el.removeClass('quiz-header-expanded').addClass('quiz-header-collapsed');
			});
		},
		
    	template: QuizHeaderTmpl,
        tagName: 'div',
		className: 'quiz-header-inner quiz-header-expanded',

    	/* ui selector cache */
    	ui: {},

		/* Ui events hash */
		events: {},

		/* on render callback */
		onRender: function() {
			setTimeout(function(){
				if(!$('.quiz-page-content').find(':not(p, br)').length && !$('.quiz-page-content').text().replace(/[\n\t ]?/gm, '').length) {
					this.$('.quiz-page-content-row').hide();
				}
			}, 100);
		},
		templateHelpers: function(){
			var self = this;
			return {
				quizCurrentResultData: function() {
					if(!window.quizResultId) {
						return false;
					}
					var quizResult = AppMan.reqres.request('quiz:resultData', window.quizResultId);
					if(window.quizUserResultImage)
						quizResult.image = window.quizUserResultImage;
					return quizResult;
				},
				isWelcomePage: function(){
					return !AppMan.reqres.request('quiz:isStarted');
				},
				quizSharedUserId: function(){
					return window.quizSharedUserId;
				}
			}
		}
	});

});
