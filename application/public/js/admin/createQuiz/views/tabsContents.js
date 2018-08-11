define([
        'backbone',
        'views/tabContent'
    ],
    function( Backbone, TabContentView ) {
        'use strict';

        /* Return a ItemView class definition */
        return Backbone.Marionette.CollectionView.extend({

            initialize: function() {
                console.log("initialize a TabsContents CollectionView");
            },
            el : '#tabContents',

            childView: TabContentView,

            /* ui selector cache */
            ui: {},

            /* Ui events hash */
            events: {},

            /* on render callback */
            onRender: function() {

            }
        });
    });
