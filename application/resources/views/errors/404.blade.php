<?php
\App\Http\Middleware\Common::loadCommonData();
?>
@extends('layout')

@section('content')
    <div class="row" style="margin-bottom: 50px;">
        <div class="col-md-6 col-md-offset-3 text-center notFound-title-block">
            <br><br><br>
            <div style="font-size: 10em;"><strong>404</strong></div>
            <br><br><br>
            <h3>{{__('pageNotFound')}}</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-center"><strong>{{__('latestQuizzes')}}</strong></h3>
            @include('quizes/quizesList')
            @if(is_array($quizes) && count($quizes))
            <div class="text-center">
                <br/>
                <a href="{{route('quizes')}}" class="btn btn-primary"><span>{{__('viewMoreQuizzes')}}</span></a>
            </div>
            @endif
        </div>
    </div>
@stop