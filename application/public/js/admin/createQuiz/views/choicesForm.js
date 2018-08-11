define([
        'backbone',
        'hbs!templates/choicesForm',
        'appMan',
        'views/EditableCollectionCompositeView',
        'hbs!templates/choicesList',
        'views/choiceItem',
        'collections/choices',
        'views/choicesList'
    ],
    function( Backbone, ChoicesFormTmpl, AppMan, EditableCollectionCompositeView, choicesListTemplate, ChoiceItemView, ChoicesCollection, ChoicesListView) {
        'use strict';

        /* Return a ItemView class definition */
        return Backbone.Marionette.ItemView.extend({

            initialize: function() {
                console.log("initialize a ChoicesForm ItemView");
            },

            template: ChoicesFormTmpl,

            /* ui selector cache */
            ui: {
                formContainer: '.form-container',
                form: '.form',
                formResultsBox: '.form-results-box'
            },

            /* Ui events hash */
            events: {
            },

            /* on render callback */
            onRender: function() {
                var self = this;
                var choices = new ChoicesCollection(QuizData.choices);
                var choicesView = new ChoicesListView({
                    el: self.ui.form,
                    collection: choices,
                    confirmDelete: function(callback){
                        dialogs.confirm('Are you sure to delete it?', function(confirmed){
                            callback(confirmed);
                        })
                    }
                });
                choicesView.render();
                choices.on('change add remove', function(){
                    QuizData.choices = choices.toJSON();
                });
            },
            templateHelpers: function(){
                var self = this;
                return {

                };
            }
        });
    });
