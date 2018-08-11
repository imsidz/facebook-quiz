define([
        'backbone',
        'hbs!templates/resultItemEditor',
        'appMan'
    ],
    function( Backbone, ResultItemEditorTmpl, AppMan) {
        'use strict';

        /* Return a ItemView class definition */
        return Backbone.Marionette.ItemView.extend({

            initialize: function() {
                console.log("initialize a ResultItemEditor ItemView");
            },

            template: ResultItemEditorTmpl,
            className: '',

            /* ui selector cache */
            ui: {
                form: '.form',
                formResultsBox: '.form-results-box'
            },

            /* Ui events hash */
            events: {
            },

            /* on render callback */
            onRender: function() {
                var self = this;
                self.$('.form, .form-results-box').html('');
                setTimeout(function(){
                    self.ui.form.jsonForm({
                        schema: getResultSchema(),
                        form: [
                            '*',
                            {
                                "type": "actions",
                                "items": [
                                    {
                                        "type": "submit",
                                        "title": "Update result (draft)"
                                    },
                                    {
                                        type: "button",
                                        title: '<i class="fa fa-times"></i> Close',
                                        onClick: function(e){
                                            e.preventDefault();
                                            self.destroy();
                                        }
                                    }
                                ]
                            }
                        ],
                        value: self.model.toJSON(),
                        onSubmit: function (errors, values) {
                            if (errors) {
                                self.ui.formResultsBox.html('<p>Some error! Please check if you filled in the details correctly</p>');
                            }
                            else {
                                //values = addResultIds(values);
                                self.model.set(values);
                                self.ui.formResultsBox.html('<div class="alert alert-success"><strong>Updated draft!</strong></div>');
                                self.trigger('form:submitted');
                            }
                        }
                    });
                }, 50);
            },
            templateHelpers: function(){
                var self = this;
                return {

                };
            }
        });
    });
