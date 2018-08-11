<div class="footer">
	&copy; {{\Helpers::getSiteName()}}
	{{do_action_ref_array('show_widgets', ['footer', &$widgets])}}
	@if(!empty($widgets['footer']))
		@foreach($widgets['footer'] as $widget)
            {!! do_shortcode($widget['content']) !!}
		@endforeach
	@endif

</div>