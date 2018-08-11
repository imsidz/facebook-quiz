define([
	'backbone',
	'hbs!templates/quizProgress'
],
function( Backbone, QuizProgressTmpl  ) {
    'use strict';

	/* Return a ItemView class definition */
	return Backbone.Marionette.ItemView.extend({

		initialize: function() {
			console.log("initialize a quizProgress ItemView");
			this.model.on('change', this.render);
		},
		
    	template: QuizProgressTmpl,
        tagName: 'ul',
		className: 'quiz-progress progress-breadcrumb list-unstyled list-inline',

    	/* ui selector cache */
    	ui: {},

		/* Ui events hash */
		events: {
			'click li a' : 'navigateStage'
		},
		navigateStage: function(e){
			e.preventDefault();
			var elm = $(e.target);
			var questionId = elm.data('index');
			this.trigger('quiz:show-question', questionId);
		},
		/* on render callback */
		onRender: function() {
			console.log(this.model)
		}
	});

});
