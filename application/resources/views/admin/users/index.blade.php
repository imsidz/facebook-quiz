@extends('admin/layout')


@section('content')
	<h1>All users</h1>
	<hr/>
	<div class="row">
		@include('admin.users.partials.userDownloadOptions')
	</div>
	@include('admin.users.partials.userList')
@stop