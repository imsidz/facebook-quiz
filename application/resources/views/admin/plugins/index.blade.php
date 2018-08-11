@extends('admin/layout')

@section('header')
    <h1>
        Plugins
            <p>
                <small>This page lets you manage plugins</small>
            </p>
    </h1>
@stop
@section('content')

    <style>
        .plugins-preview-carousel .preview-placeholder, .plugins-preview-carousel .carousel-inner>.item {
            height: 150px;
            text-align:center;
            background-position: center;
            background-size: cover;
        }

        .plugins-preview-carousel .preview-placeholder .fa {
            margin-top: 45px;
            color: #dddddd;
        }

        .plugin-stats {
            font-size: large;
            font-weight: bold;
        }
        .plugin-box > .box-body {
            padding: 0px;
        }
        .plugin-title {
            margin-top: 5px;
        }
        .plugin-details {
            padding: 5px 10px;
        }
        .plugin-details p:last-child {

        }
        .plugin-links {
            padding: 5px 10px;
            background-color: #f8f8f8;
            border-top: solid 1px #eee;
        }
        .plugin-link {
            margin-left: 3px;
            padding-left: 5px;
            border-left: solid 1px #ccc;
        }
        .plugin-link:first-child {
            border-left: 0px;
            padding-left: 0px;
        }
        .plugin-enable-toggle {
            width: 80px;
        }
        .plugin-status-legend {
            margin: 0px;
        }
        .plugin-activation-container {
            padding: 10px 0px;
            margin-top: -10px;
            margin-bottom: 10px;
            border-bottom: solid 1px #eeeeee;
        }
    </style>
<div class="row">
	<div class="col-md-12">
        <div class="row">
            @forelse($plugins as $plugin)
                @include('admin.plugins.partials.pluginItem', ['slug'   =>  $plugin['slug'], 'plugin' => $plugin])
            @empty
                <div class="col-md-12">
                    <div class="text-center" style="border: dashed 2px #ccc; margin: 30px;">
                        <br/>
                        <p><i class="fa fa-info-circle fa-3x text-muted"></i></p>
                        <p><b>No plugins uploaded yet.</b></p>
                        <br/>
                    </div>
                </div>
            @endforelse
        </div>
	</div>
</div>

    <script>
        (function() {
            var pluginToggleSettings = {text: {on: 'Enabled', off: 'Disabled'}};
            $('.plugin-enable-toggle').data('toggle-settings', JSON.stringify(pluginToggleSettings));
        })();
    </script>
<script src="{{asset('js/admin/admin.js')}}"></script>

<script>
    $(function() {
        $('.plugins-preview-carousel').carousel();
    });
</script>

    <script>
        //Plugin Installer
        $(function() {
            var installButtons = $('.plugin-install-btn');
            installButtons.on('click', function(e) {
                var installEndPoint = $(this).data('end-point');
                dialogs.loading("Installing")
                $.post(installEndPoint).success(function () {
                    window.location.href = window.location.href;
                }).fail(function(jqXhr){
                    dialogs.error(jqXhr.responseText);
                })
            });
        });

        //Plugin Uninstaller
        $(function() {
            var uninstallButtons = $('.plugin-uninstall-btn');
            uninstallButtons.on('click', function(e) {
                var uninstallEndPoint = $(this).data('end-point');
                dialogs.loading("Uninstalling")
                $.post(uninstallEndPoint).success(function () {
                    window.location.href = window.location.href;
                }).fail(function(jqXhr){
                    dialogs.error(jqXhr.responseText);
                })
            });
        });

        @if(Session::has('plugin-install-success'))
            $(function() {
                dialogs.success("Installed successfully. You can now enable it.");
            });
        @endif

        @if(Session::has('plugin-uninstall-success'))
            $(function() {
                dialogs.success("Uninstalled successfully.");
            });
        @endif

    </script>

@stop