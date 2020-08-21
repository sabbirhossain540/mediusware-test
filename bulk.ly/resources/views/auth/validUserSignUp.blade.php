@extends('layouts.auth')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
<div class="auth-container">

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
					<!-- <li class="active"><a href="{{ route('register') }}">Register</a></li> -->
					<li><a href="{{ route('login') }}">Login</a></li>
				  </ul>
				</div>
			  </div>
			</nav>



	   
			  <div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="Register">
					<h3 class="text-center">Create an account or <a href="{{ route('login') }}">login.</a></h3>
					<div class="row">
							<div class="col-md-8 col-md-offset-2">

						

								<form class="form-horizontal" role="form" method="POST" action="{{ url('/app/bulk.ly/free/signUp/'.$urlData->code) }}">
									{{ csrf_field() }}
									<div class="row">
										<div class="col-md-6">
											<div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
												<div class="col-md-12">
													<i class="input-icon fa fa-user"></i>
													<input id="first_name" type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" placeholder="First name" required autofocus>

													@if ($errors->has('first_name'))
														<span class="help-block">
															<strong>{{ $errors->first('first_name') }}</strong>
														</span>
													@endif
												</div>
											</div>        
										</div>
										 

										<div class="col-md-6">
											<div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
												<div class="col-md-12">
													<input id="last_name" type="text" class="form-control nlp" name="last_name" value="{{ old('last_name') }}" placeholder="Last name" required autofocus>

													@if ($errors->has('last_name'))
														<span class="help-block">
															<strong>{{ $errors->first('last_name') }}</strong>
														</span>
													@endif
												</div>
											</div>        
										</div>
									</div>

									<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
										<div class="col-md-12">
										<i class="input-icon fa fa-envelope-o"></i>
											<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email" required>

											@if ($errors->has('email'))
												<span class="help-block">
													<strong>{{ $errors->first('email') }}</strong>
												</span>
											@endif
										</div>
									</div>

									<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
										<div class="col-md-12">
										<i class="input-icon fa fa-lock"></i>
											<input id="password" type="password" class="form-control" name="password" placeholder="Password" required>

											@if ($errors->has('password'))
												<span class="help-block">
													<strong>{{ $errors->first('password') }}</strong>
												</span>
											@endif
										</div>
									</div>
									<div class="form-group">
										<div class="col-md-12">
											<i class="input-icon fa fa-lock"></i>
											<input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Password confirmation" required>
										</div>
									</div>
										
									<div class="form-group{{ $errors->has('token') ? ' has-error' : '' }}">
										<div class="col-md-12">
										<i class="input-icon fa fa-lock"></i>
											<input id="token" type="text" class="form-control" name="token" id="token" placeholder="Promo Code" required>
					

											
												<span class="help-block text-danger">
													<strong> {{ Session::get( 'message' ) }}</strong>
												</span>

												<span class="help-block text-danger">
													<strong id="token_error"> </strong>
												</span>
										
										</div>
									</div>

									<div class="form-group{{ $errors->has('tos') ? ' has-error' : '' }}">
										<div class="col-md-12">
											<div class="checkbox">
												
													<input class="check-toog left-toog" type="checkbox" name="tos" class="tos" value="yes" id="conditions"> 
													<label for="conditions">I agree to the <a target="_blank" href="https://bulk.ly/terms">terms and conditions.</a></label>

											@if ($errors->has('tos'))
												<span>
													<strong>{{ $errors->first('tos') }}</strong>
												</span>
											@endif

												
											</div>
	
										</div>
									</div>
									<div class="form-group{{ $errors->has('recaptcha') ? ' has-error' : '' }}">
										<div class="col-md-12 text-center">
											<div style="display: inline-block;" data-callback="recaptchaCallback"  class="g-recaptcha" data-sitekey="<?php echo env('RECAPTCHAKEY'); ?>"></div>

											@if ($errors->has('recaptcha'))
												<div>
													<strong>{{ $errors->first('recaptcha') }}</strong>
												</div>
											@endif
										</div>
									</div>


									<div class="form-group text-center">
										<div class="col-md-12">
											<button  type="submit" class="btn btn-default width-btn btn-dc btn-block">
											
												Create Acount <i class="fa-btn fa fa-angle-right"></i>
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

		</div>
	</div>
</div>
<script src='https://www.google.com/recaptcha/api.js'></script>
<script>
    document.getElementById("CreateAcount").disabled = true;
    function recaptchaCallback(response){
        console.log(response);
        if(response){
            document.getElementById("CreateAcount").disabled = false;
        }
    }

    jQuery(document).ready(function($) {
    	$('.alert').alert();
    });


    jQuery(document).ready(function($) {
    	if (getElementById('token').val()<6) {
    		$('token_error').show('400');
    	}
    });
</script>

<!-- <button id="CreateAcount" type="submit" class="btn btn-default width-btn btn-dc btn-block"> -->
@endsection
