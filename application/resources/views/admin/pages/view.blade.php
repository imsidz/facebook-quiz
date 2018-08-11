@extends('admin/layout')


@section('content')
    <h1 class="page-header">Viewing pages</h1>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">All pages</div>
                </div>
                <div class="panel-body">
                    <div style="margin-bottom: 10px;"><a class="btn btn-success" href="{{route('adminCreatePage')}}"><i class="fa fa-plus"></i> Create new Page</a></div>
                    <ul class="list-group">
                        @forelse ($pages as $page)

                            <li class="list-group-item clearfix">
                                <a class="pull-left"
                                   href="{{ route('viewPage', array('nameString' => $page->urlString))}}"><b>{{ $page->title }}</b></a>

                                <div class="pull-right"><a
                                            href="{{ route('adminCreatePage', array('action' => 'edit', 'pageId' => $page->id)) }}"
                                            class="btn btn-primary" role="button">Edit</a> <a
                                            href="{{ route('adminDeletePage') . '?pageId=' . $page->id }}"
                                            class="btn btn-danger" role="button">Delete</a>
                                </div>
                            </li>
                        @empty
                            <li>No quizzes yet!</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

@stop