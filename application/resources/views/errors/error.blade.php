@extends('layout')

@section('content')

    <div class="panel panel-danger">
        <div class="panel-heading">
            {{__('someErrorOccured')}}
        </div>
        <div class="panel-body">
            <h3 class="text-danger">{{$title or __('someErrorOccured')}}</h3>
            <p>{{$message or " "}}</p>
        </div>
    </div>
@stop