define([
        'backbone',
        'underscore',
        'appMan',
        'hbs!templates/quizResult',
        'views/correctAnswers'
    ],
    function( Backbone, _, AppMan, ResultTmpl, CorrectAnswersView) {
        'use strict';

        /* Return a ItemView class definition */
        return Backbone.Marionette.LayoutView.extend({

            initialize: function() {
                var self = this;
                console.log("initialize a QuizResult Layout");
            },
            regions: {
                'correctAnswers' : '.correctAnswers'
            },
            template: ResultTmpl,
            className: 'quiz-result',

            /* ui selector cache */
            ui: {},

            /* Ui events hash */
            events: {
                "click .social-sharing-btn" : "trackShareActivity",
                "click .share-on-fb" : "shareOnFb",
                "click .view-correct-ans-btn" : "viewCorrectAnswers"
            },
            shareOnFb: function(e){
                var $this = $(e.currentTarget);
                (typeof ga == "function") && ga('send', 'event', 'share', 'facebook', $this.data('url'));
                var sharerUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + $this.data('url');
                window.open(sharerUrl, "popup", "width=600px,height=300px,left=50%,top=50%");
                this.trackShareActivity();
            },
            trackShareActivity: function() {
                this.trigger('quiz:share');
            },
            viewCorrectAnswers: function(e){
                var self = this;
                var questionsAnswers = {questions: []};
                var quiz = AppMan.reqres.request('quiz');
                var questions = quiz.get('questions');
                _.each(questions, function(question) {
                    var correctChoice = question.choices.findWhere({correct: true});
                    questionsAnswers.questions.push({
                        question: question.question,
                        answer: correctChoice ? correctChoice.get('title') : "",
                        image: correctChoice ? correctChoice.get('image') : ""
                    });
                });
                var correctAnsView = new CorrectAnswersView({
                    model : new Backbone.Model(questionsAnswers)
                });
                self.correctAnswers.show(correctAnsView);
                $(e.currentTarget).hide();
            },
            onBeforeRender: function(){
            },
            /* on render callback */
            onRender: function() {
                var self = this;
                var quizConfig = AppMan.reqres.request('config:quiz');
                if(quizConfig.showSharePromptModal) {
                    setTimeout(function(){
                        self.$('#resultSharePromptModal').modal();
                    }, 8000);
                }
                self.$('#resultSharePromptModal').on('hidden.bs.modal', function() {
                    $(this).remove();
                });
                self.addResultShareButtons();
                self.on('result-displayed', function() {
                    self.renderResultGraph();
                });
                (typeof ga == "function") && ga('send', 'event', 'quiz', 'got-result', self.model.get('title'));
            },
            addResultShareButtons: function() {
                var self = this;
                var buttonsContainer = this.$('.result-share-buttons-container');
                var resultSharingButtonsBlock = $('.sharing-buttons-block').clone();
                resultSharingButtonsBlock.appendTo(buttonsContainer);

                var dataAttributes = ['data-url', 'data-text', 'data-image'];
                var resultShareButtons = resultSharingButtonsBlock.find('.social-sharing-btn');
                resultShareButtons.on('click', function() {
                    self.trackShareActivity();
                })
                _.each(dataAttributes, function(attribute){
                    resultShareButtons.attr(attribute, buttonsContainer.attr(attribute));
                });
                resultShareButtons.attr('href', '#');
                resultSharingButtonsBlock.find('.share-quiz-more-btn').click(function(e) {
                    resultSharingButtonsBlock.toggleClass('show-more');
                    e.preventDefault();
                    return false;
                });
            },
            renderResultGraph: function() {
                var resultGraph = this.$('.result-graph');
                (typeof resultGraph.circliful == "function") && resultGraph.circliful();
            },
            onAttach: function() {
                try{
                    SocialSharing.parse();
                } catch(e){}
            },
            templateHelpers: function(){
                var self = this;
                var quizConfig = AppMan.reqres.request('config:quiz');
                return {
                    socialMedia: function(){
                        return AppMan.reqres.request('config:socialMedia');
                    },
                    shareQuizLink: function(){
                        return AppMan.reqres.request('quiz:quizShareUrl', {
                            isRef : true
                        });
                    },
                    shareResultLink: function(){
                        return AppMan.reqres.request('quiz:quizResultShareUrl', {
                            resultId : self.model.get('id'),
                            isRef : true
                        });
                    },
                    quizTopic: function() {
                        var quiz = AppMan.reqres.request('quiz');
                        return quiz.get('topic');
                    },
                    quizUrl: function() {
                        return AppMan.reqres.request('quiz:viewQuizUrl');
                    },
                    quizRetryUrl: function() {
                        var url = AppMan.reqres.request('quiz:viewQuizUrl');
                        if(window.isQuizEmbedded) {
                            url += '?embed=true';
                        }
                        return url;
                    },
                    resultImage: function() {
                        var image = AppMan.reqres.request('quiz:quizResultImageUrl');
                        if(!image)
                            image = contentUrl(self.model.get('image'));
                        return image;
                    },
                    quizImage: function() {
                        var quiz = AppMan.reqres.request('quiz');
                        return quiz.get('image');
                    },
                    isScoreBased: function(){
                        var quiz = AppMan.reqres.request('quiz');
                        return ((quiz.get('type') === 'scoreBased') ? true : false);
                    },
                    showCorrectAnswersAtTheEnd: function() {
                        return AppMan.reqres.request('isShowCorrectAnswersAtTheEndEnabled');
                    },
                    quizConfig: function(){
                        var quizConfig = AppMan.reqres.request('config:quiz');
                        return quizConfig;
                    },
                    debug: function(optionalValue) {
                        console.log(this.quizConfig());
                        console.log("Current Context");
                        console.log("====================");
                        console.log(this);

                        if (optionalValue) {
                            console.log("Value");
                            console.log("====================");
                            console.log(optionalValue);
                        }
                        return true;
                    },
                    showPageLikePrompt: function(){
                        return quizConfig.showPageLikePrompt;
                    },
                    showSharePromptModal: function(){
                        return quizConfig.showSharePromptModal;
                    },
                    showFacebookComments: function(){
                        return quizConfig.showFacebookComments;
                    },
                    showResultScore: function(){
                        var showResultScore = quizConfig.showResultScore;
                        return showResultScore === true || showResultScore == "true";
                    },
                    percentScore: function () {
                        return AppMan.reqres.request('percentScore');
                    }
                };
            }
        });

    });
