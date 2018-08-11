define([
	'backbone',
	'hbs!templates/choice'
],
function( Backbone, ChoiceTmpl  ) {
    'use strict';
	function getRandElm(items) {
		return items[Math.floor(Math.random()*items.length)];
	}
	/* Return a ItemView class definition */
	return Backbone.Marionette.ItemView.extend({

		initialize: function() {
			console.log("initialize a Choice ItemView");
		},
		className: 'grid-box choice-item',
    	template: ChoiceTmpl,

    	/* ui selector cache */
    	ui: {},

		/* Ui events hash */
		events: {
			'click .question-choice': 'chooseChoice'
		},
		chooseChoice: function(e){
			var choiceElm = $(e.currentTarget);
			var choiceId = choiceElm.data('choiceId');
			this.trigger('quiz:choice-chosen', choiceId);
			//this.$('.question-choice').removeClass('active');
			//choiceElm.addClass('active');
			e.preventDefault();
		},
		onBeforeRender: function(){
			/*var boxColors = ['red', 'yellow', 'blue', 'green', 'turquoise'];
			this.model.set('boxColor', getRandElm(boxColors));*/
		},
		/* on render callback */
		onRender: function() {}
	});

});
