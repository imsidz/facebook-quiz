@extends('layout')
@section('content')
<div id="loginError" class="hide"></div>
<a id="loginWithFbBtn" class="btn btn-fb" data-action="loginWithFB" href="javascript:void(0);">{{__('loginWithFB')}}</a>
@stop