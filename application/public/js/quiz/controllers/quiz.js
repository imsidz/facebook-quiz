define([
	'backbone',
	'underscore',
	'appMan',
	'quizMaster',
	'models/quizProgress',
	'models/question',
	'views/quizProgress',
	'views/question',
	'models/quizResult',
	'views/quizFinish',
    'views/shareLocker',
],
function( Backbone, _, AppMan, QuizMaster, QuizProgress, QuestionModel, QuizProgressView, QuestionView, QuizResultModel, QuizFinishView, ShareLockerView) {
    'use strict';

    var canvasContainer = $('#quizCanvasContainer');
    var topmenu = $('#topmenu');
    function scrollToTop() {
        $('body').trigger('scroll-top');
        $(window).scrollTop(canvasContainer.offset().top - (topmenu.outerHeight()));
    }
	
	function stripSlashes(url) {
		return url.replace(/\/+$/, "");
	}

	/*
	 * Returns a map of querystring parameters
	 *
	 * Keys of type <fieldName>[] will automatically be added to an array
	 *
	 * @param String url
	 * @return Object parameters
	 */
	function getParams(url) {
		var regex = /([^=&?]+)=([^&#]*)/g, params = {}, parts, key, value;

		while((parts = regex.exec(url)) != null) {

			key = parts[1], value = parts[2];
			var isArray = /\[\]$/.test(key);

			if(isArray) {
				params[key] = params[key] || [];
				params[key].push(value);
			}
			else {
				params[key] = value;
			}
		}

		return params;
	}

	function getUrlComponents(url){
		var el = document.createElement('a');
		el.href = url;
		return el;
	}

	function isScoreBased(){
		if(AppMan.reqres.request('quiz').get('type') == 'scoreBased') {
			return true;
		}else {
			return false;
		}
	}

	function getQuizConfigByKey(key) {
		var siteQuizConfig = AppMan.reqres.request('config:quiz');
		var quizSettings = AppMan.reqres.request('quiz').get('settings') || {};
		var configVal = siteQuizConfig[key];

		//Override global quiz config. The option will be a select dropdown field with values "As per quiz config" (the default from global quiz config, "Yes" (true), "No" (false)
		if(quizSettings[key] == "Yes") {
			configVal = true;
		} else if(quizSettings[key] == "No") {
			configVal = false;
		}
		return configVal;
	}
    function isQuizConfigEnabled(key) {
		var configValue = getQuizConfigByKey(key);
		configValue = (configValue == "true" || configValue === true);
        return configValue;
    }

    function isShowCorrectAnswerDuringQuizEnabled() {
        return isQuizConfigEnabled('showCorrectAnswerDuringQuiz');
    }

    AppMan.reqres.setHandler('isShowCorrectAnswersAtTheEndEnabled', function() {
        return isQuizConfigEnabled('showCorrectAnswersAtTheEnd');
    });

	function ifLoggedIn(callback) {
		if(User.isLoggedIn()) {
			callback();
		} else {
			$('body').on('loggedIn', function(){
				callback();
			});
		}
	}
	
	function prepareLoginPromptMessage() {
		var siteQuizConfig = AppMan.reqres.request('config:quiz');
		var quizSettings = AppMan.reqres.request('quiz').get('settings') || {};
		var loginPromptMessage;
		var globalLoginPromptMessage = (quizSettings.forceLogin === 'on-quiz-start') ? siteQuizConfig.loginPromptMessageOnQuizStart : ((quizSettings.forceLogin === 'before-result') ? siteQuizConfig.loginPromptMessageBeforeResult : '');
		//If login prompt message is specified 
		if(quizSettings.loginPromptMessage && quizSettings.loginPromptMessage.length) {
			loginPromptMessage = quizSettings.loginPromptMessage;
		} else if(globalLoginPromptMessage){
			loginPromptMessage = globalLoginPromptMessage;
		} else {
			if(quizSettings.forceLogin === 'on-quiz-start') {
				loginPromptMessage = 'Please login to start the quiz!';
			} else if(quizSettings.forceLogin === 'before-result'){
				loginPromptMessage = 'Please login to view your result!';
			} else {
				loginPromptMessage = 'Please login to proceed!';
			}
		}
		AppMan.reqres.setHandler('quiz:loginPromptMessage', function(){
			return loginPromptMessage;
		});
	}

    function processForceLogin(callback) {
        var quizSettings = AppMan.reqres.request('quiz').get('settings') || {};
        //Checking Force login
        if(quizSettings.forceLogin === 'before-result' && !User.isLoggedIn()) {
            AppMan.trigger('login-required', AppMan.reqres.request('quiz:loginPromptMessage'));
            var finishAfterLogin = function() {
                if(!self.quizFinished)
                    callback();
                $('body').off('loggedIn', finishAfterLogin);
            }
            $('body').on('loggedIn', finishAfterLogin);
            return;
        } else {
            callback();
        }
    }

    window.processShareLock = function(callback) {
        //Checking if share lock is enabled
        if(isQuizConfigEnabled('enableShareLock')) {
            AppMan.trigger('share-to-proceed');
            AppMan.command.execute('quiz:showShareLocker', function(success) {
                if(success)
                    callback();
            });
            return;
        } else {
            callback();
            return;
        }
    }

    function runResultLockers(callback) {
        processForceLogin(function() {
            processShareLock(function() {
                callback();
            })
        })

    }

	var QuizController = Backbone.Marionette.Controller.extend({

        quizStarted: false,
        quizFinished: false,
		initialize: function( options ) {
			console.log('initialize a Quiz Controller');
			var self = this;
			App.quizController = this;
			this.quiz = options.quiz;
			this.quizMaster = new QuizMaster({
				quiz: this.quiz
			});
			this.finishedQuestions = {};
			self.currentQuestion = -1;
			AppMan.reqres.setHandler('user', function(){
				return User;
			});
			AppMan.reqres.setHandler('config:socialMedia', function(){
				if(!window.SiteMainConfig) {
					return {};
				}
				return window.SiteMainConfig.social || {};
			});
			AppMan.reqres.setHandler('config:quiz', function(){
				if(!window.SiteQuizConfig) {
					return {};
				}
				return window.SiteQuizConfig || {};
			});
			AppMan.reqres.setHandler('quiz', function(){
				return App.quizController.quiz;
			});
			AppMan.reqres.setHandler('quiz:meta', function(property){
				if(property) {
					return App.quizMeta[property];
				} else {
					return App.quizMeta;
				}
			});
			AppMan.reqres.setHandler('quiz:viewQuizUrl', function(){
				var url, 
					user = AppMan.reqres.request('user');
				url = AppMan.reqres.request('quiz:meta', 'viewQuizUrl');
				return url;
			});

            AppMan.reqres.setHandler('quiz:viewQuizEmbedUrl', function(){
                var url = AppMan.reqres.request('quiz:viewQuizUrl') + '?embed=true';
                return url;
            });

			AppMan.reqres.setHandler('quiz:quizShareUrl', function(options){
				options = options || {};
				var url = AppMan.reqres.request('quiz:viewQuizUrl'),
					user = AppMan.reqres.request('user');
				if(options.resultUrl) {
					url = options.resultUrl;
				}
				var urlComponents = getUrlComponents(url);
				var params = getParams(url);
				if(options.isRef && user.isLoggedIn()) {
					params['ref-by'] = user.data.id;
				}
				if(user.isLoggedIn() && typeof FB != "undefined") {
					params['user-fb-id'] = FB.getUserID();
				}
				url = urlComponents.protocol + '//' + urlComponents.host + urlComponents.pathname  + '?' + $.param(params);
				return url;
			});

			AppMan.reqres.setHandler('quiz:quizResultShareUrl', function(options){
				//This handler will be overridden when the result url is obtained from the server. This enables support for custom result url.
				//By default(if not overridden) Return the normal url generated by the quizShareUrl request handler.
				return AppMan.reqres.request('quiz:quizShareUrl');
			});

			AppMan.reqres.setHandler('quiz:quizResultImageUrl', function(options){
				//Set result image url as false. The result view will check for this data and show the default result image if the value obtained is false.
				//Might be over-ridden when a custom result is generated
				return false;
			})

			AppMan.reqres.setHandler('quiz:resultData', function(resultId){
				if(!resultId) {
					return false;
				}
				return _.findWhere(self.quiz.get('results'), {id: resultId});
			});
			AppMan.reqres.setHandler('quiz:questions', function(){
				return self.quiz.get('questions');
			});
			AppMan.reqres.setHandler('quiz:totalQuestions', function(){
				return self.quiz.get('questions').length;
			});
			AppMan.reqres.setHandler('quiz:get-question', function(index){
				var questions = AppMan.reqres.request('quiz:questions');
                if(!questions || !questions[index]) {
                    return false;
                } else {
                    return questions[index];
                }
			});
			AppMan.reqres.setHandler('quiz:next-question', function(){
				var questions = AppMan.reqres.request('quiz:questions');
				var nextQuesion = AppMan.reqres.request('quiz:get-question', self.currentQuestion + 1);
				return (nextQuesion || false);
			});
			AppMan.on('quiz:skipped-question', function(question){
				AppMan.trigger('quiz:finished-question', question);
				AppMan.command.execute('quiz:next-question');
			});
			AppMan.on('quiz:answered-question', function(question, choiceId){
				AppMan.trigger('quiz:finished-question', question);
				AppMan.command.execute('quiz:next-question');
			});
			AppMan.on('quiz:finished-question', function(question){
				self.finishedQuestions[question.index] = true;
			});
            AppMan.on('quiz:finished', function(){
                self.quizFinished = true;
            });
            AppMan.on('quiz:started', function(){
                self.quizStarted = true;
            });

			
			prepareLoginPromptMessage();
		},
		viewQuiz: function(){
			var self = this;
			function populateProgressStages(){
				var questions = AppMan.reqres.request('quiz:questions');
                //If no questions are found, there are no stages. Skip
                if(!questions || !questions.length)
                    return;
				var stages = [{stage: __('start'), completed: true, questionId: -1, index: -1}];
				if(self.currentQuestion == -1) {
					stages[0].active = 1;
				}
				
				//Marking start and finial states
				stages[0].isStart = true;
				stages[stages.length - 1].isFinish = true;
				var stagePos = 1,
					stage = {};
				_.each(questions, function(question) {
					var index = stagePos-1;
					stage = {stage: stagePos, questionId: question.id, index: index};
					if(self.finishedQuestions[question.index]) {
						stage.completed = true;
					} else {
						stage.completed = false;
					}
					if(self.currentQuestion == question.index) {
						stage.active = true;
					} else {
						stage.active = false;
					}
					stages.push(stage);
					stagePos++;
				});
				var onFinishedStage = (self.currentQuestion >= questions.length) ? true : false;
				stages.push({stage: __('finish'), completed: AppMan.reqres.request('quiz:is-finished') ? true : false, active: onFinishedStage});
				return stages;
			}

            function setupQuizProgressView() {
                var questions = AppMan.reqres.request('quiz:questions');
                var stages = populateProgressStages();
                var quizProgress = new QuizProgress({
                    stages: stages
                });
                App.quizProgressView = new QuizProgressView({
                    model: quizProgress
                });
                //If no questions are found, there are no stages. Skip
                if(questions && questions.length) {
                    App.quizProgress.show(App.quizProgressView).$el.parents('.quiz-progress-row').show();
                }

                AppMan.on('quiz:question:change', function(){
                    quizProgress.set({
                        stages: populateProgressStages()
                    });
                });
            }


			this.quiz.settings = this.quiz.settings || {};
			
			AppMan.on('quiz:start', function(){
				var user = AppMan.reqres.request('user');
				var quizSettings = self.quiz.get('settings') || {};
				
				if(quizSettings.forceLogin === 'on-quiz-start' && !User.isLoggedIn()) {
					AppMan.trigger('login-required', AppMan.reqres.request('quiz:loginPromptMessage'));
                    var startAfterLogin = function() {
                        if(!self.quizStarted)
                            AppMan.trigger('quiz:start');
                        $('body').off('loggedIn', startAfterLogin);
                    }
					$('body').on('loggedIn', startAfterLogin);
					return;
				}

                setupQuizProgressView();

				App.quizProgressView.on('quiz:show-question', function(questionIndex){
					questionIndex = (questionIndex === -1) ? 0 : questionIndex;
					//If question not yet finished, dont show - the user has to do all the previous questions to reach this one
					if(!self.finishedQuestions[questionIndex]) {
						return;
					}
					//If the quiz is score based, user should not attempt the question again as it will help them fix their scores.
					if(isScoreBased()) {
						return;
					}
					var question = AppMan.reqres.request('quiz:get-question', questionIndex);
					self.currentQuestion = questionIndex;
					AppMan.command.execute('quiz:show-question', question);
				});
				AppMan.command.execute('quiz:next-question');
				AppMan.trigger('quiz:started');
			});
			AppMan.on('quiz:finish', function(){
				var quizSettings = self.quiz.get('settings') || {};
				function onFinish(){
					App.quizProgressView.model.set({
						stages: populateProgressStages()
					});
					var result = new QuizResultModel(self.quizMaster.prepareResult());
					var quizFinishView = new QuizFinishView({
						model: result
					});
					quizFinishView.on('quiz:share', function(){
						AppMan.trigger('quiz:share');
					});
					var quizSettings = self.quiz.get('settings') || {};
					App.quizCanvas.show(quizFinishView);
					scrollToTop();
					AppMan.trigger('quiz:finished');
					AppMan.trigger('quiz:got-result', result);
				}
				runResultLockers(function() {
                    onFinish();
                });
			});
			AppMan.command.setHandler('quiz:next-question', function(){
				self.currentQuestion++;
				var nextQuestion = AppMan.reqres.request('quiz:get-question', self.currentQuestion);
				if(nextQuestion) {
					AppMan.command.execute('quiz:show-question', nextQuestion);
				} else {
					AppMan.trigger('quiz:finish');
				}
			});
			AppMan.command.setHandler('quiz:show-question', function(question){
				var newQuestion = new QuestionModel(question);
				var newQuestionView = new QuestionView({
					model: newQuestion
				});
				newQuestionView.on('quiz:skipped-question', function(question){
					AppMan.trigger('quiz:skipped-question', question);
				});
				newQuestionView.on('quiz:answered-question', function(questionJSON, choiceId){
					function proceedAfterAnswering(){
						AppMan.trigger('quiz:answered-question', questionJSON, choiceId);
					}
					if(isScoreBased() && isShowCorrectAnswerDuringQuizEnabled()){
						newQuestionView.showAnswerResponse(choiceId);
						newQuestionView.on('quiz:proceed-after-answer-response', function(question){
							proceedAfterAnswering();
						});
					} else{
						proceedAfterAnswering();
					}
				});
				AppMan.trigger('quiz:question:change');
				App.quizCanvas.show(newQuestionView);
				scrollToTop();
			});

            AppMan.command.setHandler('quiz:showShareLocker', function(callback) {
                var url = AppMan.reqres.request('quiz:viewQuizUrl');
                var shareLockerModel = new Backbone.Model({
                    url : url
                });
                var shareLockerView = new ShareLockerView({
                    model: shareLockerModel
                });
                shareLockerView.on('done', function(){
                    AppMan.trigger('quiz:share');
                    callback(true);
                });
                App.quizCanvas.show(shareLockerView);
                scrollToTop();
            })
			
			AppMan.on('login-required', function(message){
				AppMan.command.execute('promptLogin', message);
			});
			AppMan.command.setHandler('promptLogin', function(message){
				$('body').trigger('prompt-login', message);
                scrollToTop();
			});


            //Listen to 'activity-recorded' event to do stuff when activity is recorded
            self.quiz.on('activity-recorded', function() {
                $('body').trigger('user-activity-recorded');
            });

			AppMan.on('quiz:started', function(){
				$('body').trigger('quiz:started');
				setTimeout(function(){
                    scrollToTop();
                }, 200);
				ifLoggedIn(function(){
					self.quiz.recordActivity('attempt');
				});
			});
			
			AppMan.on('quiz:finished', function(){
				ifLoggedIn(function(){
					self.quiz.recordActivity('completion');
				});
			});
			
			AppMan.on('quiz:like', function(){
				ifLoggedIn(function(){
					self.quiz.recordActivity('like');
				});
			});
			
			AppMan.on('quiz:share', function(){
				ifLoggedIn(function(){
					self.quiz.recordActivity('share');
				});
			});
			
			AppMan.on('quiz:comment', function(){
				ifLoggedIn(function(){
					self.quiz.recordActivity('comment');
				});
			});
			
			AppMan.on('quiz:answered-question', function(question, choiceId){
				ifLoggedIn(function(){
					self.quiz.recordUserAnswer(question.id, choiceId);
				});
			});
			AppMan.on('quiz:got-result', function(result){
				var resultId = result.get('id');
				self.quiz.on('result-ready', function (resultData) {
					AppMan.trigger('quiz:result-ready', resultData);
				})
				self.quiz.recordUserResult(resultId);
			});

			AppMan.on('quiz:result-ready', function (resultData) {
				//Overrides the default quiz result url handler
				AppMan.reqres.setHandler('quiz:quizResultShareUrl', function(options){
					options = options || {};
					options.resultUrl = resultData.url
					return AppMan.reqres.request('quiz:quizShareUrl', options);
				})
				if(resultData.imageUrl) {
					AppMan.reqres.setHandler('quiz:quizResultImageUrl', function(options){
						return resultData.imageUrl;
					})
				}
			});
			
			AppMan.command.execute('fb', function(){
				FB.Event.subscribe('edge.create', function fbLikeCallback(url){
					url = stripSlashes(url);
					if(url === stripSlashes(AppMan.reqres.request('quiz:viewQuizUrl'))) {
						AppMan.trigger('quiz:like');
					}
				});
				
				FB.Event.subscribe('comment.create', function fbLikeCallback(response){
					var url = stripSlashes(response.href);
					if(url === stripSlashes(AppMan.reqres.request('quiz:viewQuizUrl'))) {
						AppMan.trigger('quiz:comment');
					}
				});
			});

            (function() {
                var body = $('body');
                //Track disqus comments
                body.on('disqus:new-comment', function(comment) {
                    AppMan.trigger('quiz:comment');
                });

                //Track shares
                body.on('social:share', function() {
                    AppMan.trigger('quiz:share');
                });
            })();
			
			//console.log(a,b);
		},
		all: function(){
			//alert('default route fired');
		}
	});

	return QuizController;
});
