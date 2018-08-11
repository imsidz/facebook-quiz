define([
        'app',
        'backbone',
        'models/tab'
    ],
    function( App, Backbone, Tab ) {
        'use strict';

        /* Return a collection class definition */
        return Backbone.Collection.extend({
            initialize: function() {
                console.log('initialize a TabsContents collection');
            },
            model: Tab
        });
    });
