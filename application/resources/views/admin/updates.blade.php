@extends('admin/layout')

@section('content')
    <br/>
    <div class="row">
        <div class="col-md-6">
            <h1>Updates</h1>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">
                        Choose the correct version of update to run the updater.
                    </div>
                </div>
                <div class="panel-body">
                    <ol class="list-unstyled">
                        @foreach($updateDirs as $updateDir)
                            <li>
                                <a href="{{route('update', ['update-path'   =>  $updateDir])}}">{{basename($updateDir)}}</a>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop