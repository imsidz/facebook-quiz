define([
        'backbone',
        'backbone.marionette',
        'underscore',
        'appMan',
        'routers/quiz',
        'controllers/quiz',
        'models/quiz',
        'collections/choices'
    ],
    function ( Backbone, Marionette, _, AppMan, QuizRouter, QuizController, QuizModel, ChoicesCollection) {
        'use strict';
        var App = new Backbone.Marionette.Application();

        App.addRegions({

        });

        App.on("start", function(){
            /*Backbone.history.start({
                root: BASE_PATH + '/admin/quizes/create',
                pushState: true
            });*/
        });
        App.quizModel = new QuizModel(QuizData);
        App.quizMeta = QuizMeta;
        App.addInitializer(function(options){
            /*var quizRouter = new QuizRouter({
                controller: new QuizController({
                    quiz: App.quizModel
                })
            });
            App.quizRouter = quizRouter;*/
            var quizController = new QuizController({
                quiz: App.quizModel
            });
            quizController.createEditQuiz();
        });

        window.App = App;
        return App;
    });
