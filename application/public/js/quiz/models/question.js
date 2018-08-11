define([
	'backbone'
],
function( Backbone ) {
    'use strict';

	/* Return a model class definition */
	return Backbone.Model.extend({
		initialize: function() {
			console.log("initialize a Question model");
		},
		idAttribute: 'id',

		defaults: {},
		isCorrectChoice: function(choiceOrChoiceId){
			var choice;
			if(typeof choiceOrChoiceId == "string") {
				//choice id is passed
				choice = this.get('choices').get(choiceOrChoiceId);
			} else{
				choice = choiceOrChoiceId;
			}
			return choice.get('correct');
		}

    });
});
