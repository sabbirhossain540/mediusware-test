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
							<li> <a class="btn btn-primary" href="/admin/membership-plan/add">Add plan</a> </li>
						</ul>
						<hr>

<h4>Monthly Plan</h4>
<ul class="list-unstyled">
						@foreach($plans_m as $key => $plan)
						<li><br><strong>{{$plan->name}}</strong></li>
						<li>${{$plan->price}} per month</li>
						<li>Connected Buffer Account:  {{$plan->connucted_buf_account}}</li>
						<li>Save Content Upload Post:  {{$plan->save_content_upload_post}} </li>
						<li>Save Content Upload Group:  {{$plan->save_content_upload_group}}</li>
						<li>Save Content Curation Feeds:  {{$plan->save_content_curation_feeds}} </li>
						<li>Save Content Curation Group:  {{$plan->save_content_curation_group}} </li>
						<li>Save RSS Automotion Feeds:  {{$plan->save_rss_auto_feeds}}</li>
						<li>Save RSS Automotion Group: {{$plan->save_rss_auto_group}}</li>
						<li>
						<a class="btn btn-default" href="/admin/membership-plan/edit/{{$plan->id}}">Edit</a> 
						<a class="btn btn-default" href="/admin/membership-plan/delete/{{$plan->id}}">Delete</a>
						</li>
						
						@endforeach
</ul>
<br>
<br>
<h4>Yearly Plan</h4>

<ul class="list-unstyled">
						@foreach($plans_y as $key => $plan)
						<li><br><strong>{{$plan->name}}</strong></li>
						<li>${{$plan->price}} per year</li>
						<li>Connected Buffer Account:  {{$plan->connucted_buf_account}}</li>
						<li>Save Content Upload Post:  {{$plan->save_content_upload_post}} </li>
						<li>Save Content Upload Group:  {{$plan->save_content_upload_group}}</li>
						<li>Save Content Curation Feeds:  {{$plan->save_content_curation_feeds}} </li>
						<li>Save Content Curation Group:  {{$plan->save_content_curation_group}} </li>
						<li>Save RSS Automotion Feeds:  {{$plan->save_rss_auto_feeds}}</li>
						<li>Save RSS Automotion Group: {{$plan->save_rss_auto_group}}</li>
						<li>
						<a class="btn btn-default" href="/admin/membership-plan/edit/{{$plan->id}}">Edit</a> 
						<a class="btn btn-default" href="/admin/membership-plan/delete/{{$plan->id}}">Delete</a>
						</li>
						


						@endforeach

</ul>
					</div>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>
@endsection