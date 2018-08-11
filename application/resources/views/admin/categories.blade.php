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
    <br/>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">
                        Categories
                    </div>
                </div>
                <div class="panel-body">
                    <div>
                        <form class="form-inline">
                            <a class="btn btn-success" href="{{route('adminCategoriesAddEdit')}}"><i class="fa fa-plus"></i> Create category</a>
                            @if(isset($showLanguageFilter))
                                <div class="form-group">
                                    <select class="form-control" name="" id="languageFilter">
                                        <option value="">Filter by language</option>
                                        <option value="">All languages</option>
                                        @foreach($languages as $language)
                                            <option @if($filteredLanguage == $language['code']) selected="selected" @endif value="{{$language['code']}}">{{$language['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </form>
                    </div>
                    {!!  $grid  !!}
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#languageFilter').on('change', function() {
            var langauge = $(this).val();
            top.location.href = '{{route('adminCategories')}}?of-language=' + langauge;
        });
    </script>
@stop