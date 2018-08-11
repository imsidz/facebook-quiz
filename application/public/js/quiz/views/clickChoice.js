define([
	'backbone',
	'hbs!templates/clickChoice'
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
			var clickLink = choiceElm.attr('href');
			window.open(clickLink, "clickChoice", "width=1200px,height=800px,left=50%,top=50%");
			//choiceElm.addClass('active');
			e.preventDefault();
		},
		onBeforeRender: function(){
			var boxColors = ['red', 'yellow', 'blue', 'green', 'turquoise'];
			this.model.set('boxColor', getRandElm(boxColors));
		},
		/* on render callback */
		onRender: function() {}
	});

});
