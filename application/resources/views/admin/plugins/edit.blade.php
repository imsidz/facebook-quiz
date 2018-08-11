@extends('admin/layout')

@section('head')
    @parent
    <link rel="stylesheet" href="{{asset('bower_components/gridstack/dist/gridstack.min.css')}}"/>
    <style>
        .grid-stack-item-content {
            background-color: #fff;
        }
        .grid-editor {
            padding: 60px 30px;
            background: #333333;
        }

        .grid-editor-canvas {
            border: dashed 2px #666;
            padding: 10px 0px;
            max-width: 1200px;
            margin: 0px auto;
        }

        .no-posts-yet-error {
            margin: 20px 30px;
        }
        .featured-post-item {
            position: absolute;
            left: 0px;
            right: 0px;
            top: 0px;
            bottom: 0px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .grid-item-actions-panel {
            position: absolute;
            top: 0;
            right: 10px;
            padding: 5px;
            z-index: 1 !important;
        }
        .temp-item-film {
            position: absolute;
            top: 0px;
            left: 0px;
            padding: 1px 4px;
            background-color: #FF4319;
            font-weight: bold;
        }
        .featured-post-item.temp-item {
            opacity: .6;
        }
    </style>
@stop
@section('header')
    <h1>
        Edit Featured posts
    </h1>
@stop
@section('content')

<div class="row">
	<div class="col-md-12">
		<div class="box box-info">
			<div class="box-body">
				<h4>{{$featuredPosts->getName()}}</h4>
                <p>You can add posts to this featured list from <a href="{{route('adminViewPosts')}}">the posts page here.</a></p>
                @if(!empty($postToAdd))
                    <h3>Adding post: <small>{{$postToAdd->title}}</small></h3>
                    <div class="alert alert-warning">
                        Click "Save these posts" button to save the post to featured posts list. You can resize/reposition it before saving.
                    </div>
                @endif

                <div class="grid-editor">
                    <div class="grid-editor-canvas">
                        <div class="no-posts-yet-error hide">
                            <div class="alert alert-warning">
                                <i class="fa fa-2x fa-lightbulb-o"></i> &nbsp; No Posts in this featured list yet
                            </div>
                            <div>You can add posts to this featured list from <a href="{{route('adminViewPosts')}}">the posts page here.</a></div>
                        </div>
                        <div class="grid-stack clearfix featured-posts">

                        </div>
                    </div>
                    <div class="clearfix grid-actions">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Save these featured posts & the layout</h4>
                                <div class="btn btn-success btn-lg save-posts-btn">
                                    Save these posts
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4>Use a preset layout</h4>
                                <ul class="list-unstyled list-inline presets-block">
                                    <li>
                                        <div class="btn btn-default preset-item-btn" data-preset-default="[3,2]" data-preset-grid="[[5,3], [4,3], [3,3], [5,2], [4,2], [3,2]]">1</div>
                                    </li>
                                    <li>
                                        <div class="btn btn-default preset-item-btn" data-preset-default="[4,2]" data-preset-grid="[[6,2], [6,2], [5,3], [4,3], [3,3]]">2</div>
                                    </li>
                                    <li>
                                        <div class="btn btn-default preset-item-btn" data-preset-default="[3,2]" data-preset-grid="[[4,3], [4,3], [4,3], [6,2], [6,2]]">3</div>
                                    </li>
                                    <li>
                                        <div class="btn btn-default preset-item-btn" data-preset-default="[4,2]" data-preset-grid="[[6,3], [3,3], [3,3], [3,2], [3,2], [6,2]]">4</div>
                                    </li>
                                    <li>
                                        <div class="btn btn-default preset-item-btn" data-preset-default="[3,2]" data-preset-grid="[[3,2], [6,2], [3,2], [3,2], [3,2], [3,2], [3,2]]">5</div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</div>
	</div>
</div>


<script id="itemContentTemplate" type="text/template">
    <div class="featured-post-item <% if(typeof temporary != "undefined" && temporary) { print('temp-item'); } %>" style="background-image: url(<%= asset(_.escape(image)) %>);">
        <div class="text-gradient-bg-black featured-post-item-title-container">
            <div class="featured-post-item-title"><%= _.escape(title) %></div>
            <div class="featured-post-item-description"><%= _.escape(description) %></div>
        </div>
    </div>
    <% if(typeof temporary != "undefined" && temporary) { %>
        <div class="temp-item-film">
            Not saved Yet!
        </div>
    <% } %>
</script>

@stop

@section('foot')
    @parent
    <script src="{{asset('js/admin/admin.js')}}"></script>
    <script src="{{asset('bower_components/gridstack/dist/gridstack.min.js')}}"></script>
    <script src="{{asset('js/admin/featured-posts-editor.js')}}"></script>

    @if(\Session::has('featuredPostsJustSaved'))
        <script>
            dialogs.success('Featured posts saved!');
        </script>
    @endif

    <script>
        var featuredPosts = {!! json_encode($featuredPostsItems) !!};

        var savedGridData = {!! !empty($featuredPostsGridData ) ? json_encode($featuredPostsGridData)  : '{}' !!};
        @if(!empty($postToAdd))
            var postToAdd = {!! json_encode($postToAdd->toArray()) !!};
            window.notSavedYet = true;
        @else
            var postToAdd = undefined;
        @endif

        function getGridItems() {
            var gridItems = savedGridData.items || [];
            if(postToAdd) {
                gridItems.push({
                    itemId : postToAdd.id,
                    auto:true,
                    temporary: true
                });
            }
            return gridItems;
        }

        (function() {
            var gridItems = getGridItems();
            if(!gridItems.length) {
                $('.no-posts-yet-error').removeClass('hide');
            }
            var featuredPostsEditor =	new FeaturedPostsEditor($('.grid-editor'), gridItems, {
                gridOptions: {
                    vertical_margin: 6
                },
                getItemDataHandler: function(item) {
                    var matchedItem = _.findWhere(featuredPosts, {id : item['itemId']});
                    if(item.temporary)
                        matchedItem.temporary = true;
                    return matchedItem;
                },
                gridItemTemplate: _.template($('#itemContentTemplate').html()),
                saveGridHandler: function (gridData) {
                    _.each(gridData, function(gridItem){
                        gridItem.temporary && delete gridItem.temporary;
                        gridItem.auto && delete gridItem.auto;
                    });
                    var saveEndPoint = '{{action('AdminFeaturedPostsController@postUpdate', [$featuredPostsKey])}}';
                    var editPageUrl = '{{action('AdminFeaturedPostsController@getEdit', [$featuredPostsKey])}}';
                    $.post(saveEndPoint, {
                        'gridData'  :   JSON.stringify(gridData)
                    }).success(function() {
                        window.notSavedYet = false;
                        window.location.href = editPageUrl;
                    }).fail(function(jqXhr) {
                        dialogs.error(jqXhr.responseText);
                    });
                },
                confirmDelete: function(callback) {
                    dialogs({
                        title: "Are you sure?",
                        text: "Are you sure to delete this post from the featured list?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "danger",
                        confirmButtonText: "Yes! Delete it!",
                        closeOnConfirm: true
                    }, function() {
                        window.notSavedYet = true;
                        callback();
                    });
                }
            });

            featuredPostsEditor.render();

            window.featuredPostsEditor = featuredPostsEditor;

        })();
    </script>

    <script>
        $(window).on('beforeunload', function(){
            if(window.notSavedYet)
                return "Changes to Featured posts are not saved! If you would like to save them, stay on this page.";
        });
    </script>
@stop