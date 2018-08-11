@extends('admin/layout')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if(!empty($error))
                <h1>Oops!</h1>
                <div class="alert alert-danger">
                    <b>{{ $message }}</b>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        @include('admin.update.partials.logs')
                    </div>
                </div>
            @else
                <div class="text-center">
                    <h2>{{ $message }}<span class="text-success">@if($updateAvailable) : {{$updateVersion}} @endif</span></h2>
                </div>
                <br/>
                @if($updateAvailable)
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Update details</h3>
                                </div>
                                <div class="panel-body">
                                    @include('admin.update.partials.updateDetails', ['updateVersion' => $updateVersion])
                                </div>
                            </div>
                            <div class="text-center">
                                <a href="{{ route('doUpdate') }}" class="btn btn-success btn-lg"><i class="fa fa-rocket"></i> &nbsp;Update Now!</a>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
@stop