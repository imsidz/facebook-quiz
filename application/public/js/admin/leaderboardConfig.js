
function renderConfigForm() {
	vent.trigger('configFormForm:beforeRender');
	var formOptions = getFormViewOptions(leaderboardConfigSchema
		, {
		formOptions: {

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
			leaderboardConfigSchema
		,
		form: formOptions,
		value: leaderboardConfigData,
		onSubmit: function (errors, values) {
		  if (errors) {
			$('#configFormResult').html('<p>I beg your pardon?</p>');
		  }
		  else {
			  leaderboardConfigData = values;
			  vent.trigger('hideForm', 'configForm');
		  }
			vent.trigger('config-form-submitted');
		}
	});
}

renderConfigForm();