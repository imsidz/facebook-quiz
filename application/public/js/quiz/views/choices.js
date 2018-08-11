define([
	'backbone',
		'underscore',
	'views/choice',
	'views/fbLikeChoice',
	'views/clickChoice',
	'masonry',
	'imagesLoaded'
],
function( Backbone, _, ChoiceView, FbLikeChoiceView, ClickChoiceView , Masonry, imagesLoaded) {
    'use strict';

	/* Return a ItemView class definition */
	return Backbone.Marionette.CollectionView.extend({

		initialize: function() {
			console.log("initialize a Choices CollectionView");
			this.on('childview:quiz:choice-chosen', function(choiceView, choiceId){
				this.trigger('quiz:choice-chosen', choiceId, choiceView.model);
			});
            var self = this;
			var lastAnimateClass;
			function getAnimateClass(view){
				lastAnimateClass = (lastAnimateClass == 'bounceInLeft') ? 'bounceInRight' : 'bounceInLeft';
				return lastAnimateClass;
			}
			this.on('childview:render', function(choiceView){
                var animateClass = getAnimateClass(choiceView);
				choiceView.$el.addClass('animated ' + animateClass);
                setTimeout(function() {
                    choiceView.$el.removeClass(animateClass);
                }, 1500);
			});

		},
		className: 'choices-box clearfix',
    	getChildView: function(choice) {
			switch(choice.get('type')) {
				case "fb-like":
					return FbLikeChoiceView;
					break;
				case "click":
					return ClickChoiceView;
					break;
			}
			return ChoiceView;
		},

    	/* ui selector cache */
    	ui: {},

		/* Ui events hash */
		events: {},
		
		/* on render callback */
		onRender: function() {
			var self = this,
				hasOnlyTextChoices = true;

            if(self.masonry) {
                self.masonry.destroy();
            }
			this.collection.each(function(choice) {
				if(choice.get('image')) {
					hasOnlyTextChoices = false;
				}
			});
			if(hasOnlyTextChoices) {
				self.$el.addClass('has-only-simple-choices');
			} else {
				self.$el.removeClass('has-only-simple-choices');
				setTimeout(function(){
					console.log(self.$el)
					var $container = self.$el;
					// initialize
					imagesLoaded($container, function(){
						self.masonry = new Masonry( $container[0], {
						  itemSelector: '.choice-item'
						});
					});
				}, 200);
			}
		},
		hideWrongChoices: function(){
            this.trigger('hidden:wrong-choices');
			this.collection = this.collection.clone();
			this.collection.remove(this.collection.where({correct: false}));
			this.render();
		}
	});

});
