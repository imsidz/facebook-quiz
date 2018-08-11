<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>{{\Jitheshgopan\AppInstaller\Installer::trans('installer.installerName')}}</title>

    <!-- Bootstrap Core CSS -->
    <link href="{{\Jitheshgopan\AppInstaller\Installer::asset('css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{\Jitheshgopan\AppInstaller\Installer::asset('css/styles.css')}}" rel="stylesheet">
    <link href="{{\Jitheshgopan\AppInstaller\Installer::asset('css/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{\Jitheshgopan\AppInstaller\Installer::asset('css/animate.css')}}" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            padding-top: 70px;
            /* Required padding for .navbar-fixed-top. Remove if using .navbar-static-top. Change if height of navigation changes. */
        }
    </style>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="{{\Jitheshgopan\AppInstaller\Installer::asset('js/jquery-1.11.0.js')}}"></script>
    <script src="{{\Jitheshgopan\AppInstaller\Installer::asset('js/underscore-min.js')}}"></script>
    <script src="{{\Jitheshgopan\AppInstaller\Installer::asset('js/jsonform.js')}}"></script>
</head>

<body>

<!-- Navigation -->
<nav class="navbar navbar-fixed-top header">
    <div class="col-md-12">
        <div class="navbar-header">

            <a href="#" class="navbar-brand">{{\Jitheshgopan\AppInstaller\Installer::trans('installer.installerName')}}</a>
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse1">
                <i class="glyphicon glyphicon-search"></i>
            </button>

        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse1">

        </div>
    </div>
</nav>


<!-- Page Content -->
<div class="container" id="main">

    <div class="row">
        <div class="col-md-12">
            <div class="text-center page-heading-block">
                <h2>{{\Jitheshgopan\AppInstaller\Installer::trans('installer.pageHeading')}}</h2>
                <p>{{\Jitheshgopan\AppInstaller\Installer::trans('installer.pageDescription')}}</p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2 main-canvas-block">
            <div class="row">
                <div class="col-md-4">
                    @include(\Jitheshgopan\AppInstaller\Installer::view('stagesNav'))
                </div>
                <div class="col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            @yield('stageContent')
                        </div>
                        {{--<div class="panel-footer">

                        </div>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->

</div>
<!-- /.container -->

<div class="footer text-center">
    <p><b>{{\Jitheshgopan\AppInstaller\Installer::trans('installer.footerMainText')}}</b></p>
    <small>{{\Jitheshgopan\AppInstaller\Installer::trans('installer.footerSubText')}}</small>
</div>

<!-- Bootstrap Core JavaScript -->
<script src="{{\Jitheshgopan\AppInstaller\Installer::asset('js/bootstrap.min.js')}}"></script>

</body>

</html>