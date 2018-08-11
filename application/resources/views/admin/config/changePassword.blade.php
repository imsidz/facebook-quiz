@extends('admin/layout')

@section('content')
    <div class="row" style="margin-top: 40px;">
        <div class="col-md-5">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">
                        Change Admin username/password
                    </div>
                </div>
                <div class="panel-body">
                    @if(!empty($formErrors))
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($formErrors as $error)
                                    <li>{{$error}}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if(!empty($formSuccess))
                        <div class="alert alert-success">New Login details successfully saved!</div>
                            <a class="btn btn-success" href="{{route('admin')}}">Go back to dashboard</a>
                            <br/><br/>
                    @else
                    <form action="{{route('adminChangePassword')}}" method="POST">
                        <div class="form-group">
                            <label for="usernameInput">Username</label>
                            <input type="text" name="username" class="form-control" id="usernameInput" placeholder="Enter username" value="{{$currentUsername or ''}}">
                        </div>
                        <div class="form-group">
                            <label for="passwordInput">Password</label>
                            <input type="password" name="password" class="form-control" id="passwordInput" placeholder="Password">
                        </div>
                        <div class="form-group">
                            <label for="passwordRepeatInput">Repeat Password</label>
                            <input type="password" name="repeatPassword" class="form-control" id="passwordRepeatInput" placeholder="Repeat password">
                        </div>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
