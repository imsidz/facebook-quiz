define([
        'backbone',
        'hbs!templates/resultsForm',
        'appMan',
        'views/EditableCollectionCompositeView',
        'hbs!templates/resultsList',
        'views/resultItem',
        'collections/results',
        'views/resultsList'
    ],
    function( Backbone, ResultsFormTmpl, AppMan, EditableCollectionCompositeView, resultsListTemplate, ResultItemView, ResultsCollection, ResultsListView) {
        'use strict';

        /* Return a ItemView class definition */
        return Backbone.Marionette.ItemView.extend({

            initialize: function() {
                console.log("initialize a ResultsForm ItemView");
            },

            template: ResultsFormTmpl,

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
                var results = new ResultsCollection(QuizData.results);
                var resultsView = new ResultsListView({
                    el: self.ui.form,
                    collection: results,
                    confirmDelete: function(callback){
                        dialogs.confirm('Are you sure to delete it?', function(confirmed){
                            callback(confirmed);
                        })
                    }
                });
                resultsView.render();
                results.on('change add remove', function(){
                    QuizData.results = results.toJSON();
                });
            },
            templateHelpers: function(){
                var self = this;
                return {

                };
            }
        });
    });
