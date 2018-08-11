@extends('admin/layout')

@section('content')
	<!-- Page Heading -->
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">
				@if($creationMode) Create a @elseif($editingMode) Edit @endif
				page</h1>

		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">

		</div>
	</div>
	<!-- /.row -->
	<script>
		var pageSchema = {!! $pageSchema or 'null' !!};
		var origPageData, pageData;
		origPageData = pageData = {!! $pageData or 'null' !!};
	</script>
	<div class="row">
		<div class="col-md-10">
			<div class="panel panel-info">
				<div class="panel-heading">Page details</div>
				<div class="panel-body">
					<div class="" id="pageFormContainer">
						<form class="page-form-common" action="" id="pageForm"></form>
						<div class="form-results-box" id="pageFormResult"></div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="{{assetWithCacheBuster('js/admin/admin.js')}}"></script>

	<script>
		vent.on('page-form-submitted', function(){
			$.post(BASE_PATH + '/admin/pages/create', {
				pageId: pageData.id || undefined,
				page: pageData
			}).success(function(res){
				if(res.success) {
					dialogs.success('Page Saved', function(){
						window.location.href = '{{route('adminViewPages')}}';
					});
				} else if(res.error) {
					dialogs.error('Error occured\n' + res.error);
				} else {
					dialogs.error('Some Error occured');
				}
			}).fail(function(res){
				dialogs.error(res.responseText);
			});
		})
	</script>

	<script>
		function renderPageForm() {
			vent.trigger('pageForm:beforeRender');
			var formOptions = getFormViewOptions(pageSchema, {
				events: {
					results: {
						onChange: function(e, node){

						},
						onInsert: function(e, node){

						}
					}
				},
				formOptions: {
					"ogData": {
						htmlClass: "form-section"
					}
				}
			});

			$('#pageFormContainer').find('#pageForm, .form-results-box').html('');
			$('#pageForm').jsonForm({
				schema: pageSchema,
				form: formOptions,
				value: pageData,
				onSubmit: function (errors, values) {
					if (errors) {
						$('#pageFormResult').html('<p>I beg your pardon?</p>');
					}
					else {
						pageData = values;
						if(origPageData.id) {
							pageData.id = origPageData.id;
						}
						vent.trigger('hideForm', 'pageForm');
					}
					vent.trigger('page-form-submitted');
				}
			});
		}

		renderPageForm();
		function convertToSlug(Text)
		{
			return Text
					.toLowerCase()
					.replace(/[^\w ]+/g,'')
					.replace(/ +/g,'-')
					;
		}
		@if($creationMode)
            $('[name="title"]').change(function(){
			var title = $(this).val();
			var titleUrlString = convertToSlug(title);
			$('[name="urlString"]').val(titleUrlString);
		});
		@endif
	</script>

@stop