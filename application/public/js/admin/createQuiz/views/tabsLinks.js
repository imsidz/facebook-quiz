define([
        'backbone',
        'views/tabLink'
    ],
    function( Backbone, TabLinkView ) {
        'use strict';

        /* Return a ItemView class definition */
        return Backbone.Marionette.CollectionView.extend({

            initialize: function() {
                console.log("initialize a TabsLinks CollectionView");
            },
            el : '#tabLinks',

            childView: TabLinkView,
            childEvents: {
               "tab:shown": function(child, tabId){
                   this.trigger('tab:shown', tabId);
                   this.children.each(function(childView){
                       //DeActivate all other children
                       if(childView != child) {
                           childView.deActivate();
                       }
                       childView.render();
                   });
               }
            },

            /* ui selector cache */
            ui: {},

            /* Ui events hash */
            events: {},

            /* on render callback */
            onRender: function() {
                //Activate the first tab
                //this.children.first().activate();
            },
            activateFirst: function(){
                this.children.first().activate();
            }
        });
    });
