define([
	'backbone',
	'underscore',
	'appMan'
],
function( Backbone, _, AppMan) {
    'use strict';
	return Backbone.Marionette.Controller.extend({
		initialize: function(options){
			this.bindEvents();
			this.quiz = options.quiz;
			this.userQuestionChoices = {};
		},
		bindEvents : function(){
			var self = this;
			AppMan.on('quiz:answered-question', function(question, choiceId){
				self.saveAnswer(question, choiceId);
			});
		},
		saveAnswer: function(question, choiceId){
			this.userQuestionChoices[question.index] = choiceId;
		},
		getUserChoice: function(questionIndex) {
			return this.userQuestionChoices[questionIndex];
		},
		getUserChoiceById: function(questionId){
			var questions = this.quiz.get('questions');
			var question = _.findWhere(questions, {id: questionId});
			return this.getUserChoice(question.index);
		},
		prepareResult: function(){
			var self = this;
			var isScoreBased;
            var quizType = self.quiz.get('type');
			if(quizType == 'scoreBased') {
				isScoreBased = true;
			}else {
				isScoreBased = false;
			}
            if(quizType == 'random') {
                return self._getRandomResult();
            }
			var resultScores = {},
				userChoice,
				questions = self.quiz.get('questions'),
				score = 0;
			_.each(questions, function(question) {
				userChoice = self.userQuestionChoices[question.index];
				question.choices.each(function(choice){
					if(choice.get('id') == userChoice) {
						if(isScoreBased) {
							if(choice.get('correct') == true) {
								score++;
							}
						} else {
							var favoursResults = choice.get('favoursResult');
							if(typeof favoursResults == "string") {
								//Old version format - supports only one favoured result
								favoursResults = [{
									result : choice.get('favoursResult'),
									weightage: parseInt(choice.get('favouredResultWeightage'))
								}];
							}
							for(var i in favoursResults) {
								if(!resultScores[favoursResults[i].result]){
									resultScores[favoursResults[i].result] = 0;
								}
								resultScores[favoursResults[i].result] += parseInt(favoursResults[i].weightage);
							}
						}
					}
				});
			});
			var result,
				resultScore = 0;
			if(!isScoreBased) {
				for(var i in resultScores) {
					if(resultScores[i] >= resultScore) {
						resultScore = resultScores[i];
						result = i;
					}
				}
				_.each(self.quiz.get('results'), function(res){
					if(res.id == result) {
						result = res;
					}
				});
			} else {
				var curMinScore = 0,
					scorePercent = (score/AppMan.reqres.request('quiz:totalQuestions')) * 100;
                AppMan.reqres.setHandler('percentScore', function() {
                    return scorePercent;
                })
				_.each(self.quiz.get('results'), function(res){
					res.minScore = parseInt(res.minScore);
					if(scorePercent >= res.minScore) {
						if(res.minScore >= curMinScore) {
						   result = res;
						   curMinScore = res.minScore;
						}
					}
				});
			}
			return result;
		},
        _getRandomResult: function() {
            function randomFrom(array) {
                return array[Math.floor(Math.random() * array.length)];
            }
            var results = this.quiz.get('results');
            return randomFrom(results);
        }
	});
});