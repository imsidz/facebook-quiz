define([
        'backbone',
        'hbs!templates/form',
        'appMan'
    ],
    function( Backbone, FormTmpl, AppMan) {
        'use strict';

        /* Return a ItemView class definition */
        return Backbone.Marionette.ItemView.extend({

            initialize: function() {
                console.log("initialize a DetailsForm ItemView");
            },

            template: FormTmpl,

            /* ui selector cache */
            ui: {
                formContainer: '.form-container',
                form: '.form',
                formResultsBox: '.form-results-box'
            },

            /* Ui events hash */
            events: {
            },

            /* on render callback */
            onRender: function() {
                var self = this;
                var categoriesTitleMap = this.populateCategories();
                var formOptions = {
                    "settings": {
                        "type": "fieldset",
                        "title": "Settings",
                        "expandable": true,
                        "htmlClass": "panel-warning"
                    },
                    "category": {
                        titleMap : categoriesTitleMap
                    }
                }

                window.beforeRenderJsonForm('detailsForm', Schemas.quizSchema, formOptions);

                formOptions = getFormViewOptions(Schemas.quizSchema, {
                    exclude: ['questions'],
                    formOptions: formOptions,
                    events: {
                        type: {
                            titleMap: {
                                "prediction": "Prediction / Discovery",
                                "scoreBased": "Score based : Typical Q/A",
                                "random": "Random"
                            }
                        }
                    }
                });

                _.findWhere(formOptions, {type: "actions"}).items = [
                    {
                        "type": "submit",
                        "title": 'Update Basic details (draft)'
                    }
                ];

                self.ui.formContainer.find('.form, .form-results-box').html('');
                self.ui.form.jsonForm({
                    schema: Schemas.quizSchema,
                    form: formOptions,
                    value: QuizData,
                    onSubmit: function (errors, values) {
                        if (errors) {
                            self.ui.formResultsBox.html('<p>Some error! Please check if you filled in the details correctly</p>');
                        }
                        else {
                            setQuizData(values, ['results', 'questions']);
                            console.log('QuizData after adding quiz details: ', QuizData);
                            vent.trigger('hideForm', 'quizForm');
                            vent.trigger('showForm', 'quizResultsForm');
                            self.trigger('form:submitted');
                            self.ui.formResultsBox.html('<div class="alert alert-success"><strong>Updated draft!</strong></div>');
                        }
                    }
                });
            },
            templateHelpers: function(){
                var self = this;
                return {

                };
            },
            //Add categories to 'category' field's enum
            populateCategories : function(){
                if(!window.Categories) {
                    return;
                }
                var categories = window.Categories;
                var titleMap = {};
                Schemas.quizSchema.category.enum = [];
                for(var i in categories) {
                    Schemas.quizSchema.category.enum.push(categories[i]['id']);
                    titleMap[categories[i]['id']] = categories[i]['name'];
                }
                return titleMap;
            },
            //Add languages to 'language' field's enum
            populateLanguages : function(){
                if(!window.Languages) {
                    return;
                }
                var languages = window.Languages;
                var titleMap = {};
                Schemas.quizSchema.language.enum = [];
                for(var i in languages) {
                    Schemas.quizSchema.language.enum.push(languages[i]['code']);
                    titleMap[languages[i]['code']] = languages[i]['name'];
                }
                return titleMap;
            }
        });
    });
