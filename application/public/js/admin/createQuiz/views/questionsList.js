define([
        'backbone',
        'views/questionItem',
        'views/EditableCollectionCompositeView',
        'hbs!templates/questionsList',
        'models/question',
        'views/questionItemEditor'
    ],
    function( Backbone, QuestionItemView, EditableCollectionCompositeView, QuestionsListTemplate, QuestionModel, QuestionItemEditorView ) {
        'use strict';

        /* Return a ItemView class definition */
        return Backbone.Marionette.EditableCollectionCompositeView.extend({

            initialize: function() {
                console.log("initialize a QuestionsList EditableCollectionCompositeView");
            },

            template: QuestionsListTemplate,

            childView: QuestionItemView,
            childViewContainer: '.questions-list',
            childEvents: {

            },
            collectionEvents: {
                'remove' : "render"
            },

            /* ui selector cache */
            ui: {
                questionItemEditor: '.question-item-editor'
            },

            /* Ui events hash */
            events: {},

            createChild: function(callback){
                var self = this;
                var newQuestion = new QuestionModel();
                var questionItemFormView = this._renderItemFormView(newQuestion);
                questionItemFormView.on('form:submitted', function(){
                    callback(newQuestion);
                });
            },
            _destroyPreviousFormViewIfExits: function(){
                if(this._isQuestionItemFormViewShown()){
                    this.questionItemFormView.destroy();
                }
            },
            _isQuestionItemFormViewShown: function(){
                return (!!this.questionItemFormView);
            },
            onEditItemChild: function(childView, model) {
                this._renderItemFormView(model);
            },
            _renderItemFormView: function(question){
                //Remove currently shown form
                this._destroyPreviousFormViewIfExits();
                var self = this;
                var questionItemFormView = new QuestionItemEditorView({
                    model: question
                });
                questionItemFormView.on('form:submitted', function(){
                    this.destroy();
                });
                questionItemFormView.render().$el.appendTo(this.ui.questionItemEditor);
                //Focus first field for faster editing
                questionItemFormView.$('input:visible').first().focus();
                this.questionItemFormView = questionItemFormView;
                questionItemFormView.on('destroy', function(){
                    self.questionItemFormView = undefined;
                    $(window).scrollTop(self.ui.questionItemEditor.offset().top - 400);
                });
                $(window).scrollTop(questionItemFormView.$el.offset().top - 100);
                return questionItemFormView;
            },
            /* on render callback */
            onRender: function() {

            }
        });
    });
