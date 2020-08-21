@extends('layouts.app')
@section('content')
<div class="container-fluid app-body settings-page">
	<h3>Buffer Post</h3>
	<div class="row">
		<form  method="GET" action="{{ route('bufferPosts.index') }}">
		<div class="col-md-12">
			 
				<div class="col-md-3">
	                  <input type="text" class="form-control" name="search" placeholder="Search">
				</div>
				<div class="col-md-3">
	                  <input type="date" class="form-control" name="dateSearch" placeholder="date">
	                </form>
				</div>
				<div class="col-md-3">
	                  <select class="form-control" name="groupSearch" id="groupSearch" placeholder="groupSearch">
	                  	<option value="">All Group</option>
	                  	<option value="upload">Upload</option>
	                  	<option value="curation">Curation</option>
	                  	<option value="rss-automation">RSS Automation</option>
	                  </select>
				</div>

				<div class="col-md-3">
	                  <button type="Submit" class="btn btn-success">Submit</button>
				</div>
		</div>
		</form>
	</div>
	<div class="row">
		<div class="col-md-12">
			<table class="table table-hover social-accounts"> 

				<thead> 
					<tr><th>Group Name</th> <th>Group Type</th> <th>Account Name</th> <th>Post Text</th> <th>Time</th> </tr> 
				</thead> 
				<tbody> 
					@foreach($bufferPosts as $BufferPost )
						<tr>
							<td>{{ $BufferPost->groupInfo->name }}</td>
							<td>{{ $BufferPost->groupInfo->type }}</td>
							<td>{{ $BufferPost->accountInfo->name }}</td>
							<td>{{ $BufferPost->post_text }}</td>
							<td>{{ $BufferPost->sent_at }}</td>
						</tr>

					@endforeach

				</tbody> 

			</table>
			{{ $bufferPosts->appends([request()->query('search')])->links() }}
		</div>
	</div>
</div>

<script type="text/javascript">
	$document().ready(function(){
		$("#groupSearch").select(function(){
			alert("ok");
		});
	});
	
</script>
@endsection