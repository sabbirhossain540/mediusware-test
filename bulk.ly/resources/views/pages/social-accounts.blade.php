@extends('layouts.app')
@section('content')
<div class="container-fluid app-body">
	<h3>Social Accounts 

	@if($user->plansubs())
		@if($user->plansubs()['plan']->slug == 'proplusagencym' OR $user->plansubs()['plan']->slug == 'proplusagencyy' )
			<a href="https://bufferapp.com/oauth2/authorize?client_id={{env('BUFFER_CLIENT_ID')}}&redirect_uri={{env('BUFFER_REDIRECT')}}&response_type=code" class="btn btn-primary pull-right">Add Buffer Account</a>
		@endif
	@endif




	</h3>

	<div class="row">
		<div class="col-md-12">
			<table class="table table-hover social-accounts"> 
				<thead> 
					<tr><th>Account</th> <th>Last post sent</th> <th># of post sent</th> <th>Groups</th> <th>Active</th> </tr> 
				</thead> 
				<tbody> 
				@foreach ($profiles as $profile)
					@if ( $profile['type'] != 'google' )
					<tr>
						<td width="350">
							<div class="media">
								<div class="media-left">
									<a href="">
										<span class="fa fa-{{$profile['type']}}"></span>
										<img width="50" class="media-object img-circle" src="{{$profile['avatar']}}" alt="">
									</a>
								</div>
								<div class="media-body media-middle" style="width: 180px;">
									<h4 class="media-heading">{{$profile['name']}}</h4>
								</div>
							</div>
						</td> 
						<td><i class="fa fa-clock-o"></i> <span data-sent="@if (isset(unserialize($profile['post_sent'])['last_sent_at'])) {{ strtotime(unserialize($profile['post_sent'])['last_sent_at'])}} @endif"></span></td> 
						<td>
						@if (isset(unserialize($profile['post_sent'])['count']))
						@if(unserialize($profile['post_sent'])['count']>0) {{unserialize($profile['post_sent'])['count']}} @else 0 @endif
						@else
						0
						@endif
						</td> 
						<td>{{ count($profile->groupsact($profile->id)) }} 
						<span class="dropdown">
							<a href="#" class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">(view)</a>
							@if(count($profile->groups($profile->id)) > 0)
							<ul class="dropdown-menu dropdown-center dropdown-pop" aria-labelledby="dropdownMenu1">
								<li>
									<form method="post" id="social-account-id-group-{{$profile['account_id']}}" class="social-account-id-group">
										{{ csrf_field() }}
										<input type="hidden" name="account_id" value="{{$profile['id']}}">
										GROUPS OVERVIEW
										<div style="width: 450px;">
											<ul class="nav nav-tabs nav-justified" role="tablist">
												<li role="presentation" class="active"><a href="#upload-{{$profile['account_id']}}" aria-controls="upload-{{$profile['account_id']}}" role="tab" data-toggle="tab">Content Upload</a></li>
												<li role="presentation"><a href="#curation-{{$profile['account_id']}}" aria-controls="curation-{{$profile['account_id']}}" role="tab" data-toggle="tab">Content Curation</a></li>
												<li role="presentation"><a href="#rssauto-{{$profile['account_id']}}" aria-controls="rssauto-{{$profile['account_id']}}" role="tab" data-toggle="tab">Rss Automotion</a></li>
											</ul>
											<div class="tab-content">
												<div role="tabpanel" class="tab-pane active" id="upload-{{$profile['account_id']}}">
													<ul class="list-unstyled">
														@foreach($profile->groups($profile->id) as $group)
															@if($group->type=='upload')
																@if(in_array($profile['id'], unserialize($group->target_acounts)))
																	<li><input id="{{$profile['account_id']}}-{{$group->id}}" class="check-toog left-toog" type="checkbox" value="{{$group->id}}" name="group_id[]" checked> <label for="{{$profile['account_id']}}-{{$group->id}}">  {{$group->name}}</label> </li>
																@else
																	<li><input id="{{$profile['account_id']}}-{{$group->id}}" class="check-toog left-toog" type="checkbox" value="{{$group->id}}" name="group_id[]"> <label for="{{$profile['account_id']}}-{{$group->id}}"> {{$group->name}}</label> </li>
																@endif
															@endif
														@endforeach     	
													</ul>
												</div>
												<div role="tabpanel" class="tab-pane" id="curation-{{$profile['account_id']}}">
													<ul class="list-unstyled">
														@foreach($profile->groups($profile->id) as $group)
															@if($group->type=='curation')
																@if(in_array($profile['id'], unserialize($group->target_acounts)))
																	<li><input id="{{$profile['account_id']}}-{{$group->id}}" class="check-toog left-toog" type="checkbox" value="{{$group->id}}" name="group_id[]" checked><label for="{{$profile['account_id']}}-{{$group->id}}"> {{$group->name}}</label> </li>
																@else
																	<li><input id="{{$profile['account_id']}}-{{$group->id}}" class="check-toog left-toog" type="checkbox" value="{{$group->id}}" name="group_id[]"><label for="{{$profile['account_id']}}-{{$group->id}}"> {{$group->name}}</label> </li>
																@endif
															@endif
														@endforeach     	
													</ul>
												</div>
												<div role="tabpanel" class="tab-pane" id="rssauto-{{$profile['account_id']}}">
													<ul class="list-unstyled">
														@foreach($profile->groups($profile->id) as $group)
															@if($group->type=='rss-automation')
																@if(in_array($profile['id'], unserialize($group->target_acounts)))
																	<li><input id="{{$profile['account_id']}}-{{$group->id}}" class="check-toog left-toog" type="checkbox" value="{{$group->id}}" name="group_id[]" checked><label for="{{$profile['account_id']}}-{{$group->id}}">  {{$group->name}}</label> </li>
																@else
																	<li><input id="{{$profile['account_id']}}-{{$group->id}}" class="check-toog left-toog" type="checkbox" value="{{$group->id}}" name="group_id[]"> <label for="{{$profile['account_id']}}-{{$group->id}}">  {{$group->name}}</label> </li>
																@endif
															@endif
														@endforeach     	
													</ul>
												</div>
											</div>
										</div>
										<button type="submit" class="btn btn-default">Save</button>
									</form>
								</li>
							</ul>
							@endif
						</span>
						</td> 
						<td>
						<form method="post" action="" id="active-deactive-account-{{$profile['account_id']}}" class="active-deactive-account">
							{{ csrf_field() }}
							<input type="hidden" name="ids" value="{{$profile['id']}}">
							<input id="act-{{$profile['account_id']}}" type="checkbox" @if($profile['status'] == '1') checked @endif name="active_inactive" class="check-toog left-toog">
							<label for="act-{{$profile['account_id']}}"></label>
						</form>
						</td> 
					</tr>
					@endif
				@endforeach
				</tbody> 
			</table>
		</div>
	</div>
</div>
@endsection
