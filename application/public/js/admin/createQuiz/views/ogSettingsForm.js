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
                console.log("initialize a OgSettingsForm ItemView");
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

            onBeforeRender: function(){
                this.updateOgImagesProperties();
            },
            /* on render callback */
            onRender: function() {
                var self = this;
                var ogImagesSchema = Schemas.ogImagesSchema.properties;
                var formOptions = getFormViewOptions(ogImagesSchema, {
                    events: {

                    }
                });
                _.findWhere(formOptions, {type: "actions"}).items = [
                    {
                        "type": "submit",
                        "title": 'Update OG images (draft)'
                    }
                ];
                self.ui.formContainer.find('.form, .form-results-box').html('');
                self.ui.form.jsonForm({
                    schema: ogImagesSchema,
                    form: formOptions,
                    value: QuizData.ogImages,
                    onSubmit: function (errors, values) {
                        if (errors) {
                            self.ui.formResultsBox.html('<p>Some error! Please check if you filled in the details correctly</p>');
                        }
                        else {
                            QuizData.ogImages = values;
                            self.trigger('form:submitted');
                            self.ui.formResultsBox.html('<div class="alert alert-success"><strong>Updated draft!</strong></div>');
                        }
                    }
                });
                if(!this.useOgForResults()) {
                    self.ui.form.append('<br><div class="alert alert-info">For quizzes with user profile images on results, no OG images for results are needed.</div>');
                }
            },
            templateHelpers: function(){
                var self = this;
                return {

                };
            },
            updateOgImagesProperties: function() {
                if(!this.useOgForResults()) {
                    //For quizzes with user images on results, no OG images for results are needed
                    return;
                }
                var resultItems = _.pluck(QuizData.results, 'id');
                _.each(resultItems, function(resultId){
                    var resultText = getResultTextFromId(resultId);
                        Schemas.ogImagesSchema.properties[resultId] = {
                        title: resultText + ' (1200 x 630) (Optional!)',
                        description: 'OG image for the result: "' + resultText + '" (Displayed on Facebook when a user shares his result page)',
                        type: 'image'
                    }
                });
            },
            useOgForResults: function () {
                if(AppMan.reqres.request('quiz').get('settings').addUserPicInResults) {
                    //For quizzes with user images on results, no OG images for results are needed
                    return false;
                }
                return true;
            }
        });
    });
