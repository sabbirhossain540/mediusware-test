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




					<form method="post" action="">
					{{csrf_field()}}
					<h3>Add Plan</h3>
					  <div class="form-group">
					    <label class="hide" for="PlanName">Plan Name</label>
					    <input type="text" name="name" class="form-control" id="PlanName" placeholder="Plan Name">
					  </div>

				  
					  <div class="form-group">

					  <div class="radio"> 
					  <label> <input type="radio" name="type" value="Month" checked="checked"> Monthly  </label>
					  </div>
					  <div class="radio"> 
					  <label> <input type="radio" name="type" value="Year" > Yearly  </label>
					  </div>
					  </div>


					  <div class="form-group">
					    <label class="hide" for="PlanPrice">Plan Price</label>
					    <input type="text" name="price" class="form-control" id="PlanPrice" placeholder="Plan Price">
					  </div>
					  <div class="form-group">
					    <label class="hide" for="PostPerMonth">Post Per Month</label>
					    <input type="text" name="ppm" class="form-control" id="PostPerMonth" placeholder="Post Per Month">
					  </div>

					  <div class="form-group">
					    <label class="hide" for="ConnectedBufferAccount">Connected Buffer Account</label>
					    <input type="text" name="connucted_buf_account" class="form-control" id="ConnectedBufferAccount" placeholder="Connected Buffer Account">
					  </div>

					  <div class="form-group">
					    <label class="hide" for="SaveContentUploadPost">Save Content Upload Post</label>
					    <input type="text" name="save_content_upload_post" class="form-control" id="SaveContentUploadPost" placeholder="Save Content Upload Post">
					  </div>

					  <div class="form-group">
					    <label class="hide" for="SaveContentUploadGroup">Save Content Upload Group</label>
					    <input type="text" name="save_content_upload_group" class="form-control" id="SaveContentUploadGroup" placeholder="Save Content Upload Group">
					  </div>


					  <div class="form-group">
					    <label class="hide" for="SaveContentCurationFeeds">Save Content Curation Feeds</label>
					    <input type="text" name="save_content_curation_feeds" class="form-control" id="SaveContentCurationFeeds" placeholder="Save Content Curation Feeds">
					  </div>

					  <div class="form-group">
					    <label class="hide" for="SaveContentCurationGroup">Save Content Curation Group</label>
					    <input type="text" name="save_content_curation_group" class="form-control" id="SaveContentCurationGroup" placeholder="Save Content Curation Group">
					  </div>

					  <div class="form-group">
					    <label class="hide" for="SaveRSSAutomotionFeeds">Save RSS Automotion Feeds</label>
					    <input type="text" name="save_rss_auto_feeds" class="form-control" id="SaveRSSAutomotionFeeds" placeholder="Save RSS Automotion Feeds">
					  </div>

					  <div class="form-group">
					    <label class="hide" for="SaveRSSAutomotionGroup">Save RSS Automotion Group</label>
					    <input type="text" name="save_rss_auto_group" class="form-control" id="SaveRSSAutomotionGroup" placeholder="Save RSS Automotion Group">
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