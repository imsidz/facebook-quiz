<!DOCTYPE html>
<html lang="en">

<head>
@section('head')
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>{{\Helpers::getSiteName()}} Admin</title>

    <!-- Bootstrap Core CSS -->
    <link href="{{assetWithCacheBuster('/css/bootstrap.min.css')}}" rel="stylesheet">

    <!-- Custom CSS -->
    @if(App::isLocal())
    <link href="{{assetWithCacheBuster('/css/admin.css')}}" rel="stylesheet">
    @else
    <link href="{{assetWithCacheBuster('/css/admin.min.css')}}" rel="stylesheet">
    @endif

    <style>
        .dropdown img {
            max-width: 100%;
        }

        .modal {
            overflow: auto !important;
        }
    </style>
        <link rel="stylesheet" href="{{ assetWithCacheBuster('bower_components/animate.css/animate.min.css')}}">
    <!-- Custom Fonts -->
    <link href="{{assetWithCacheBuster('/font-awesome-4.4.0/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">

   	<!-- jQuery Version 1.11.0 -->
    <script src="{{ assetWithCacheBuster('bower_components/jquery/dist/jquery.min.js')}}"></script>
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <link rel="stylesheet" href="{{ assetWithCacheBuster('bower_components/jquery-ui/themes/smoothness/jquery-ui.css') }}" />

<!-- elFinder CSS (REQUIRED) -->
<link rel="stylesheet" type="text/css" href="{{assetWithCacheBuster('packages/ahmadazimi/laravel-media-manager/css/elfinder.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{assetWithCacheBuster('packages/ahmadazimi/laravel-media-manager/css/theme.css')}}">

{{--
<link rel="stylesheet" href="{{ assetWithCacheBuster('bower_components/summernote/dist/summernote.css')}}">
<link rel="stylesheet" href="{{ assetWithCacheBuster('bower_components/summernote/dist/summernote-bs3.css')}}">
--}}

<link rel="stylesheet" href="{{ assetWithCacheBuster('bower_components/jquery-toggles/css/toggles.css') }}">
<link rel="stylesheet" href="{{ assetWithCacheBuster('bower_components/jquery-toggles/css/themes/toggles-light.css') }}">
<link rel="stylesheet" href="{{ assetWithCacheBuster('css/bootstrap-stacked-tabs.css') }}">
        <link rel="stylesheet" href="{{assetWithCacheBuster('/bower_components/spectrum/spectrum.css')}}"/>

{{do_action('admin_print_head_styles')}}

<!-- elFinder JS (REQUIRED) -->
<script src="{{ assetWithCacheBuster('/packages/ahmadazimi/laravel-media-manager/js/elfinder.min.js') }}"></script>



<script src="{{ assetWithCacheBuster('bower_components/underscore/underscore.js')}}"></script>
<script src="{{ assetWithCacheBuster('bower_components/jsonform/lib/jsonform.js')}}"></script>
<script src="{{ assetWithCacheBuster('bower_components/jquery-ui/jquery-ui.js')}}"></script>
<script src="{{ assetWithCacheBuster('bower_components/handlebars/handlebars.min.js')}}"></script>
<script src="{{ assetWithCacheBuster('packages/ahmadazimi/laravel-media-manager/js/elfinder.min.js')}}"></script>
{{--<script src="{{ assetWithCacheBuster('bower_components/summernote/dist/summernote.js')}}"></script>--}}
<script src="{{ assetWithCacheBuster('bower_components/jquery-toggles/toggles.js')}}"></script>
<script src="{{ assetWithCacheBuster('bower_components/uri.js/src/URI.min.js')}}"></script>
<script src="{{ assetWithCacheBuster('bower_components/spinjs/spin.js')}}"></script>
<script src="{{assetWithCacheBuster('/bower_components/spectrum/spectrum.js')}}"></script>
<script src="{{assetWithCacheBuster('/js/plugins/tinymce/tinymce.min.js')}}"></script>

{{do_action('admin_print_head_scripts')}}

<script>
    /*
     Initializing bootbox to default alerts and all to be used before loading it
     */
    var bootbox = {
        alert: alert
    }
    window.dialogs = bootbox;
    dialogs.error = function(msg){
        alert(msg);
    }
    dialogs.success = function(msg){
        alert(msg);
    }

</script>

<style>
	
	.show-form-btn {
		display: none;
	}
	.form-hidden > * {
		display: none;
	}
	.form-hidden .show-form-btn {
		display: block;
	}

    .modal-backdrop {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1030;
        background-color: #000000;
    }
    .modal-dialog {
        margin-left: auto;
        margin-right: auto;
        width: auto;
        padding: 10px;
        z-index: 1050;
    }
	/* TO OVERRIDE BOOTSTRAP MODAL CSS CHANGES IN SUMMERNOTE*//*
	.modal-dialog {
		width: 80% !important;
	}
	
	.note-editor .note-editable {
		background: #fff;
	}*/
	/*First level sortable field groups*/
	._jsonform-array-ul.ui-sortable > li {
		background: #f8f8f8;
		border: solid 1px #ddd;
		padding: 30px;
		margin: 20px 0px;
	}
	/*Nested sortable field groups*/
	.controls .ui-sortable > li .ui-sortable > li {
		background: #fff;
	}
	.controls .tabbable .tab-pane > div {
		//background: #f8f8f8; padding: 20px;
		//margin-bottom: 20px;
	}
	.tabbable .nav.nav-tabs.ui-sortable:empty {
		display: none;
	}
</style>
   
    <script>
        var BASE_PATH = '{{ url('') }}';
        var ASSET_BASE_PATH = '{{ asset('') }}';
        var CONTENT_BASE_PATH = '{{ content_url('') }}/';
        window.asset = function(path) {
            path = path || '';
            return path.match(/^http[s]?:\/\/.*$/) ? path : ASSET_BASE_PATH + path;
        }
        window.contentUrl = function(path) {
            path = path || '';
            return path.match(/^http[s]?:\/\/.*$/) ? path : CONTENT_BASE_PATH + path;
        }
        var chooseImagePlaceholder = '{{ asset('images/choose-image.gif') }}';
		var mediaConnectorRoute = '{{ route('mediaConnector') }}';

        @if(!empty($categories))
        window.Categories = {!! json_encode($categories) !!};
        @endif

        @if(!empty($languages))
        window.Languages = {!! json_encode($languages) !!};
        @endif
	</script>

        <script>

            function getUrlParams() {
                var urlParams;
                var match,
                        pl     = /\+/g,  // Regex for replacing addition symbol with a space
                        search = /([^&=]+)=?([^&]*)/g,
                        decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); },
                        query  = window.location.search.substring(1);

                urlParams = {};
                while (match = search.exec(query))
                    urlParams[decode(match[1])] = decode(match[2]);
                return urlParams;
            }
            function getUrlWithParam(params) {
                var url = [location.protocol, '//', location.host, location.pathname].join('');
                var originalParams = getUrlParams();
                var params = $.extend(true, originalParams, params);
                return url + '?' + $.param(params);
            }
        </script>
@show

    {{do_action('admin_head')}}
</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{route('admin')}}">{{\Helpers::getSiteName()}} Admin panel</a>
            </div>
            <!-- Top Menu Items -->
            <ul class="nav navbar-right top-nav">
                <li style="margin-right: 10px;">
                    <a target="_blank" style="color: #ffffff; margin: 9px auto;padding: 5px 16px;" class="btn btn-success btn-block" href="{{route('home')}}">View Site</a>
                </li>
                <li >
                    <a href="{{ URL::route('adminLogout')}}"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                </li>
            </ul>
            @include('admin.partials.mainSidebar')
            <!-- /.navbar-collapse -->
        </nav>

        @section('belowNavbar')
        @show

        <div id="page-wrapper">

            <div class="container-fluid">

                <div class="page-header">
                    @yield('header')
                </div>

            @yield('content')
            
            <div id="mediaManagerModal" class="modal fade" style="z-index: 1050">
			  <div class="modal-dialog modal-lg" style="max-width: 800px;">
				<div class="modal-content">
				  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title">Choose a file</h4>
				  </div>
				  <div class="modal-body">
					<div id="elFinder"></div>
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				  </div>
				</div><!-- /.modal-content -->
			  </div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->
    @section('foot')
    <!-- Bootstrap Core JavaScript -->
    <script src="{{ assetWithCacheBuster('/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>

    <script src="{{ assetWithCacheBuster('/bower_components/bootbox/bootbox.js') }}"></script>
    <script>
        window.dialogs = bootbox;
        dialogs.error = function(msg, callback){
            callback = callback || function(){};
            msg = msg.replace('\n', '<br>');
            bootbox.alert('<br><div class="panel panel-danger"><div class="panel-heading">Error</div><div class="panel-body">'+ msg +'</div></div>', callback);
        }
        dialogs.success = function(msg, callback){
            callback = callback || function(){};
            msg = msg.replace('\n', '<br>');
            bootbox.alert('<br><div class="panel panel-success"><div class="panel-heading">Success</div><div class="panel-body">'+ msg +'</div></div>', callback);
        }
    </script>

    <script src="{{asset('/bower_components/sweetalert/dist/sweetalert.min.js')}}"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('/bower_components/sweetalert/dist/sweetalert.css')}}">

    <!-- Morris Charts JavaScript -->
    <link rel="stylesheet" href="{{ assetWithCacheBuster('/css/plugins/morris.css')}}"/>
    <script src="{{ assetWithCacheBuster('/js/plugins/morris/raphael.min.js')}}"></script>
    <script src="{{ assetWithCacheBuster('/js/plugins/morris/morris.min.js')}}"></script>

    <script>
        (function() {
            $('body').on('click', '[data-refer-shortcodes]', function(e) {
                window.open('{{route('adminShortCodes')}}');
                e.preventDefault();
            });
        })();
    </script>
    {{do_action('admin_print_foot_scripts')}}

    @include('admin.partials.dialogs')
    @show

    {{do_action('admin_foot')}}
</body>

</html>
