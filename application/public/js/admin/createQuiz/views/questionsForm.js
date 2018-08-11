define([
        'backbone',
        'hbs!templates/questionsForm',
        'appMan',
        'views/EditableCollectionCompositeView',
        'hbs!templates/questionsList',
        'views/questionItem',
        'collections/questions',
        'views/questionsList'
    ],
    function( Backbone, QuestionsFormTmpl, AppMan, EditableCollectionCompositeView, questionsListTemplate, QuestionItemView, QuestionsCollection, QuestionsListView) {
        'use strict';

        /* Return a ItemView class definition */
        return Backbone.Marionette.ItemView.extend({

            initialize: function() {
                console.log("initialize a QuestionsForm ItemView");
            },

            template: QuestionsFormTmpl,

            /* ui selector cache */
            ui: {
                formContainer: '.form-container',
                form: '.form',
                formResultsBox: '.form-results-box'
            },

            /* Ui events hash */
            events: {
            },

            onBeforeRender: function(){
                /*To update favouredResults enum of chices schema dynamically after adding results*/
                this.updateResultsForChoices();
            },
            /* on render callback */
            onRender: function() {
                var self = this;
                var questions = new QuestionsCollection(QuizData.questions);
                var questionsView = new QuestionsListView({
                    el: self.ui.form,
                    collection: questions,
                    confirmDelete: function(callback){
                        dialogs.confirm('Are you sure to delete it?', function(confirmed){
                            callback(confirmed);
                        })
                    }
                });
                questionsView.render();
                questions.on('change add remove', function(){
                    QuizData.questions = questions.toJSON();
                });
            },
            templateHelpers: function(){
                var self = this;
                return {

                };
            },
            updateResultsForChoices: function() {
                var resultItems = [];
                for(var i in QuizData.results) {
                    resultItems.push(QuizData.results[i].id);
                }
                if(Schemas.questionSchema.choices.items.properties.favoursResult) {
                    Schemas.questionSchema.choices.items.properties.favoursResult.items.properties.result.enum = resultItems;
                    origQuestionSchema.choices.items.properties.favoursResult.items.properties.result.enum = resultItems;
                }
            }
        });
    });
