define([
        'backbone',
        'backbone.marionette',
        'underscore',
        'appMan',
        'utils/fbApi',
        'views/quizHeader',
        'views/quizWelcome',
        'routers/quiz',
        'controllers/quiz',
        'models/quiz',
        'collections/choicesCollection'
    ],
    function ( Backbone, Marionette, _, AppMan, FBApi, QuizHeaderView, QuizWelcomeView, QuizRouter, QuizController, QuizModel, ChoicesCollection) {
        'use strict';
        var App = new Backbone.Marionette.Application();

        App.addRegions({
            quizHeader: '#quizHeader',
            quizProgress: '#quizProgress',
            quizCanvas: '#quizCanvas'
        });
        App.on("start", function(){
            /*Backbone.history.start({
             root: ''
             });*/
        });
        function touchupQuizData() {
            QuizData.active = (QuizData.active === 'true' || QuizData.active === true) ? true : false;
            //assign indexes to questions
            for(var i in QuizData.questions) {
                //QuizData.questions[i].id = i;
                QuizData.questions[i].skippable = (QuizData.questions[i].skippable === 'true' || QuizData.questions[i].skippable === true) ? true : false;
                QuizData.questions[i].index = i;
                //assign indexed to choices
                for(var j in QuizData.questions[i].choices) {
                    //QuizData.questions[i].choices[j].id = j;
                    if(QuizData.questions[i].choices[j].correct !== undefined) {
                        QuizData.questions[i].choices[j].correct = (QuizData.questions[i].choices[j].correct === 'true' || QuizData.questions[i].choices[j].correct === true) ? true : false;
                    }
                }
                QuizData.questions[i].choices = new ChoicesCollection(QuizData.questions[i].choices);
            }
        }
        touchupQuizData();
        App.quizModel = new QuizModel(QuizData);
        App.quizMeta = QuizMeta;
        App.addInitializer(function(options){
            /*var quizRouter = new QuizRouter({
             controller: new QuizController({
             quiz: App.quizModel
             })
             });*/
            var quizController = new QuizController({
                quiz: App.quizModel
            });
            quizController.viewQuiz();

            var quizHeaderView = new QuizHeaderView({
                model: App.quizModel
            });
            App.quizHeader.show(quizHeaderView);
            AppMan.reqres.setHandler('quiz:isStarted', function(){
                return false;
            });
            AppMan.on('quiz:started', function(){
                AppMan.reqres.setHandler('quiz:isStarted', function(){
                    return true;
                });
                quizHeaderView.render();
            });
            var quizWelcomeView = new QuizWelcomeView({
                model: App.quizModel
            });

            App.quizCanvas.show(quizWelcomeView);
            $('body').trigger('quiz:ready');
        });
        window.App = App;
        return App;
    });
