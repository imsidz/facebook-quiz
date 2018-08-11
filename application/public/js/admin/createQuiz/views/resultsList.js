define([
        'backbone',
        'views/resultItem',
        'views/EditableCollectionCompositeView',
        'hbs!templates/resultsList',
        'models/result',
        'views/resultItemEditor'
    ],
    function( Backbone, ResultItemView, EditableCollectionCompositeView, ResultsListTemplate, ResultModel, ResultItemEditorView ) {
        'use strict';

        /* Return a ItemView class definition */
        return Backbone.Marionette.EditableCollectionCompositeView.extend({

            initialize: function() {
                console.log("initialize a ResultsList EditableCollectionCompositeView");
            },

            template: ResultsListTemplate,

            childView: ResultItemView,
            childViewContainer: '.results-list',
            childEvents: {

            },
            collectionEvents: {
                'remove' : "render",
                'change' : "render"
            },

            /* ui selector cache */
            ui: {
                resultItemEditor: '.result-item-editor'
            },

            /* Ui events hash */
            events: {},

            createChild: function(callback){
                var self = this;
                var newResult = new ResultModel();
                var resultItemFormView = this._renderItemFormView(newResult);
                resultItemFormView.on('form:submitted', function(){
                    callback(newResult);
                    this.destroy();
                });
            },
            _destroyPreviousFormViewIfExits: function(){
                if(this._isResultItemFormViewShown()){
                    this.resultItemFormView.destroy();
                }
            },
            _isResultItemFormViewShown: function(){
                return (!!this.resultItemFormView);
            },
            onEditItemChild: function(childView, model) {
                this._renderItemFormView(model);
            },
            _renderItemFormView: function(result){
                //Remove currently shown form
                this._destroyPreviousFormViewIfExits();
                var self = this;
                var resultItemFormView = new ResultItemEditorView({
                    model: result
                });
                resultItemFormView.render().$el.appendTo(this.ui.resultItemEditor);
                //Focus first field for faster editing
                resultItemFormView.$('input:visible').first().focus();
                this.resultItemFormView = resultItemFormView;
                resultItemFormView.on('destroy', function(){
                    self.resultItemFormView = undefined;
                    //$(window).scrollTop(self.$el.offset().top);
                });
                $(window).scrollTop(resultItemFormView.$el.offset().top - 100);
                return resultItemFormView;
            },
            /* on render callback */
            onRender: function() {

            }
        });
    });
