define([
	'backbone',
	'hbs!templates/fbLikeChoice'
],
function( Backbone, ChoiceTmpl  ) {
    'use strict';
	function getRandElm(items) {
		return items[Math.floor(Math.random()*items.length)];
	}
	function stripSlashes(url) {
		return url.replace(/\/+$/, "");
	}
	
	/* Return a ItemView class definition */
	return Backbone.Marionette.ItemView.extend({

		initialize: function() {
			console.log("initialize a FbLikeChoice ItemView");
			var self = this;
			function fbLikeCallback(url){
				url = decodeURIComponent(stripSlashes(url));
                var urlToLike = decodeURIComponent(stripSlashes(self.model.get('data')));
				if(url === urlToLike) {
					var choiceId = self.model.get('id');
					self.trigger('quiz:choice-chosen', choiceId);
				}
			}
			AppMan.command.execute('fb', function(){
				FB.Event.subscribe('edge.create', fbLikeCallback);
			});
			this.on('destroy', function(){
				AppMan.command.execute('fb', function(){
					FB.Event.unsubscribe('edge.create', fbLikeCallback);
				});
			});
		},
		className: 'grid-box choice-item fb-like-choice',
    	template: ChoiceTmpl,

    	/* ui selector cache */
    	ui: {},

		/* Ui events hash */
		events: {
			'click .question-choice': 'clickChoiceLink'
		},
		clickChoiceLink: function(e){
			var self = this;
			self.$('.fb-like').popover('show');
			if(self.hideFbLikePopoverTimer) {
				clearTimeout(self.hideFbLikePopoverTimer);
			}
			self.hideFbLikePopoverTimer = setTimeout(function() {
				self.$('.fb-like').popover('hide');
			}, 2000);
			e.preventDefault();
		},
		onBeforeRender: function(){
			var boxColors = ['red', 'yellow', 'blue', 'green', 'turquoise'];
			this.model.set('boxColor', getRandElm(boxColors));
		},
		/* on render callback */
		onRender: function() {
			var self = this;
			AppMan.command.execute('fb', function(){
				FB.XFBML.parse(self.el);
			});
			self.$('.fb-like').popover({title: 'Like it', content: __('clickLikeToChooseThisOption'), trigger: 'manual', placement: 'left'});
		},
		onDestroy: function(){
			var self = this;
			if(self.hideFbLikePopoverTimer) {
				clearTimeout(self.hideFbLikePopoverTimer);
			}
		}
	});

});
