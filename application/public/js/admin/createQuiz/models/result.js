define([
        'backbone'
    ],
    function( Backbone ) {
        'use strict';

        /* Return a model class definition */
        return Backbone.Model.extend({
            initialize: function() {
                console.log("initialize a Result model");
                if(!this.get('id')) {
                    this.set('id', guid());
                }
            },

            defaults: {}
        });
    });
