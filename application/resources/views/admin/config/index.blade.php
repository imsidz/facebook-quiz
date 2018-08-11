@extends('admin/layout')

@section('content')
	<style>
		.color-block{
			background: #f2f2f2;
			padding: 15px 15px;
			width: 30%;
			display: inline-block;
			margin: 10px 1% 10px 1%;
			border: solid 1px #e5e5e5;
		}
		.favicon-field .field-image-preview {
			width: 64px;
		}
	</style>
	<!-- Page Heading -->
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">
				Site Config
			</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">

		</div>
	</div>
	<!-- /.row -->
<script>
	var configMainSchema = {!! $configMainSchema or 'null'  !!};
	var mainConfigData = {!! $mainConfigData or 'null' !!};
</script>
<div class="row">
	<div class="col-md-10">
		<div class="panel panel-info">
			<div class="panel-heading">Main Configuration</div>
			<div class="panel-body">
				<div class="" id="mainConfigFormContainer">
					<form class="quiz-form-common" action="" id="mainConfigForm"></form>
					<div class="form-results-box" id="mainConfigFormResult"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="{{assetWithCacheBuster('js/admin/admin.js')}}"></script>

<script>
	vent.on('config-form-submitted', function(){
		$.post(BASE_PATH + '/admin/config', {
			config: mainConfigData
		}).success(function(res){
			if(res.success) {
				dialogs.success('Config Saved');
			} else if(res.error) {
				dialogs.error('Error occured\n' + res.error);
			} else {
				dialogs.error('Some Error occured\n' + res);
			}
		}).fail(function(res){
			dialogs.error(res.responseText);
		});
	})
</script>

<script src="{{assetWithCacheBuster('js/admin/config.js')}}"></script>

@stop