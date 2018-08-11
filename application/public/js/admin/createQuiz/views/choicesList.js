define([
        'backbone',
        'views/choiceItem',
        'views/EditableCollectionCompositeView',
        'hbs!templates/choicesList',
        'models/choice',
        'views/choiceItemEditor'
    ],
    function( Backbone, ChoiceItemView, EditableCollectionCompositeView, ChoicesListTemplate, ChoiceModel, ChoiceItemEditorView ) {
        'use strict';

        /* Return a ItemView class definition */
        return Backbone.Marionette.EditableCollectionCompositeView.extend({

            initialize: function() {
                var self = this;
                console.log("initialize a ChoicesList EditableCollectionCompositeView");
                this.listenTo(this.collection, 'remove', this.onChildRemoved);
            },

            template: ChoicesListTemplate,

            childView: ChoiceItemView,
            childViewContainer: '.choices-list',
            childEvents: {

            },
            collectionEvents: {
                'remove' : "render"
            },

            /* ui selector cache */
            ui: {
                choiceItemEditor: '.choice-item-editor'
            },

            /* Ui events hash */
            events: {},

            createChild: function(callback){
                var self = this;
                var newChoice = new ChoiceModel();
                var choiceItemFormView = this._renderItemFormView(newChoice);
                choiceItemFormView.on('form:submitted', function(){
                    callback(newChoice);
                });
            },
            _destroyPreviousFormViewIfExits: function(){
                if(this._isChoiceItemFormViewShown()){
                    this.choiceItemFormView.destroy();
                }
            },
            _isChoiceItemFormViewShown: function(){
                return (!!this.choiceItemFormView);
            },
            onEditItemChild: function(childView, model) {
                this._renderItemFormView(model);
            },
            _renderItemFormView: function(choice){
                //Remove currently shown form
                this._destroyPreviousFormViewIfExits();
                var self = this;
                var choiceItemFormView = new ChoiceItemEditorView({
                    model: choice
                });
                choiceItemFormView.on('form:submitted', function(){
                    //self.hideChoiceEditorModal();
                    this.destroy();
                });
                choiceItemFormView.render().$el.appendTo(this.ui.choiceItemEditor);
                self.showChoiceEditorModal();
                //Focus first field for faster editing
                choiceItemFormView.$('input:visible').first().focus();
                this.choiceItemFormView = choiceItemFormView;
                choiceItemFormView.on('destroy', function(){
                    self.choiceItemFormView = undefined;
                    self.hideChoiceEditorModal();
                    $(window).scrollTop(self.ui.choiceItemEditor.offset().top - 400);
                });
                $(window).scrollTop(choiceItemFormView.$el.offset().top - 100);
                return choiceItemFormView;
            },
            onBeforeRender: function(){
                this.hideChoiceEditorModal();
            },
            /* on render callback */
            onRender: function() {

            },
            showChoiceEditorModal: function(){
                self.$('.choice-item-editor-modal').modal('show');
            },
            hideChoiceEditorModal: function(){
                self.$('.choice-item-editor-modal').modal('hide');
            },
            onChildRemoved: function(){
                var self = this;
                setTimeout(function(){
                    $(window).scrollTop(self.$el.offset().top - 100);
                },200);
            }
        });
    });
