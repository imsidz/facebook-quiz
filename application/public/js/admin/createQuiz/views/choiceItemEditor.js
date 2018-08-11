define([
        'backbone',
        'hbs!templates/choiceItemEditor',
        'appMan'
    ],
    function( Backbone, ChoiceItemEditorTmpl, AppMan) {
        'use strict';

        /* Return a ItemView class definition */
        return Backbone.Marionette.ItemView.extend({

            initialize: function() {
                console.log("initialize a ChoiceItemEditor ItemView");
            },

            template: ChoiceItemEditorTmpl,
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
                var choicesSchema = getQuestionSchema().choices.items.properties;
                self.ui.form.jsonForm({
                    schema: choicesSchema,
                    form: [
                        '*',
                        {
                            "type": "actions",
                            "items": [
                                {
                                    "type": "submit",
                                    "title": "Save Choice"
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
                    value: this.model.toJSON(),
                    onSubmit: function (errors, values) {
                        if (errors) {
                            self.ui.formResultsBox.html('<p>Some error! Please check if you filled in the details correctly</p>');
                        }
                        else {
                            self.model.set(values);
                            self.ui.formResultsBox.html('<div class="alert alert-success"><strong>Changes saved!</strong></div>');
                            self.trigger('form:submitted');
                        }
                    }
                });
                this.$('._jsonform-array-addmore').click(function(){
                    self.updateFavouredResultChoiceLabels();
                });
                this.updateFavouredResultChoiceLabels();
            },
            templateHelpers: function(){
                var self = this;
                return {

                };
            },
            updateFavouredResultChoiceLabels: function(){
                var self = this;
                var i = 0;
                var stop = false;

                while(true) {
                    var selector = '.' + escapeSelector('jsonform-error-favoursResult['+i+']---result');
                    var selectElms = self.$(selector).find('select');
                    if(!selectElms.length) {
                        break;
                    }
                    selectElms.each(function() {
                        var selectElm = $(this);
                        if(selectElm.attr('name').split('.').pop() !== 'result') {
                            return;
                        }
                        selectElm.children('option').each(function(){
                            $(this).text(getResultTextFromId($(this).attr('value')));
                        });
                    });
                    i++;
                }

            }
        });
    });
