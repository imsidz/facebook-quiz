@extends('layout')


@section('content')

{{do_action_ref_array('show_widgets', ['homeHeader', &$widgets])}}
@if(!empty($widgets['homeHeader']))
<div class="home-head-widget-section">
	@foreach($widgets['homeHeader'] as $widget)
		{!! do_shortcode($widget['content']) !!}
	@endforeach
</div>
@endif

@include('quizes/quizesList', ['paginate' =>  true])

{{do_action_ref_array('show_widgets', ['homeFooter', &$widgets])}}
@if(!empty($widgets['homeFooter']))
<div class="home-foot-widget-section">
	@foreach($widgets['homeFooter'] as $widget)
        {!! do_shortcode($widget['content']) !!}
	@endforeach
</div>
@endif

@stop


@section('foot')
@parent
<script src="{{ assetWithCacheBuster('bower_components/masonry/dist/masonry.pkgd.min.js') }}"></script>
<script src="{{ assetWithCacheBuster('bower_components/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
<script>
	$(function(){
		var $container = $('.quiz-items-row');
		imagesLoaded($container, function(){
			  $container.masonry( $container[0], {
				  itemSelector: '.quiz-item'
				});
		});
	});
</script>
@stop
