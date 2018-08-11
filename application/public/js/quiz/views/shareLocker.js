define([
        'backbone',
        'hbs!templates/shareLocker',
        'appMan'
    ],
    function( Backbone, shareLockerTmpl, AppMan  ) {
        'use strict';

        /* Return a ItemView class definition */
        return Backbone.Marionette.ItemView.extend({

            initialize: function() {
                console.log("initialize a shareLocker ItemView");
            },
            className: 'share-locker',
            template: shareLockerTmpl,

            /* ui selector cache */
            ui: {

            },

            /* Ui events hash */
            events: {
                'click [data-network="facebook"]' : 'shareOnFB'
            },
            shareOnFB: function(){
                var self = this;
                var model = self.model;
                var url = model.get('url');
                FB.ui(
                    {
                        method: 'share',
                        href: url
                    },
                    // callback
                    function(response) {
                        if (response && !response.error_message) {
                            self.trigger('done', {
                                network: 'facebook'
                            });
                        } else {

                        }
                    }
                );
            },
            /* on render callback */
            onRender: function() {
            },
            templateHelpers: function(){
                return {

                }
            }
        });

    });
