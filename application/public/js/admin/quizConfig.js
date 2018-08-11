
function renderConfigForm() {
	vent.trigger('configFormForm:beforeRender');
	var formOptions = getFormViewOptions(quizConfigSchema
		, {
		formOptions: {
			"forceLogin" : {
				titleMap : {
				"disabled" : "Disabled",
				"on-quiz-start" : "On quiz start",
				"before-result" : "Before showing result"
				}
			},
			"userPicXPos": {
				type : "number"
			},
			"userPicYPos": {
				type : "number"
			},
			"userPicSize": {
				type : "number"
			}
		},
		events: {
			results: {
				onChange: function(e, node){

				},
				onInsert: function(e, node){

				}
			}
		}
	});

	$('#configFormContainer').find('#configForm, .form-results-box').html('');
	$('#configForm').jsonForm({
		schema: 
			quizConfigSchema
		,
		form: formOptions,
		value: quizConfigData,
		onSubmit: function (errors, values) {
		  if (errors) {
			$('#configFormResult').html('<p>I beg your pardon?</p>');
		  }
		  else {
			  quizConfigData = values;
			  vent.trigger('hideForm', 'configForm');
		  }
			vent.trigger('config-form-submitted');
		}
	});
}

renderConfigForm();