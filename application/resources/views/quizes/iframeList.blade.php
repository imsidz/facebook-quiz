<!DOCTYPE html>
<html lang="{{App::getLocale()}}">
<head>
	<meta charset="UTF-8">
	<title>{{__('quizzes')}}</title>
		@if(App::isLocal())
	<link href="{{ assetWithCacheBuster('/themes/modern/style.css')}}" rel="stylesheet">
	@else
	<link href="{{ assetWithCacheBuster('/themes/modern/style.min.css')}}" rel="stylesheet">
	@endif
	
	@if(App::isLocal())
    <link href="{{ assetWithCacheBuster('/css/main.css')}}" rel="stylesheet">
    @else
    <link href="{{ assetWithCacheBuster('/css/main.min.css')}}" rel="stylesheet">
    @endif

    <!-- Custom Fonts -->
    <link href="{{ assetWithCacheBuster('/font-awesome-4.1.0/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">
    
	<!-- jQuery Version 1.11.0 -->
   	@if(App::isLocal())
    <script src="{{ assetWithCacheBuster('bower_components/jquery/dist/jquery.min.js')}}"></script>
    @else
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    @endif
	<script src="{{ assetWithCacheBuster('bower_components/masonry/dist/masonry.pkgd.min.js') }}"></script>
	<script src="{{ assetWithCacheBuster('bower_components/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>

	<style>
		.quiz-item{
			max-width: 360px !important;
		}
	</style>
</head>
<body>
<script>
	$(function(){
		var $container = $('.quiz-items-row'),
			$body = $('body'),
            $document = $(document);
		function informHeightChange(){
			var message = {
					type: "quizzes-iframe-height-change", 
				  	height: $document.height(),
					element: '{{Input::get('elm')}}'
			  };
			parent.postMessage(JSON.stringify(message), '*');
		}
		imagesLoaded($container, function(){
			  masonry = new Masonry( $container[0], {
				  itemSelector: '.quiz-item'
				});
				masonry.on( 'layoutComplete', function( msnryInstance, laidOutItems ) {
					informHeightChange();
				});
			informHeightChange();
		});
	});
</script>
	<div class="container">
		<div class="row">
		<div class="col-md-12">
			@include('quizes/quizesList')
		</div>
		<script>
			$('.quiz-item a').attr('target', "_blank");
		</script>
	</div>
	</div>
</body>
</html>
