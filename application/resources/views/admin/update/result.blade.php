@extends('admin/layout')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>Installing updates</h1>
            <br/>
            @if(!empty($error))
                <div class="alert alert-danger">
                    <b>{{ $message }}</b>
                </div>
                <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        @include('admin.update.partials.logs')
                    </div>
                </div>
                <br><br>
            @else
                <div class="text-center">
                    <i class="fa fa-5x fa-check-circle text-success text-green"></i>
                    <h3>{{ $message }}</h3>
                    <p>Current version: <b>{{ $updateVersion }}</b></p>
                </div>
                <br><br>
            @endif
        </div>
    </div>
@stop