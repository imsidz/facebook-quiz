define([
	'backbone',
	'underscore',
	'appMan',
		'views/tabsLinks',
		'views/tabsContents',
		'collections/tabs',
		'views/detailsForm',
		'views/resultsForm',
		'views/questionsForm',
		'views/ogSettingsForm'
],
function( Backbone, _, AppMan, TabsLinksView, TabsContentsView, Tabs, DetailsFormView, ResultsFormView, QuestionsFormView, OgSettingsFormView) {
    'use strict';
	
	function scrollToTop() {
		$(window).scrollTop(App.quizCanvas.el.offsetTop);
	}
	
	function stripSlashes(url) {
		return url.replace(/\/+$/, "");
	}

	var QuizController = Backbone.Marionette.Controller.extend({

		initialize: function( options ) {
			console.log('initialize a Quiz Controller');
			var self = this;
			App.quizController = this;
			this.quiz = options.quiz;

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

			AppMan.reqres.setHandler('quiz:questions', function(){
				return self.quiz.get('questions');
			});
			AppMan.reqres.setHandler('quiz:totalQuestions', function(){
				return self.quiz.get('questions').length;
			});
			AppMan.reqres.setHandler('quiz:get-question', function(index){
				var questions = AppMan.reqres.request('quiz:questions');
				if(questions[index]) {
					return questions[index];
				}else {
					return false;
				}
			});
		},
		createEditQuiz: function(){
			$('#quizEditor').removeClass('hidden');
			$('#quizEditorLoading').addClass('hidden');
			var tabs = ['Basic Details', 'Results', 'Questions', 'OG settings'];
			var tabsArray = [];
			_.each(tabs, function(tab){
				tabsArray.push({title: tab});
			});

			AppMan.reqres.setHandler('tabs', function(){
				return tabsArray;
			});

			var tabs = AppMan.reqres.request('tabs');
			var tabsCollection = new Tabs(tabs);
			var tabsLinksView = new TabsLinksView({
				collection: tabsCollection
			});

			var footerTabsLinksView = new TabsLinksView({
				el: '#footerTabLinks',
				collection: tabsCollection
			});

			var tabsContentsView = new TabsContentsView({
				collection: tabsCollection
			});
			function onTabShown(tabId){
				AppMan.trigger('tab:shown', tabId);
			}
			tabsLinksView.on('tab:shown', function(tabId){
				onTabShown(tabId);
			});
			footerTabsLinksView.on('tab:shown', function(tabId){
				onTabShown(tabId);
			});

			AppMan.reqres.setHandler('formView', function(tabId){
				switch(tabId) {
					case "basic-details":
						return DetailsFormView;
					case "results":
						return ResultsFormView;
					case "questions":
						return QuestionsFormView;
					case "og-settings":
						return OgSettingsFormView;
				}
			});

			AppMan.on('tab:shown', function(tabId) {
				$(window).scrollTop(0);
				App.quizController.currentTab = tabId;
			});
			AppMan.on('tab:shown', function(tabId){
				var view = AppMan.reqres.request('tabContentView:' + tabId);
				if(!view.formView) {
					var formView = AppMan.reqres.request('formView', tabId);
					view.formView = new formView({
						el: view.$('.form-container')
					});
				}
				view.formView.render();
				$(window).trigger('quiz-editor-tab-shown', tabId);
			});
			tabsContentsView.render();
			tabsLinksView.render();
			footerTabsLinksView.render();
			tabsLinksView.activateFirst();

			//AppMan.reqres.setHandler('formView', self.getTabId()
		},
		all: function(){
			//alert('default route fired');
		}
	});

	return QuizController;
});
