@extends('admin/layout')

@section('content')
	<!-- Page Heading -->
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">
				Quiz Config
			</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">

		</div>
	</div>
	<!-- /.row -->
<script>
	var quizConfigSchema = {!! $quizConfigSchema or 'null' !!};
	var quizConfigData = {!! $quizConfigData or 'null' !!};
</script>
<div class="row">
	<div class="col-md-10">
		<div class="panel panel-info">
			<div class="panel-heading">Quiz Configuration</div>
			<div class="panel-body">
				<div class="" id="configFormContainer">
					<form class="quiz-form-common" action="" id="configForm"></form>
					<div class="form-results-box" id="configFormResult"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="{{assetWithCacheBuster('js/admin/admin.js')}}"></script>

<script>
	vent.on('config-form-submitted', function(){
		$.post('{{ route('adminConfigQuiz')}}', {
			quizConfig: quizConfigData
		}).success(function(res){
			if(res.success) {
				dialogs.success('Config Saved');
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

<script src="{{assetWithCacheBuster('js/admin/quizConfig.js')}}"></script>

@stop