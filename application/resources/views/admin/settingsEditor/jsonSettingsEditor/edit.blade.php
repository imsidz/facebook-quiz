@extends('admin/layout')

@section('header')
    <h1>
        {{$pageTitle}}
        <p>
            <small>{{$pageDescription}}</small>
        </p>
    </h1>
@stop

@section('content')
    <style>
        .jsonform-error-listImageTextFont .img-thumbnail {
            display: none;
        }
        .show-edit-list-notification-reference {
            cursor: pointer;
        }
    </style>

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-purple">
                <div class="panel-body">
                    <div class="" id="configFormContainer">
                        <form class="post-form-common" action="" id="configForm"></form>
                        <div class="form-results-box" id="configFormResult"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{asset('js/admin/admin.js')}}"></script>
    <script src="{{asset('js/admin/jsonSettingsEditor.js')}}"></script>

    <script>
        var configSchema = {!! $configSchema or 'null' !!};
        var configData = {!! $configData or 'null' !!};
        var settingsForm = new SettingsForm(configSchema, configData);

        //Render form
        settingsForm.render($('#configFormContainer'));
        settingsForm.on('submitted', function(e, settingsData) {
            $.post('{{ Request::url() }}', {
                settings: settingsData
            }).success(function(res){
                dialogs.success('Settings Saved');
            }).fail(function(jqXhr, res){
                dialogs.error(jqXhr.responseText);
            });
        });
    </script>
@stop