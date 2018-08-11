function renderMainConfigForm() {
	vent.trigger('mainConfigFormForm:beforeRender');
	var formOptions = getFormViewOptions(configMainSchema.properties, {
		events: {
			results: {
				onChange: function(e, node){

				},
				onInsert: function(e, node){

				}
			}
		},
		formOptions: {

			"social" : {
				htmlClass: 'form-section'
			},
			"ogData" : {
				htmlClass: 'form-section'
			},
			"customCode" : {
				htmlClass: 'form-section'
			},
			"navbarColor": {
				htmlClass: "color-block"
			},
			"mainBtnColor": {
				htmlClass: "color-block"
			},
			"linkColor": {
				htmlClass: "color-block"
			},
			"favicon": {
				htmlClass: "favicon-field"
			}
		}
	});

	console.log(formOptions);

	$('#mainConfigFormContainer').find('#mainConfigForm, .form-results-box').html('');
	$('#mainConfigForm').jsonForm({
		schema: configMainSchema.properties,
		form: formOptions,
		value: mainConfigData,
		onSubmit: function (errors, values) {
		  if (errors) {
			$('#mainConfigFormResult').html('<p>I beg your pardon?</p>');
		  }
		  else {
			  mainConfigData = values;
			  vent.trigger('hideForm', 'mainConfigForm');
			  console.log('QuizData after adding mainConfig: ', mainConfigData);
			  console.log(values);
		  }
			vent.trigger('config-form-submitted');
		}
	});
}

renderMainConfigForm();