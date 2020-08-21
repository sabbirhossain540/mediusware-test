@extends('admin.layouts.app')



@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
			<div class="panel-body">
				<div class="row">
					<div class="col-md-3">
						<div class="list-group">
						  	<a class="list-group-item @if (\Request::route()->getName()=='admin') active @endif" href="/admin/">Overview</a>
						  	<a class="list-group-item active" href="/admin/manage-user/">Manage User</a>
						  	<a class="list-group-item @if (\Request::route()->getName()=='admin/membership-plan') active @endif" href="/admin/membership-plan">Membership Plan</a>
						  	<a class="list-group-item @if (\Request::route()->getName()=='admin/free-sign-up') active @endif" href="/admin/free-sign-up">Free Sign Up</a>
						</div>
					</div>
					<div class="col-md-9">
						<h3>Add User</h3>

						<form action="" method="post">
						{{csrf_field()}}
						  <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
						    <label for="FirstName">First Name</label>
						    <input type="text" class="form-control" id="FirstName" name="first_name" placeholder="First Name">
								@if ($errors->has('first_name'))
									<span class="help-block">
										<strong>{{ $errors->first('first_name') }}</strong>
									</span>
								@endif
						  </div>
						  <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
						    <label for="LastName">Last Name</label>
						    <input type="text" class="form-control" id="LastName" name="last_name" placeholder="Last Name">
								@if ($errors->has('last_name'))
									<span class="help-block">
										<strong>{{ $errors->first('last_name') }}</strong>
									</span>
								@endif
						  </div>

						  <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
						    <label for="Email">Email</label>
						    <input type="email" class="form-control" id="Email" name="email" placeholder="Email ">
								@if ($errors->has('email'))
									<span class="help-block">
										<strong>{{ $errors->first('email') }}</strong>
									</span>
								@endif
						  </div>

						  <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
						    <label for="Password">Password</label>
						    <input type="password" class="form-control" name="password" id="Password" placeholder="Password ">
								@if ($errors->has('password'))
									<span class="help-block">
										<strong>{{ $errors->first('password') }}</strong>
									</span>
								@endif
						  </div>

						  <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
						    <label for="Date">Trial days</label>
						    <input type="text" class="form-control" name="date" id="Date" placeholder="Trial days ">
								@if ($errors->has('date'))
									<span class="help-block">
										<strong>{{ $errors->first('date') }}</strong>
									</span>
								@endif
						  </div>



						  <button type="submit" class="btn btn-default">Submit</button>
						</form>

					</div>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>
@endsection