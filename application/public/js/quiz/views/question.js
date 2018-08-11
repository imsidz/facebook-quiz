define([
        'backbone',
        'underscore',
        'appMan',
        'views/choices',
        'hbs!templates/question'
    ],
    function( Backbone, _, AppMan, ChoicesView, QuestionTmpl  ) {
        'use strict';

        /* Return a ItemView class definition */
        return Backbone.Marionette.LayoutView.extend({

            initialize: function(options) {
                console.log('initialize a Question LayoutView');

            },

            template: QuestionTmpl,
            regions: {
                choices: '.choices-box-container'
            },

            /* ui selector cache */
            ui: {
                choicesSection: '.choices-section'
            },

            /* Ui events hash */
            events: {
                'click .skip-question-btn' : 'skipQuestion',
                'click .proceed-after-answer-response-btn' : 'nextQuestion'
            },
            skipQuestion: function(){
                this.trigger('quiz:skipped-question', this.model);
            },
            nextQuestion: function(){
                this.trigger('quiz:proceed-after-answer-response', this.model);
            },
            /* on render callback */
            onRender: function() {
                var self = this;
                setTimeout(function(){
                    if(!$('.quiz-question-description').find(':not(p, br)').length) {
                        this.$('.quiz-question-description').remove();
                    }
                }, 200);

                this.choicesView = new ChoicesView({
                    collection: this.model.get('choices')
                });
                this.choices.show(this.choicesView);
                var self = this;
                this.choicesView.on('quiz:choice-chosen', function(choiceId, choice){
                    //IF already chosen and answer response is being shown
                    if(self.isAnswerResponseShown()) {
                        return;
                    }
                    if(choice.get('correct')){
                        self.trigger('quiz:answer:correct');
                    }
                    self.trigger('quiz:answered-question', self.model.toJSON(), choiceId);
                });

            },
            templateHelpers: function () {
                var self = this;
                return {
                    totalQuestions: function(){
                        return AppMan.reqres.request('quiz:questions').length;
                    },
                    questionNumber: function(questionId) {
                        return parseInt(self.model.get('index')) + 1;
                    }
                };
            },
            showAnswerResponse: function(choiceId){
                this._isAnswerResponseShown = true;
                var isCorrect = this.model.isCorrectChoice(choiceId);
                if(isCorrect){
                    this.ui.choicesSection.addClass('right-answer');
                } else{
                    this.ui.choicesSection.addClass('wrong-answer');
                }
                this.ui.choicesSection.addClass('show-answer-response');
                this.$('.question-answer-response').addClass('animated bounceIn');
                this.hideWrongAnswers()
            },
            isAnswerResponseShown: function(){
                return this._isAnswerResponseShown;
            },
            hideWrongAnswers: function(){
                this.choicesView.hideWrongChoices();
            }
        });

    });
