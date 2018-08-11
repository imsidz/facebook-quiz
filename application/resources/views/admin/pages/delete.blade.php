@extends('admin/layout')


@section('content')

<h1 class="page-header">Delete page</h1>
@if(@$getConfirmation)
	Are you sure to delete "{{ $page['title'] }}" page?
	<br><br>
	<form action="" method="POST">
		<input type="submit" class="btn btn-danger" value="Yes">
		<a href="{{ url('/admin/pages/view') }}" class="btn btn-default">No</a>
	</form>
	<br><br>
@endif

@if(@$deleteSuccess)
	Page "{{ $page['title'] }}" successfully deleted
	<br><br><a href="{{ url('/admin/pages/view') }}" class="btn btn-default">Okay</a>
	<br><br>
@endif
	
@stop