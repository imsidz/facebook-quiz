{{do_action_ref_array('show_widgets', ['sideBar', &$widgets])}}
@if(!empty($widgets['sideBar']))
	@foreach($widgets['sideBar'] as $widget)
		<div class="sidebar-item">
            {!! do_shortcode($widget['content']) !!}
		</div>
	@endforeach
@endif

{{--
@if(@$currentPage == 'viewQuiz')
@include('quizes/quizesList')
@endif
--}}