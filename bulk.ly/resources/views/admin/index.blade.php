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
						<ul class="list-unstyled">
							<li> <strong>Total users : </strong> {{count($users)}} </li>


						@foreach($subscriptions as $key => $subscription)
						<li> <strong>{{$subscription->name}} : </strong>  {{$subscription->count}} </li>
						@endforeach
						
			

                        <li> <strong># of post sent to Buffer : </strong> {{ \Bulkly\BufferPosting::count() }} </li>
                        <li> <strong># of post saved on database : </strong> {{ \Bulkly\SocialPosts::count() }}  </li>

						</ul>


						

					</div>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>
@endsection