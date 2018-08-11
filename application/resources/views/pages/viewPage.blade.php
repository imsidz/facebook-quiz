@extends('layout')


@section('content')

	<h1 class="page-header">{{ $page->title }}</h1>
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			{!! $page->content !!}
		</div>
	</div>


@stop