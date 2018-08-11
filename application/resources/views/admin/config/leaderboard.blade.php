@extends('admin/layout')

@section('content')
    <!-- Page Heading -->
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                Leaderboard Config
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">

        </div>
    </div>
    <!-- /.row -->
    <script>
        var leaderboardConfigSchema = {!! $leaderboardConfigSchema or 'null' !!};
        var leaderboardConfigData = {!! $leaderboardConfigData or 'null' !!};
    </script>
    <div class="row">
        <div class="col-md-10">
            <div class="panel panel-info">
                <div class="panel-heading">Leaderboard Configuration</div>
                <div class="panel-body">
                    <div class="" id="configFormContainer">
                        <form class="leaderboard-form-common" action="" id="configForm"></form>
                        <div class="form-results-box" id="configFormResult"></div>
                    </div>
                </div>
            </div>
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <div class="panel-title">
                        <i class="fa fa-lightbulb-o"></i> How to add leaderboard
                    </div>
                </div>
                <div class="panel-body">
                    <p>Add the short code [leaderboard] to any widget in the <a href="{{route('adminConfigWidgets')}}">widgets config page</a>.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="{{assetWithCacheBuster('js/admin/admin.js')}}"></script>

    <script>
        vent.on('config-form-submitted', function(){
            $.post('{{ route('adminConfigLeaderboard')}}', {
                leaderboardConfig: leaderboardConfigData
            }).success(function(res){
                if(res.success) {
                    dialogs.success('Config Saved');
                } else if(res.error) {
                    dialogs.error('Error occured\n' + res.error);
                } else {
                    dialogs.error('Some Error occured');
                }
            }).fail(function(res){
                dialogs.error(res.responseText);
            });
        })
    </script>

    <script src="{{assetWithCacheBuster('js/admin/leaderboardConfig.js')}}"></script>

@stop