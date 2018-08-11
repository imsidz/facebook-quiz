@if(!empty($users))
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="fa fa-users"></i> Users (<b>{{$users->total()}}</b>)</h3>
		</div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-bordered">
				<tr class="">
					<th>Sl no:</th>
					<th style="width: 50px;">Photo</th>
					<th>Name</th>
					<th>Email</th>
					<th>
						<a @if($sort === 'created_at') class="text-danger" @endif href="{{ Helpers::getUrlWithQuery(array('sort' => 'created_at', 'sortType' => ($sortType == 'asc') ? 'desc' : 'asc')) }}">Signed up on @if($sort === 'created_at')

						@if($sortType === 'asc')
						<i class="fa fa-caret-up">
						@else
						<i class="fa fa-caret-down">
						@endif
						</i>
						@endif
						</a>
					</th>
					<th>
						<a @if($sort === 'referrals') class="text-danger" @endif href="{{ Helpers::getUrlWithQuery(array('sort' => 'referrals', 'sortType' => ($sortType == 'asc') ? 'desc' : 'asc')) }}">Referrals @if($sort === 'referrals')
						@if($sortType === 'asc')
						<i class="fa fa-caret-up">
						@else
						<i class="fa fa-caret-down">
						@endif
						</i>
						@endif
						</a>
					</th>
				</tr>
			@forelse($users as $key => $user)
				<tr>
					<td>{{$user->slNo}}</td>
					<td><a target="_blank" href="https://www.facebook.com/app_scoped_user_id/{{@$user->profiles->first()->uid}}"><img src="{{$user->photo}}" alt="" width="50"></a></td>
					<td><a target="_blank" href="https://www.facebook.com/app_scoped_user_id/{{@$user->profiles->first()->uid}}">{{$user->name}}</a></td>
					<td>{!! $user->email !!}</td>
					<td>{{ Helpers::prettyTime($user->created_at, false)}}</td>
					<td>@if($user->referrals) {{$user->referrals}} @else 0 @endif</td>
				</tr>
			@empty
				<tr><td colspan="6" class="text-center text-danger"><b>No Users found</b></td></tr>
			@endforelse

			</table>
			</div>
		</div>
	</div>
{{ $users->render()}}
@endif