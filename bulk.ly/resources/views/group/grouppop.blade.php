
<div class="dropdown">
	<div id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn">
		<span class="fa fa-ellipsis-v"></span>
	</div>
	<ul class="dropdown-menu dropdown-pop post-to" aria-labelledby="dLabel">
	   <li>
			POST TO
			<ul class="list-inline">
				@foreach ($group->targertservices() as $targertservice)
				@if($targertservice != 'google')
				<li><i class="fa fa-{{ $targertservice }}"></i></li>
				@endif
				@endforeach
			</ul>  
		   <br>
			<table class="table">
				<tr>
					<td width="200">GROUP</td><td width="200" align="right"> {{$group->name}}</td>
				</tr>
				<tr>
					<td width="200"># OF POSTS</td><td width="200" align="right"> {{count($group->posts)}}</td>
				</tr>
				
				<tr>
					<td width="200">FREQUENCY</td><td width="200" align="right"> {{$group->frequency}} times {{$group->interval}}</td>
				</tr>
				@if($group->status=='1')
				<tr>
					<td width="200">POST LAST SENT</td><td width="200" align="right"> 
					<?php
					$buffpost = \Bulkly\BufferPosting::where('group_id', $group->id)->orderBy('id', 'desc')->first();
					if($buffpost) {
					if($buffpost->sent_at){  
						?>
						{{\Carbon\Carbon::parse($buffpost->sent_at)->diffForHumans(\Carbon\Carbon::now(), true)}} ago
						<?php
					}
					}
					?>
					</td>
				</tr>
				<tr>
					<td width="200">NEXT TIME TO SEND</td><td width="200" align="right"> 
					@if($group->next_schedule_time)
						{{\Carbon\Carbon::parse($group->next_schedule_time)->diffForHumans(\Carbon\Carbon::now(), true)}} from now
					@endif
					</td>
				</tr>
				 @endif
				
				
				
				<tr>
					<td width="200">RECYCLED POST</td><td width="200" align="right">@if($group->recycle =='1') Yes @else No @endif</td>
				</tr>
			</table>
			
			@if($group->type=='upload')
			<a href="{{route('content-completed', $group->id)}}" class="btn btn-default width-btn btn-dc">
			@endif
			@if($group->type=='curation')
			<a href="{{route('content-curation-completed', $group->id)}}" class="btn btn-default width-btn btn-dc">
			@endif
			@if($group->type=='rss-automation')
			<a href="{{route('rss-automation-completed', $group->id)}}" class="btn btn-default width-btn btn-dc">
			@endif
			Edit Group
			</a>
			<form id="group-delete-{{$group->id}}" class="group-delete" method="POST" style="display: inline-block;">
				{{ csrf_field() }}
				<input type="hidden" name="group_id" value="{{$group->id}}">
				<button type="submit" class="btn btn-default btn-icon-round"><span class="fa fa-trash-o"></span></button>
			</form>

	   </li>
	</ul>
</div>