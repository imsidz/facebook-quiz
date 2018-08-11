@extends('admin/layout')

@section('content')
    <style>
        #languagePlacementsContainer img {
            max-width: 100%;
        }
    </style>
    <!-- Page Heading -->
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                Languages
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div id="languageEditorPanel"></div>
            <br/>
            <div id="activeLanguageFormContainer" class="alert alert-success"></div>
            <br/><div class="btn btn-success btn-lg save-changes-btn">Save changes</div>
            <br/><br/><br/>
        </div>
    </div>
    <!-- /.row -->
    <script>
        var languagesSchema = {!! $languagesSchema or 'null' !!};
        var languageItemSchema = languagesSchema.items.properties;
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
        <div class="btn btn-default add-new-language-btn"><i class="fa fa-plus"></i> Add new language</div>
    </script>

    <script type="text/template" id="activeLanguageFormTemplate">
        <div class="form-group">
            <label for="">Active language</label>
            <select name="" id="activeLanguageField" class="form-control">
                <% _.each(languages, function(language, index){ %>
                    <option value="<%= language.code %>"><%= language.name %></option>
                <% }) %>
            </select>
        </div>
    </script>

    <script src="{{assetWithCacheBuster('js/admin/admin.js')}}"></script>

    <script src="{{assetWithCacheBuster('js/admin/languages.js')}}"></script>

@stop