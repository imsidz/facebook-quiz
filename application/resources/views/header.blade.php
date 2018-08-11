<div class="row">
	<div class="col-sm-12">
		<!-- Website Menu -->
		<div id="topmenu" class="fixed">
			<ul class="dropdown clearfix boxed boxed-white">
			   <li class="menu-toggle-block clearfix">
					<a href="{{url('/')}}" class="pull-left" style="padding:0px;">
						@if(!empty($config['main']['logo']))
							<img src="{{content_url($config['main']['logo'])}}" alt="" style="height: 40px;vertical-align: middle;margin: 10px 20px;">
						@else
							<div style="height: 60px; line-height: 60px;padding: 0px 10px;font-weight: bold;font-size: medium;">{{\Helpers::getSiteName()}}</div>
						@endif
					</a>
					<a class="btn menu-toggle" onclick="$(this).parents('.dropdown').first().toggleClass('expanded')">
						<i class="menu-icon fa fa-bars fa-lg"></i>
					</a>
                   {!! do_action('navbar_after_mobile_items') !!}

			   </li>
				<li class="menu-level-0 hidden-xs">
					<a href="{{url('/')}}" style="outline: none;padding: 0px;">
						@if(!empty($config['main']['logo']))
							<img src="{{content_url($config['main']['logo'])}}" alt="" style="height: 40px;vertical-align: middle;margin: 10px 10px;">
						@else
							<div style="height: 60px; line-height: 60px;padding: 0px 20px;font-weight: bold;font-size: medium;">{{\Helpers::getSiteName()}}</div>
						@endif
</a>
				</li>
				<li class="menu-level-0"><a href="{{URL::route('quizes')}}" style="outline: none;"><span>{{__('latest')}}</span></a></li>
				<li class="menu-level-0"><a href="{{URL::route('popularQuizes')}}" style="outline: none;"><span>{{__('popular')}}</span></a></li>
                @if($categories->count())
                <li class="menu-level-0">
                    <a href="#" hidefocus="true" style="outline: none;"><span>{{__('categories')}}</span></a>
                    <ul class="submenu-1">
                        @foreach($categories as $category)
                            <li class="menu-level-1"><a href="{{route('category', array('category-slug' => $category->slug))}}" hidefocus="true" style="outline: none;">{{ htmlspecialchars($category->name) }}</a></li>
                        @endforeach
                    </ul>
                </li>
                @endif
                {!! do_action('navbar_after_link_items') !!}
                {{do_action_ref_array('show_widgets', ['navbarLinks', &$widgets])}}
				@if(!empty($widgets['navbarLinks']))
					@foreach($widgets['navbarLinks'] as $widget)
						<li class="menu-level-0">{!! do_shortcode($widget['content']) !!}</li>
					@endforeach
				@endif

				@if(@$config['main']['enableUserLogin'] && $config['main']['enableUserLogin'] != "false")
                    <li id="headerUserMenu" class="menu-level-0 @if(isRtl()) pull-left @else pull-right @endif hide">
                        <a id="headerUserLoginLink" href="{{ route('login')}}" style="outline: none;"><span>{{__('loginBtn')}}</span></a>
                        <a class="user-details" href="javascript:;" hidefocus="true" style="outline: none;">
                            <span class="star-icon"><img src="{{LeaderboardHelpers::getPointsIcon()}}" alt="" width="20"/></span>
                            <span>
                                <small style="font-size: small; margin-right: 10px;"><strong class="user-points" style="font-size: 1.2em;"></strong> {{__('points')}}</small>
                                <img id="userProfilePicture" alt="user profile picture" class="img-circle" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" width="30" height="30">
                            </span>
                        </a>
                        <ul class="submenu-1 logout-dropdown">
                            <li class="menu-level-1"><a href="{{route('logout')}}" hidefocus="true" style="outline: none;">{{__('logoutBtn')}}</a></li>
                        </ul>
                    </li>
					{{--<a id="headerUserLogoutLink" href="{{ route('logout')}}" style="outline: none;">

                        <span>{{__('logoutBtn')}}</span>
                    </a>--}}
				</li>
				@endif

                <li class="menu-level-0 @if(isRtl()) pull-right @else pull-left @endif search-dropdown hidden-xs">
                    <a class="search-dropdown-btn" href="#" hidefocus="true" style="outline: none;"><span><i class="fa fa-search"></i></span></a>
                    <ul class="submenu-1">
                        <li class="menu-search last">
                            <form id="searchForm" class="menu-search-form" method="get" action="{{route('search')}}">
                                <input type="text" required name="q" value="" class="menu-search-field" placeholder="{{__('search')}}" hidefocus="true" style="outline: none;">
                                <input type="submit" value="&#xf002;" class="btn menu-search-submit fa" hidefocus="true" style="outline: none;">
                            </form>
                        </li>
                    </ul>
                </li>

                <li class="menu-search last hidden-sm hidden-md hidden-lg">
                    <form id="searchForm" class="menu-search-form" method="get" action="{{route('search')}}">
                        <input type="text" required name="q" value="" class="menu-search-field" placeholder="{{__('search')}}" hidefocus="true" style="outline: none;">
                        <input type="submit" value="&#xf002;" class="btn menu-search-submit fa" hidefocus="true" style="outline: none;">
                    </form>
                </li>

                <script>
                    (function() {
                        $('body').on('click', '.search-dropdown-btn', function(e) {
                            var menuSearchField = $('.search-dropdown .menu-search-field');
                            menuSearchField.focus();
                            e.preventDefault();
                        });
                    })();
                </script>

				<script>
					(function(){
						function updateUserMenu(){
							$('#headerUserMenu').removeClass('hide')
							if(User.isLoggedIn()){
								$('#userProfilePicture').attr('src', User.data['photo']);
                                $('#headerUserMenu .user-points').text(User.data['points']);
								$('#headerUserMenu').addClass('logged-in');
							} else {
								$('#headerUserMenu').removeClass('logged-in');
							}
						}
						$('body').on('loggedIn', function(){
							updateUserMenu();
						});
						updateUserMenu();
						$('#headerUserLoginLink').click(function(e){
							$('body').trigger('prompt-login');
							e.preventDefault();
						});
					})();
				</script>
			</ul>
		</div>
		<!--/ Website Menu -->
		<div class="fixed-menu-padding-adjustment"></div>
	</div>
</div>