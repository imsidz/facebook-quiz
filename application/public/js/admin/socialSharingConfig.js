
function renderConfigForm() {
	vent.trigger('configFormForm:beforeRender');

	$('#configFormContainer').find('.form-results-box').html('');

    function getActiveNetworks() {
        var networks = [];
        $('#activeNetworksSortable li').each(function(){
            networks.push($(this).data('network'));
        })
        return networks;
    }

	$('.save-config-btn').click(function() {
        socialSharingConfigData.sharingNetworks = getActiveNetworks();
		vent.trigger('config-form-submitted');
	});
}

renderConfigForm();