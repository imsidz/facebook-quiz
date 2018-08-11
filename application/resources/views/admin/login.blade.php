<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Admin login</title>
	<link href="{{ assetWithCacheBuster('/css/bootstrap.min.css')}}" rel="stylesheet">
	<style>
		body{
			background: #242634;
			color: #ffffff;
		}
		.panel{
			color: #333;
		}
		.main-container {
			padding: 100px 0px;
		}
	</style>
</head>
<body >

<div class="container main-container">
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<h3 class="text-center">{{\Helpers::getSiteName()}}</h3><br/>
			<div class="panel panel-primary">
				<div class="panel-heading text-center">
					<h4>Admin Login</h4>
				</div>
				<div class="panel-body">
					@if(!empty($error))
						<div class="alert alert-danger">
							{{$error}}
						</div>
					@endif
					<form action="{{ URL::route('adminLogin')}}" method="post">
						<div class="form-group">
							<label for="exampleInputEmail1">Username</label>
							<input type="text" class="form-control" name="username" placeholder="Username">
						  </div>
						  <div class="form-group">
							<label for="exampleInputPassword1">Password</label>
							<input type="password" class="form-control" name="password" placeholder="Password">
						  </div>
						<input name="redirect" type="hidden" value="{{{$redirect or ''}}}"/>
						  <input type="submit" class="btn btn-warning btn-block btn-lg" value="Login">
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

</body>
</html>