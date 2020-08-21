

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




                                    <form class="form-horizontal" role="form" method="POST" action="{{ route('password.email') }}">
                                        {{ csrf_field() }}

                                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                                            <div class="col-md-6">
                                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                                @if ($errors->has('email'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-md-6 col-md-offset-4">
                                                <button type="submit" class="btn btn-primary">
                                                    Send Password Reset Link
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
