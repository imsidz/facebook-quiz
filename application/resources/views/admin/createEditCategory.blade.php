@extends('admin/layout')

@section('head')
    @parent
    {!!  Rapyd::head()  !!}
    <style>
        .btn-toolbar h2 {
            margin-top: 0px;
        }
    </style>
@show

@section('content')
    <div class="row">
        <div class="col-md-6">
            <br/>
            {!!  $edit->header  !!}
            <div class="well">

                    {{ $edit->message }}

                    @if(!$edit->message)
                        Name: {!!  $edit->field('name')  !!}
                        <p class="bg-danger">{!!  $edit->field('name')->message  !!}</p>
                    @endif
                    @if(isset($hasLanguageField))
                        Language: {!! $edit->field('language') !!}
                        <p class="bg-danger">{!! $edit->field('language')->message !!}</p>
                    @endif

                {!!  $edit->footer  !!}
            </div>
            <div>
                <a class="btn btn-success btn-block" href="{{route('adminCategories')}}">View all categories</a>
            </div>
        </div>
    </div>
@stop