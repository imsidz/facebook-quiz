define([
        'backbone',
        'hbs!templates/questionItemEditor',
        'appMan',
        'collections/choices',
        'views/choicesList'
    ],
    function( Backbone, QuestionItemEditorTmpl, AppMan, ChoicesCollection, ChoicesListView) {
        'use strict';

        /* Return a ItemView class definition */
        return Backbone.Marionette.ItemView.extend({

            initialize: function() {
                console.log("initialize a QuestionItemEditor ItemView");
            },

            template: QuestionItemEditorTmpl,
            className: '',

            /* ui selector cache */
            ui: {
                form: '.form',
                formResultsBox: '.form-results-box',
                'choicesEditor' : '.choices-editor'
            },

            /* Ui events hash */
            events: {
            },

            /* on render callback */
            onRender: function() {
                var self = this;
                self.$('.form, .form-results-box').html('');
                var questionSchema = _.clone(getQuestionSchema());
                delete questionSchema.choices;
                setTimeout(function() {
                    self.ui.form.jsonForm({
                        schema: questionSchema,
                        form: [
                            '*',
                            {
                                "type": "actions",
                                "items": [
                                    {
                                        "type": "submit",
                                        "title": 'Update question (draft)'
                                    },
                                    {
                                        type: "button",
                                        title: '<i class="fa fa-times"></i> Close',
                                        onClick: function (e) {
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
                                //values = addQuestionIds(values);
                                self.model.set(values);
                                self.ui.formResultsBox.html('<div class="alert alert-success"><strong>Updated draft!</strong></div>');
                                self.trigger('form:submitted');
                            }
                        }
                    });

                    //Move choices editor into the form for better access
                    self.$('.choices-editor-container').prependTo(self.$('.form-actions'));
                    //Render choices as editableCompositeView
                    var choices = self.model.get('choices') ? self.model.get('choices') : [];
                    choices = new ChoicesCollection(choices);
                    var choicesView = new ChoicesListView({
                        el: self.ui.choicesEditor,
                        collection: choices,
                        confirmDelete: function (callback) {
                            dialogs.confirm('Are you sure to delete it?', function (confirmed) {
                                callback(confirmed);
                            })
                        }
                    });
                    choicesView.render();
                    choices.on('change add remove', function () {
                        self.model.set('choices', choices.toJSON());
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
