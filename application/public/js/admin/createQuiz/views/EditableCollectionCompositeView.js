define([
        'backbone',
        'appMan',
        'marionetteSortable'
    ],
    function( Backbone, AppMan, MarionetteSortable) {
        'use strict';
        var noOp = function(){};
        var Marionette = Backbone.Marionette;
        /* Return a ItemView class definition */
        Marionette.EditableCollectionCompositeView =  Backbone.Marionette.SortableCompositeView.extend({

            constructor: function (options) {
                var self = this;
                //this.addNewChildTemplate = options.addNewChildTemplate;
                //this.childWrapperTemplate = options.childWrapperTemplate;
                this.childPreviewView = options.childPreviewView
                this.createChild = options.createChild ? options.createChild : this.createChild;
                this.onEditItemChild = options.onEditItemChild ? options.onEditItemChild : this.onEditItemChild;
                this.confirmDelete = options.confirmDelete || function(callback){
                    callback(confirm('Are you sure?'));
                };
                Marionette.SortableCollectionView.apply(this, arguments);
                this.on('create:child', function(){
                    var self = this;
                   if(this.createChild){
                       this.createChild.call(this, function(model){
                           if(model) {
                               self.collection.add(model);
                           }

                       });
                   }
                });
                this.on('edit:child', function(childView, model) {
                    if (this.onEditItemChild) {
                        this.onEditItemChild.apply(this, arguments);
                    }
                });
                //this.listenTo(this.collection, 'change', this.render);
                var itemViewEventsHash = this._getItemViewEventsHash();
                this.on('render', function(){
                    this.delegateEvents(this._getEventsHash());
                    this.children.each(function(childView) {
                        childView.delegateEvents(itemViewEventsHash);
                    });
                    //Trigger just-created event on childView that will be added in future
                    this.on('add:child', function(childView){
                        childView.trigger('just-created');
                    });
                    //this._renderAddNewChildElement();
                });
                //Delegate UI events on newly added child
                this.on('add:child', function(childView){
                    childView.delegateEvents(itemViewEventsHash);
                })
            },
            render: function(){
                Marionette.SortableCompositeView.prototype.render.apply(this, arguments);
            },
            _getEventsHash: function(){
                var editableView = this;
                return {
                    //Bind to only the first one in the sub tree - to avoid binding on add-action buttons of nested editableCompositeViews
                    'click [data-action="add"]:first': function(){
                        editableView.onAddNewClicked();
                    }
                }
            },
            _renderAddNewChildElement: function(){
                var addNewChildView = this._getAddNewChildView();
                //this.attachHtml(this, addNewChildView.render());
                addNewChildView.render();
                window.addNewChildView = addNewChildView;
            },
            _getAddNewChildView: function(){
                var self = this;
                var template = this.addNewChildTemplate;
                if(!template)
                    template = _.template('<a data-action="add" href="javascript:void(0)">Add new</a>');
                var AddNewChildView = Marionette.ItemView.extend({
                    el: self.$('.add-new-conatiner'),
                    template: template,
                    events: {
                        'click [data-action="add"]' : "onClick"
                    },
                    onClick: function(){
                        self.onAddNewClicked();
                    }
                });
                return new AddNewChildView();
            },
            _getItemViewEventsHash: function(){
                var editableView = this;
                return {
                    'click [data-action="remove"]': function(){
                        var targetView = this;
                        editableView.confirmDelete(function(confirmed){
                            if(confirmed){
                                editableView.collection.remove(targetView.model);
                            }
                        })
                    },
                    'click [data-action="edit"]': function(){
                        var targetView = this;
                        editableView.trigger('edit:child', targetView, targetView.model);
                    }
                }
            },
            onAddNewClicked: function() {
                this.trigger('create:child');
            }
        });
        return Marionette.EditableCollectionCompositeView;
    });