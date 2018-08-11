@extends('admin/layout')

@section('header')
    <h1>
        Configure Plugin
            <small>{{$pluginName}}
    </h1>
@stop
@section('content')
    {!! $editorContent !!}
@stop