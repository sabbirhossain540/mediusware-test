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
						  	<a class="list-group-item @if (\Request::route()->getName()=='admin/manage-user') active @endif" href="/admin/manage-user/">Manage User</a>
						  	<a class="list-group-item @if (\Request::route()->getName()=='admin/membership-plan') active @endif" href="/admin/membership-plan">Membership Plan</a>
						  	<a class="list-group-item @if (\Request::route()->getName()=='admin/free-sign-up') active @endif" href="/admin/free-sign-up">Free Sign Up</a>
						</div>
					</div>
					<div class="col-md-9">


					<ul class="list-inline">
						<li>
							<a class="btn btn-primary" href="/admin/manage-user/create"> Create Account</a>
						</li>
						<li>
							<form>
								<input class="form-control" type="text" name="search" placeholder="Search">
								<button class="pull-right" style=" position: relative; margin-top: -27px; border: 0px; background: 0px;  padding-right: 12px; outline: none !important;"> <i class="glyphicon glyphicon-search"></i> </button>
							</form>
						</li>
					</ul>

					<table class="table table-bordered"> 
						<thead> 
							<tr> 
								<th>First Name</th> <th>Last Name</th> <th>Email</th> <th>Created Date</th> <th>Subscription plan</th> <!--<th>Last payment date</th> --> <th></th>
							</tr> 
						</thead> 
						<tbody> 
							@foreach($users as $key => $user)
							<tr> 
							
								<td>{{$user->first_name}}</td> 
								<td>{{$user->last_name}}</td> 
								<td><p style="word-break: break-all;">{{$user->email}}</p></td> 
								<td>{{$user->created_at}}</td>
								<td> @if($user->plansubs()['plan'])
								    {{$user->plansubs()['plan']->name}}
								    @else
								    Free
								    @endif
								</td> 
								<td> <a href="/admin/manage-user/edit/{{$user->id}}">Edit</a> </td>

							</tr>
							@endforeach
						 </tbody> 
					 </table>

					 
					</div>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>
@endsection
