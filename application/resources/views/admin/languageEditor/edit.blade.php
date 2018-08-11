@extends('admin.layout')

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
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="" id="languageEditorPanel">

                    </div>
                    <div class="btn btn-success btn-lg save-changes-btn">Save changes</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var languageItemSchema = {!! $languagesSchema or 'null' !!};
        var languagesData = {!! $languagesData or 'null' !!};
        var lastSavedLanguagesDataJson= JSON.stringify(languagesData);
    </script>
    <script id="languageItemTemplate" type="text/template">
        <div class="panel panel-default language-item" data-language-index="<%= index %>" data-language-id="<%= language.id %>">
            <div class="panel-heading clearfix">
                <div class="panel-title">
                    <strong><%= language.name || "NO TITLE" %><div class="btn btn-default btn-sm pull-right language-edit-toggle"><i class="fa fa-pencil"></i></div></strong>
                </div>
            </div>
            <div class="panel-body hidden">
                <form class="language-form" action=""></form>
            </div>
        </div>
    </script>
    <script>
        window.partial = function(which, data) {
            var tmpl = $('#' + which).html();
            return _.template(tmpl)(data);
        };
    </script>
    <script type="text/template" id="languagePanelTemplate">
        <% _.each(languages, function(language, index){ %>
        <%= partial('languageItemTemplate', {language: language, index: index}) %>

        <% }) %>
    </script>

    <script type="text/template" id="activeLanguageFormTemplate">
        <div class="form-group">
            <select name="" id="activeLanguageField" class="form-control">
                <% _.each(languages, function(language, index){ %>
                <option value="<%= language.code %>"><%= language.name %></option>
                <% }) %>
            </select>
        </div>
    </script>

    <script src="{{asset('js/admin/admin.js')}}"></script>

    <script src="{{asset('js/admin/languageEditor.js')}}"></script>

    <script>
        vent.on('languages-form-submitted', function(){
            $.post('{{ Request::url() }}', {
                languages: JSON.stringify(languagesData)
            }).success(function(res){
                lastSavedLanguagesDataJson= JSON.stringify(languagesData);
                dialogs.success('Languages Saved');
            }).fail(function(jqXhr){
                dialogs.error(jqXhr.responseText);
            });
        })
    </script>
@stop