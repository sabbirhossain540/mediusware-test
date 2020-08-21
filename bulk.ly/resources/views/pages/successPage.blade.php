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
					<li><a href="{{ route('login') }}">Login</a></li>
				  </ul>
				</div>
			  </div>
			</nav>



	   
			  <div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="Register">
					
					<div class="row">
						<div class="col-md-8 col-md-offset-2">
								
							<h3 class="text-center">You're Almost Done . Please check your email for activate you account.  </h3>
									
							
							{{--<div class="form-group text-center">--}}
								{{--<div class="col-md-12">--}}
									{{--<a href="{{ route('login') }}"><button  class="btn btn-default width-btn btn-dc btn-sm">--}}
									{{----}}
										{{--Login <i class="fa-btn fa fa-angle-right"></i>--}}
									{{--</button></a>--}}
								{{--</div>--}}
							{{--</div>--}}
								
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
</script>


<!-- <button id="CreateAcount" type="submit" class="btn btn-default width-btn btn-dc btn-block"> -->
@endsection
