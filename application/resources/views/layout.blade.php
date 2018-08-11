<!DOCTYPE html>
<html lang="{{App::getLocale()}}">

<head>

   @section('head')
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1,shrink-to-fit=no">
		@if(!empty($config['main']['favicon']))
			<link rel="shortcut icon" href="{!! apply_filters('favicon', htmlspecialchars(content_url($config['main']['favicon'])), $config['main']['favicon']) !!}">
		@endif

    @if(!empty($title))
            <title>{!! apply_filters('meta_title', htmlspecialchars($title), $title) !!}</title>
    @endif

    <meta property="og:type" content="website">
    @if(!empty($ogTitle))
            <meta property="og:title" content="{!! apply_filters('og_title', htmlspecialchars($ogTitle), $ogTitle) !!}" />
            <meta name="twitter:title" content="{!! apply_filters('og_title', htmlspecialchars($ogTitle), $ogTitle) !!}" />
    @endif


    @if(!empty($ogImage))
            <meta property="og:image" content="{!! apply_filters('og_image', htmlspecialchars($ogImage), $ogImage) !!}" />
            <meta name="twitter:image" content="{!! apply_filters('og_image', htmlspecialchars($ogImage), $ogImage) !!}" />
            <meta name="twitter:card" content="photo" />
    @endif

    @if(!empty($ogUrl))
            <meta property="og:url" content="{!! apply_filters('og_url', htmlspecialchars($ogUrl), $ogUrl) !!}" />
            <meta name="twitter:url" content="{!! apply_filters('og_url', htmlspecialchars($ogUrl), $ogUrl) !!}" />
    @endif

    @if(!empty($description))
            <meta name="description" content="{!! apply_filters('meta_description', htmlspecialchars($description), $description) !!}" />
    @endif
    @if(!empty($ogDescription))
            <meta property="og:description" content="{!! apply_filters('og_description', htmlspecialchars($description), $description) !!}" />
    @endif

        <meta property="og:site_name" content="{!! apply_filters('og_site_name', htmlspecialchars(Helpers::getSiteName()), Helpers::getSiteName()) !!}" />

    @if(!empty($canonicalUrl))
            <link rel="canonical" href="{!! apply_filters('canonical_url', htmlspecialchars($canonicalUrl), $canonicalUrl) !!}" />
    @endif
    <!-- Custom CSS -->
	@if(App::isLocal())
		<link href="{{assetWithCacheBuster('/css/main-with-libs.css')}}" rel="stylesheet">
	@else
		<link href="{{assetWithCacheBuster('/css/main.min.css')}}" rel="stylesheet">
	@endif

    @if(App::isLocal())
    <link href="{{assetWithCacheBuster('/themes/modern/style.css')}}" rel="stylesheet">
    @else
    <link href="{{assetWithCacheBuster('/themes/modern/style.min.css')}}" rel="stylesheet">
    @endif

	@if(!empty($languageDirection) && $languageDirection == 'rtl')
		@include('partials.rtlCss')
	@endif

	@if(!empty($navbarColor))
		<style>
			@include('partials.themeCss')
		</style>
	@endif

    {{do_action('print_head_styles')}}

    <!-- Custom Fonts -->
    <link href="{{assetWithCacheBuster('/font-awesome-4.1.0/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">

   	<!-- jQuery Version 1.11.0 -->
   	@if(App::isLocal())
    <script src="{{ assetWithCacheBuster('bower_components/jquery/dist/jquery.min.js')}}"></script>
    @else
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    @endif

    {{do_action('print_head_scripts')}}

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
		var SiteMainConfig = {!! @$mainConfigJSON !!};
		var SiteQuizConfig = {!! @$quizConfigJSON !!};

        SiteQuizConfig.showSharePromptModal = (SiteQuizConfig.showSharePromptModal === "true") ? true : false;
		SiteQuizConfig.showPageLikePrompt = (SiteQuizConfig.showPageLikePrompt === "true") ? true : false;
		SiteQuizConfig.showFacebookComments = (SiteQuizConfig.showFacebookComments === "true") ? true : false;
		var User = {
			isLoggedIn: function(){
				return (!$.isEmptyObject(this.data));
			},
			setData: function(data){
                var wasAlreadyLoggedIn = this.isLoggedIn();
				this.data = data;
                //Logged in after setting new data. Trigger logged in event
				if(!wasAlreadyLoggedIn && this.isLoggedIn()){
					$('body').trigger('loggedIn');
				}
			}
		};
		User.data = {!! $userData or '{}' !!};

        @if(!empty($categories))
            window.Categories = {!! json_encode($categories) !!};
        @endif
	</script>

    @if(!empty($languageStrings))
        <script>
            var languageStrings = {!! json_encode($languageStrings) !!};
            var defaultLanguageStrings = {!! json_encode($defaultLanguageStrings) !!};
            //Translation
            function __(key){
                if(languageStrings.hasOwnProperty(key)){
                    return languageStrings[key];
                } else if (defaultLanguageStrings.hasOwnProperty(key)){
                    return defaultLanguageStrings[key];
                } else {
                    return key;
                }
            }
        </script>
    @endif
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	<script>
		
	  window.fbAsyncInit = function() {
		FB.init({
		  appId      : '<?php echo @$config['main']['social']['facebook']['appId'];?>',
		  xfbml      : true,
		  version    : 'v2.5',
			cookie : true
		});

		$('body').trigger('fb-api-loaded');
	  };

	  (function(d, s, id){
			 var js, fjs = d.getElementsByTagName(s)[0];
			 if (d.getElementById(id)) {return;}
			 js = d.createElement(s); js.id = id;
			 js.src = "//connect.facebook.net/{{$languageFbCode}}/sdk.js";
			 fjs.parentNode.insertBefore(js, fjs);
		})(document, 'script', 'facebook-jssdk');
	</script>

	@if(!empty($config['main']['customCode']['head']))
		{!! $config['main']['customCode']['head'] !!}
	@endif

	@show

   {{do_action('head')}}
</head>

<body class="{{apply_filters('body_class', "")}}">
    {{do_action('body_begin')}}
	<div id="fb-root"></div>

        <div class="body_wrap @if(!empty($currentPage))page-{{$currentPage}}@endif">
    	<div class="body-container container-fluid modern-touch">

            @section('header')
                @include('header')
            @show
            <div class="row">
            	<div class="col-md-8 col-sm-7 col-xs-12 main-content-col @if($languageDirection == 'rtl') pull-right @endif">
				@yield('content')
				</div>

				<div class="col-md-4 col-sm-5 col-xs-12 sidebar-col @if($languageDirection == 'rtl') pull-left @endif">
                    @section('sidebar')
					    @include('sidebar')
                    @show
			   </div>
            </div>
                {{do_action_ref_array('show_widgets', ['commonFooterSection', &$widgets])}}
            @if(!empty($widgets['commonFooterSection']))
            <div class="row">
            	<div class="col-md-12">
            		<div class="common-foot-widget-section">
						@foreach($widgets['commonFooterSection'] as $widget)
                            {!! do_shortcode($widget['content']) !!}
						@endforeach
					</div>
            	</div>
            </div>
			@endif

            <!-- /.container-fluid -->

        </div>
            <div class="container-fluid">
                <div class="row footer-row">
                    <div class="col-md-12">
                        @section('footer')
                            @include('footer')
                        @show
                    </div>
                </div>
            </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

   <div id="loginModal" class="modal fade">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-body">
		  	<h4 class="login-prompt-message">{{__('loginBtn')}} / {{__('signUpBtn')}}</h4>
			<div class="login-panel">
                <div id="loginError" class="hide"></div>
				<ul>
				  <li>
					<div class="btn btn-fb btn-block" data-action="loginWithFB"><span>{{__('loginWithFB')}}</span></div>
				  </li>
				</ul>
			</div>
			<div class="logging-in-msg">
				<h4 class="text-center">{{__('loggingYouIn')}}</h4>
			</div>
		  </div>
		</div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
   
   @section('foot')
   <!-- Logging in -->
	<script>
		(function(){
            var body = $('body');

            var socialLoginErrorElm = $('#loginError');
            var loginModal = $('#loginModal');

            body.on('social-login:error', function(e, error) {
                socialLoginErrorElm.removeClass('hide').html('<div class="alert alert-danger">' + error + '</div>');
                loginModal.removeClass("logging-in");
            });
			window.loginWithFb = function(){
				FB.login(function(response) {
                    if (response.authResponse) {
                        if(response.authResponse.grantedScopes.split(',').indexOf('email') < 0) {
                            //If email permission not granted
                            body.trigger('social-login:error', (__('fbNoEmailError')));
                            return;
                        }
                        FB.api('/me', {fields: 'id,name,email'}, function(response) {
                            console.log('Logged in as ' + response.name + '.');
                            //Dual check email - needed to if check if the user has a verified email ID
                            if(!response.email) {
                                body.trigger('social-login:error', (__('fbNoEmailError')));
                                return;
                            }
                            body.trigger('loggedIn:fb');
                        });
                    } else {
                        body.trigger('social-login:error', (__('fbPermissionError')));
                    }
				}, {
                    scope: 'email',
                    auth_type: 'rerequest',
                    'return_scopes': true
                });
			}
			
			var body = $('body');
			body.on('click', '[data-action="loginWithFB"]', function(e){
				loginWithFb();
				e.preventDefault();
			});
			body.on('loggedIn', function(){
				loginModal.modal('hide');
			});
			body.on('loggedIn:fb', function(){
				if(!User.isLoggedIn()) {
					$.get(BASE_PATH + '/login/fb').success(function(response){
						User.setData(response.user);
					}).fail(function(jqXHR, textStatus, errorThrown){
						body.trigger('social-login:error', jqXHR.responseText);
					}).always(function(){
						loginModal.removeClass("logging-in");
					});
				}
			});
			body.on('prompt-login', function(e, message){
				loginModal.find('.login-prompt-message').html(message);
				loginModal.modal('show');
			});
		})();

        function showNewPointsAlert(addedPoints) {
            var alertOptions = {
                title: "+"+ addedPoints +" " + __('points'),
                text: __('earnedNewPointsMessage'),
                imageUrl: "{{LeaderboardHelpers::getPointsIcon()}}",
                confirmButtonText: __('earnedNewPointsOkayBtnText'),
                allowEscapeKey: true,
                allowOutsideClick: true,
                customClass: 'new-points-alert'
            }
            @if(!empty($mainBtnColor))
            alertOptions.confirmButtonColor = '{{{$mainBtnColor}}}';
            @endif
            swal(alertOptions);
        }
        $('body').on('user-activity-recorded', function() {
            $.get('{{route('getMyPoints')}}').success(function(response) {
                if(response && response.points) {
                    var oldPoints = parseInt(User.data.points);
                    var newPoints = parseInt(response.points);
                    User.data.points = newPoints;
                    User.setData(User.data);
                    if(oldPoints != newPoints) {
                        var animateClass = 'animated bounceIn';
                        $('#headerUserMenu').removeClass(animateClass).addClass(animateClass);
                        var addedPoints = parseInt(newPoints) - parseInt(oldPoints);
                        @if(MyConfig::isTrue('leaderboard.showNewPointsAlert'))
                        showNewPointsAlert(addedPoints);
                        @endif
                    }
                }
            }).fail(function() {

            });
        });

	</script>
    <!-- Bootstrap Core JavaScript -->
    <script src="{{ assetWithCacheBuster('/themes/modern/js/libs/modernizr.min.js') }}"></script>
	<script src="{{ assetWithCacheBuster('/themes/modern/js/libs/bootstrap.min.js') }}"></script>
   <script src="{{ assetWithCacheBuster('/bower_components/sweetalert/dist/sweetalert.min.js')}}"></script>

   {{do_action('print_foot_scripts')}}

	   @if(!empty($config['main']['customCode']['foot']))
		   {{$config['main']['customCode']['foot']}}
	   @endif

	@show

    {{do_action('foot')}}
</body>

</html>
