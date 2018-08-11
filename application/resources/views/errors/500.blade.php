<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    @if(App::isLocal())
        <link href="{{assetWithCacheBuster('/css/main.css')}}" rel="stylesheet">
    @else
        <link href="{{assetWithCacheBuster('/css/main.min.css')}}" rel="stylesheet">
    @endif

    @if(App::isLocal())
        <link href="{{assetWithCacheBuster('/themes/modern/style.css')}}" rel="stylesheet">
    @else
        <link href="{{assetWithCacheBuster('/themes/modern/style.min.css')}}" rel="stylesheet">
    @endif
    <link href="{{assetWithCacheBuster('/font-awesome-4.1.0/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
</head>
<body>
<div class="body_wrap @if(!empty($currentPage))page-{{$currentPage}}@endif">
    <div class="body-container container-fluid modern-touch" style="padding: 60px 30px;">
        <div class="row">
            <div class="col-md-12 text-center">
                <h1 class="text-danger">{{$title or "Some error occurred!"}}</h1>
                <p>{{$message or "Please contact admin or try again later"}}</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>