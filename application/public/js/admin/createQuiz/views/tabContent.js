define([
        'backbone',
        'hbs!templates/tabContent',
        'appMan',
        'views/detailsForm'
    ],
    function( Backbone, TabContentTmpl, AppMan, DetailsFormView) {
        'use strict';

        /* Return a ItemView class definition */
        return Backbone.Marionette.ItemView.extend({

            initialize: function() {
                console.log("initialize a TabLink CollectionView");
                var self= this;
                AppMan.reqres.setHandler('tabContentView:' + this.getTabId(), function(){
                    return self;
                })
            },

            template: TabContentTmpl,
            tagName: 'div',
            attributes: function(){
                var attributes = {};
                attributes.class = 'tab-pane fade';
                attributes.role= "tabpanel";
                attributes.id = this.getTabId();
                return attributes;
            },

            /* ui selector cache */
            ui: {},

            /* Ui events hash */
            events: {},

            /* on render callback */
            onRender: function() {
            },
            getTabId : function(){
                return this.model.getTabId();
            },
            templateHelpers: function(){
                var self = this;
                return {
                    tabId : function(){
                        return self.model.getTabId();
                    }
                };
            }
        });
    });
