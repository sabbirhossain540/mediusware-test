

@extends('layouts.auth')

@section('content')


<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="auth-container">
                @if ($status = Session::get('status'))
                <div class="alert alert-info">{{$status}}</div>
                @endif
                <div class="panel panel-default">
                    <div class="panel-body">
                        <nav class="navbar">
                            <div class="container-fluid">
                                <div class="navbar-header">
                                  <a class="navbar-brand" href="/">
                                    <img src="/images/logo.png">
                                  </a>
                                </div>
                                <div class="collapse navbar-collapse" >
                                    <ul class="nav navbar-nav navbar-right">
                                        <li><a href="{{ route('register') }}">Register</a></li>
                                        <li><a href="{{ route('login') }}">Login</a></li>
                                    </ul>
                                </div>
                            </div>
                        </nav>
                        <div class="tab-content">
                       <br>
                       <br>
                       <br>
                       <br>




                    <form class="form-horizontal" role="form" method="POST" action="{{ route('password.request') }}">
                        {{ csrf_field() }}

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ $email or old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Reset Password
                                </button>
                            </div>
                        </div>
                    </form>

                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
