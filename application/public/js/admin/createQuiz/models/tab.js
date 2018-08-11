define([
        'backbone'
    ],
    function( Backbone ) {
        'use strict';

        /* Return a model class definition */
        return Backbone.Model.extend({
            initialize: function() {
                console.log("initialize a TabLink model");
            },
            getTabId: function(){
                return this.get('title').replace(' ', '-').toLowerCase();
            },
            isCompleted: function(){
                switch(this.getTabId()){
                    case "basic-details" :
                        return !!QuizData.topic;
                    case "results":
                        return !!(QuizData.results && QuizData.results.length);
                    case "questions":
                        return !!(QuizData.questions && QuizData.questions.length);
                    case "og-settings":
                        return !!QuizData.ogImages;
                    default:
                        return false;
                }
            },

            defaults: {
                active: false
            }
        });
    });
