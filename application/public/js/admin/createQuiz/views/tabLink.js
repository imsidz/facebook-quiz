define([
        'backbone',
        'hbs!templates/tabLink',
        'appMan'
    ],
    function( Backbone, TabLinkTmpl, AppMan) {
        'use strict';

        /* Return a ItemView class definition */
        return Backbone.Marionette.ItemView.extend({

            initialize: function() {
                console.log("initialize a TabLink ItemView");
                this.listenTo(this.model, 'change', this.render);
            },

            template: TabLinkTmpl,
            tagName: 'li',
            className: function(){
                if(this.model.get('active') === true)
                    return 'active';
            },
            attributes: function(){
                var attributes = {};
                attributes.class = '';
                attributes.role= "presentation";
                return attributes;
            },

            /* ui selector cache */
            ui: {},

            /* Ui events hash */
            events: {
                "hidden.bs.tab" : "onTabHidden",
                "show.bs.tab" : "onTabShown",
                "click a" : "onClick"
            },

            onClick: function(e){
                e.preventDefault();
                $(this).tab('show');
            },
            /* on render callback */
            onRender: function() {
                this.$('[data-toggle="tooltip"]').tooltip();
                var self = this;
                setTimeout(function(){
                    if(self.model.get('active') === true)
                        self.$el.addClass('active');
                    else
                        self.$el.removeClass('active');
                }, 100);
            },
            onTabShown: function(){
                this.model.set('active', true);
                this.trigger('tab:shown', this.getTabId());
            },
            getTabId : function(){
                return this.model.getTabId();
            },
            templateHelpers: function(){
                var self = this;
                return {
                    tabId : function(){
                        return self.model.getTabId();
                    },
                    completed: function(){
                        return self.model.isCompleted();
                    }
                };
            },
            /*
            Activate the tab
             */
            activate: function(){
                //this.model.set('active', true);
                this.$('a').click();
            },
            deActivate: function(){
                this.model.set('active', false);
            }
        });
    });
