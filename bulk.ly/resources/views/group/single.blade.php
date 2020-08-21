@extends('layouts.app')
@section('content')
<div class="container-fluid app-body">
	<style type="text/css">
		.rss_lists .social-post:first-child .inst:after {
			font: normal normal normal 20px/1 FontAwesome;
		    position: absolute;
		    top: -70px;
		    width: 50px;
		    height: 50px;
		    background: #8492af;
		    color: #fff;
		    line-height: 50px;
		    text-align: center;
		    border-radius: 50%;
		    margin-left: -30px;
		    content: "\f16d";
		}
	</style>
	<div class="row">
		<div class="col-md-12 group-page">
			@include('group.nav')
			<div class="panel panel-default group-posts type-{{$group->type}} status-{{$group->status}}">
				<input type="hidden" name="sorting">
				<div class="panel-heading text-center">
					<ul class="list-inline">
						@if($group->type =='upload')
						<li class="dropdown">
							<form id="csv-to-content-upload" action="" method="POST" enctype="multipart/form-data">
								{{ csrf_field() }}
								<div class="form-group">
									<label class="btn btn-default width-btn btn-dc" for="file-upload">+ Upload Content (CSV)</label>
									<input class="hide" id="file-upload" type="file" name="csv">
								</div>
								<a class="sample_file_link"   target="_blank" href="https://bulk.ly/csv/bulkly-content-upload.csv"><small>Click here for a sample CSV file</small></a>
							</form>
						</li>
						<li class="dropdown">
							<button id="AddContentOnline" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"  class="btn btn-default navbar-btn width-btn">
							+ Add Content Online
						  	</button>
							<ul class="dropdown-menu dropdown-center dropdown-pop add-content-online-dropdown-ingroup" aria-labelledby="AddContentOnline">
								<form id="add-content-online-ingroup" method="POST">
									{{ csrf_field() }}
									<input type="hidden" name="group_id" value="{{$group->id}}">
									<div class="form-group">STATUS UPDATE:</div>
									<div class="form-group">
										<input type="text" class="form-control" name="text" id="text" placeholder="Type in your social media update here...">
									</div>
									<div class="form-group">
										<input type="url" class="form-control" name="url" id="url" placeholder="URL: Enter a link to add to your update (optional)">
									</div>
									<div class="form-group">
										<input type="url" class="form-control" name="image" id="image" placeholder="Image: Enter a URL of an image you would like to attach to your update (optional)">
									</div>
									<button type="submit" class="btn btn-default width-xl-btn btn-center btn-dc "> Save</button>
								</form>
						  	</ul>
						</li>
						<li>
							<form class="" action="/export-content" method="post">
								{{csrf_field()}}
								<input type="hidden" name="id" value="{{$group->id}}">
								<input type="submit" value="+ Export Content" id="ExportContent"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id="" class="btn btn-default navbar-btn width-btn">
						  	</button>
							</form>
							
						</li>
						<li class="dropdown">
							<form id="csv-to-reupload" action="" method="POST" enctype="multipart/form-data">
								{{ csrf_field() }}
								<div class="form-group">
									<label class="btn btn-default width-btn btn-dc" for="file-reupload">+ Reupload Content</label>
									<input class="hide" id="file-reupload" type="file" name="reupload">
								</div>
							</form>
						</li>
						@endif
						@if($group->type =='curation')
	                    <li class="dropdown">
	                        <form id="csv-to-curation-upload" method="POST" enctype="multipart/form-data">
	                            {{ csrf_field() }}
	                            <div class="form-group">
	                            	<label for="file-curation" class="btn btn-default width-btn btn-dc">+ Upload RSS Feeds</label>
	                                <input class="hide" id="file-curation" type="file" name="csv">
	                            </div>
	                            <a class="sample_file_link"  target="_blank" href="https://bulk.ly/csv/bulkly-content-curation.csv"><small>Click here for a sample CSV file</small></a>
	                        </form>
	                    </li>
						<li class="dropdown">
							<button id="AddContentOnline" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"  class="btn btn-default navbar-btn width-btn">
								+ Add RSS Feeds Online
							</button>
							<div class="dropdown-menu dropdown-center dropdown-pop drop-red" aria-labelledby="AddContentOnline">
								<form id="add-curation-online-ingroup" class="add-curation-online-ingroup" method="POST">
									{{ csrf_field() }}
									<input type="hidden" name="group_id" value="{{$group->id}}">
    	                            <div class="form-group">RSS:</div>
		                            <div class="form-group">
		                                <input type="url" class="form-control" name="url" id="url" placeholder="Enter the RSS feed URL to curate content from here...">
		                            </div>
		                            <button type="submit" class="btn btn-default btn-dc btn-round btn-center"> Save</button>
								</form>
							</div>
						</li>
						<li class="dropdown">
							<form id="curation-refresh" class="curation-refresh" method="POST">
								{{ csrf_field() }}
								<input type="hidden" name="group_id" value="{{$group->id}}">
								<button type="submit" class="btn btn-default navbar-btn width-btn"> Refresh Content</button>
							</form>
						</li>
						@endif
						@if($group->type =='rss-automation')
						<li class="dropdown">
	                        <form id="csv-to-rss-automation-upload" method="POST" enctype="multipart/form-data">
	                            {{ csrf_field() }}
	                            <div class="form-group">
	                            	<label for="file-rss-automation" class="btn btn-default width-btn btn-dc"> + Upload RSS Feeds</label>
	                                <input id="file-rss-automation" type="file" name="csv" class="hide">
	                            </div>
	                            <a class="sample_file_link"   target="_blank" href="https://bulk.ly/csv/bulkly-rss-automation.csv"><small>Click here for a sample CSV file</small></a>
	                        </form>
						</li>
						<li class="dropdown">
							<button id="AddContentOnline" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"  class="btn btn-default navbar-btn width-btn">
								+ Add RSS Feeds Online
							</button>
							<div class="dropdown-menu dropdown-center dropdown-pop drop-red" aria-labelledby="AddContentOnline">
								<form id="add-rss-automation-online-ingroup" method="POST">
									{{ csrf_field() }}
									<input type="hidden" name="group_id" value="{{$group->id}}">
    	                            <div class="form-group">RSS URL:</div>
		                            <div class="form-group">
		                                <input type="url" class="form-control" name="url"  placeholder="Enter the RSS feed URL to automatically source content from here...">
		                            </div>
									<div class="hashtag rss">
										<div class="form-group">
										<span class="fa fa-facebook"></span>
										    <input type="text" class="form-control" name="fb" placeholder="FACEBOOK HASHTAG: Enter a hashtag to add to your Facebook update (optional)">
										</div>
										<!--<div class="form-group">
										<span class="fa fa-google-plus"></span>
										    <input type="text" class="form-control" name="g" placeholder="GOOGLE+ HASHTAG: Enter a hashtag to add to your Google+ update (optional)">
										</div>-->
										<div class="form-group">
										<span class="fa fa-linkedin"></span>
										    <input type="text" class="form-control" name="in" placeholder="LinkedIn HASHTAG: Enter a hashtag to add to your LinkedIn update (optional)">
										</div>
										<div class="form-group">
										<span class="fa fa-twitter"></span>
										    <input type="text" class="form-control" name="tw" placeholder="Twitter HASHTAG: Enter a hashtag to add to your Twitter update (optional)">
										</div>
										<div class="form-group">
										<span class="fa fa-instagram"></span>
										    <input type="text" class="form-control" name="ins" placeholder="Instagram HASHTAG: Enter a hashtag to add to your Instagram update (optional)">
										</div>


									</div>
		                            <button type="submit" class="btn btn-default width-btn btn-dc btn-center"> Save</button>
								</form>
							</div>
						</li>
						@endif
					</ul>
					<br>
				</div>
				<div class="panel-body">
					@if($group->type=='upload' OR $group->type=='curation')
					<?php 
					if($group->shuffle===1){

					$postWithorder = $group->posts->shuffle();
					} else {
					$postWithorder = $group->posts;
					}
					function makeLinks($str) {
						$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
						$urls = array();
						$urlsToReplace = array();
						if(preg_match_all($reg_exUrl, $str, $urls)) {
							$numOfMatches = count($urls[0]);
							$numOfUrlsToReplace = 0;
							for($i=0; $i<$numOfMatches; $i++) {
								$alreadyAdded = false;
								$numOfUrlsToReplace = count($urlsToReplace);
								for($j=0; $j<$numOfUrlsToReplace; $j++) {
									if($urlsToReplace[$j] == $urls[0][$i]) {
										$alreadyAdded = true;
									}
								}
								if(!$alreadyAdded) {
									array_push($urlsToReplace, $urls[0][$i]);
								}
							}
							$numOfUrlsToReplace = count($urlsToReplace);
							for($i=0; $i<$numOfUrlsToReplace; $i++) {
								$str = str_replace($urlsToReplace[$i], "<a target=\"_blank\" href=\"".$urlsToReplace[$i]."\">".$urlsToReplace[$i]."</a> ", $str);
							}
							return $str;
						} else {
							return $str;
						}
					}
					?>
					<ul class="list-group post_list">
						@foreach ($postWithorder as $key => $post)
						<li class="list-group-item social-post" id="post-{{$post->id}}" data-srorid="{{$key}}" data-sorttime="{{strtotime($post->schedule_at)}}">
							<div class="media">
								
								<div class="media-body">
									<h4>{!! makeLinks($post->text) !!}</h4>
									<p class="mb-0">Url : <span class="urlup"><a target="_blank" href="{{$post->link}}">{{$post->link}}</a></span>
									@if($post->image)
									<br>Image : <span class="imageup"> <a target="_blank" href="{{$post->image}}">{{$post->image}}</a></span>
									@endif
									</p>
								</div>

								@if($group->status == '1' OR $group->status == '2')
									<div class="media-right media-middle data-sent">
										<div style="width: 200px;">
											<p class="mb-0">
												@if($post->status == 1)
													@if($post->sent_at)
														<i class="fa fa-clock-o"></i>
														<small class="text-success"> Sent <span data-sent="{{strtotime($post->sent_at)}}"></span> </small>
													@endif
												@elseif($post->status == 10)
													<i class="fa fa-clock-o"></i>
													<small class="text-danger" data-toggle="tooltip" title="{{$post->reason}}"> Error Sending </small>
												@else
													@if(!$post->sent_at)
														<i class="fa fa-clock-o"></i>
														<small> Never sent</small>
													@endif
												@endif
											</p>
										</div>
									</div>
								@endif

 								<div class="media-right media-middle text-center">
									<div style="width: 100px; position: relative;z-index: 3;">
										<div class="dropdown">
											<div class="dLabelbutton" id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn">
												<span class="fa fa-ellipsis-v"></span>
											</div>
											<ul class="dropdown-menu dropdown-pop post-update" aria-labelledby="dLabel" style="left: initial; right: 0px;">
												<li>
													<form class="form-horizontal post-update" id="post-update-{{rand()}}">
														{{ csrf_field() }}
														<input type="hidden" name="post_id" value="{{$post->id}}">
														<p>STATUS UPDATE:</p>
														<div class="form-group">
															<div class="col-sm-12">
															  <input type="text" class="form-control" name="text" value="{{$post->text}}" placeholder="Type in your social media update here...">
															</div>
														</div>
														<div class="form-group">
															<div class="col-sm-12">
															  <input type="text" class="form-control" name="link" value="{{$post->link}}" placeholder="URL: Enter a link to add to your update (optional)">
															</div>
														</div>
														<div class="form-group">
															<div class="col-sm-12">
															  <input type="text" class="form-control" name="image" value="{{$post->image}}" placeholder="Image: Enter a URL of an image you would like to attach to your update (optional)">
															</div>
														</div>
														<div class="text-center-btn">
														<button type="submit" class=" btn btn-default width-btn btn-dc btn-center">Save</button>
														<button type="button" class=" btn btn-default width-btn btn-dc btn-center send_now_post">Send Now</button>
														<button type="button" class="btn btn-default  btn-icon-round btn-center send_post_trash"><i class="fa fa-trash"></i></button>
														</div>
													</form>
												</li>
											 </ul>
										</div>
									</div>
								</div>
								
							</div>
						</li>
						@endforeach


					</ul>
					@endif
					@if($group->type=='rss-automation')

					<br>
					<br>
					<br>
					<br>
					<div class="ree_top"></div>
					<?php 
					if($group->shuffle===1){
						$postWithorder = $group->posts->shuffle();
					} else {
						$postWithorder = $group->posts;
					}
					?>
						<ul class="list-group post_list rss_lists">
							@foreach ($postWithorder as $key => $post)
							<li class="list-group-item social-post" id="post-{{$post->id}}" data-srorid="{{$key}}" data-sorttime="{{strtotime($post->schedule_at)}}">
								<div class="media">
									<div class="media-body">
										<h4 title="{{$post->rsslink}}">{{ $post->rsslink }}</h4>
										<div>
											@if($group->status == '1' OR $group->status == '2')
												<p class="mb-0"><i class="fa fa-clock-o"></i> 
													@if(!$post->sent_at)
														<small> Never sent</small>
													@endif
													@if($post->sent_at)
														<small> Sent <span data-sent="{{strtotime($post->sent_at)}}"></span> </small>
													@endif
												</p>
			 								@endif
										</div>
									</div>
								  	<div class="media-right media-middle text-center fac">
										<div style="width: 150px;">
											{{unserialize($post->hash)['fb']}}
										</div>
								 	 </div> 
								  	<!--<div class="media-right media-middle text-center goo">
										<div style="width: 150px;">
											{{unserialize($post->hash)['g']}}
										</div>
								 	 </div>--> 
								  	<div class="media-right media-middle text-center lin">
										<div style="width: 150px;">
											{{unserialize($post->hash)['in']}}
										</div>
								 	 </div> 
								  	<div class="media-right media-middle text-center twit">
										<div style="width: 150px;">
											{{unserialize($post->hash)['tw']}}
										</div>
								 	 </div> 
								 	 <div class="media-right media-middle text-center inst">
										<div style="width: 150px;">
											@if(isset(unserialize($post->hash)['ins'])) {{unserialize($post->hash)['ins']}} @endif
										</div>
								 	 </div> 
								  	<div class="media-right media-middle text-center">
										<div style="width: 100px; position: relative;z-index: 3;">
											<div class="dropdown">
												<div class="dLabelbutton" id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn">
													<span class="fa fa-ellipsis-v"></span>
												</div>
											  	<ul class="dropdown-menu dropdown-pop post-update" aria-labelledby="dLabel" style="left: initial; right: 0px; width:">
													<li>
														<form class="form-horizontal post-update" id="post-update-{{rand()}}">
															{{ csrf_field() }}
															<input type="hidden" name="post_id" value="{{$post->id}}">
															<p>RSS UPDATE:</p>
															<div class="form-group">
															    <div class="col-sm-12">
															      <input type="text" class="form-control" name="rsslink" value="{{$post->rsslink}}">
															    </div>
															</div>
														<div class="hashtag rss">
															<div class="form-group">
															    <div class="col-sm-12">
															    <span class="fa fa-facebook"></span>
															      <input type="text" class="form-control" name="fb" value="{{unserialize($post->hash)['fb']}}" placeholder="Facebook hash">
															    </div>
															</div>
															<!--<div class="form-group">
															    <div class="col-sm-12">
															    <span class="fa fa-google-plus"></span>
															      <input type="text" class="form-control" name="g" value="{{unserialize($post->hash)['g']}}" placeholder="Google + hash">
															    </div>
															</div>-->
															<div class="form-group">
															    <div class="col-sm-12">
															    <span class="fa fa-linkedin"></span>
															    
															      <input type="text" class="form-control" name="in" value="{{unserialize($post->hash)['in']}}" placeholder="Linkedin hash">
															    </div>
															</div>
															<div class="form-group">
															    <div class="col-sm-12">
															     <span class="fa fa-twitter"></span>
															      <input type="text" class="form-control" name="tw" value="{{unserialize($post->hash)['tw']}}" placeholder="Twitter hash">
															    </div>
															</div>
															<div class="form-group">
															    <div class="col-sm-12">
															     <span class="fa fa-instagram"></span>
															      <input type="text" class="form-control" name="ins" value="@if(isset(unserialize($post->hash)['ins'])){{unserialize($post->hash)['ins']}}@endif" placeholder="Instagram hash">
															    </div>
															</div>

														</div>
														<div class="text-center-btn">
															
														  	<button type="submit" class=" btn btn-default width-btn btn-dc btn-center">Save</button>
															<button type="button" class=" btn btn-default width-btn btn-dc btn-center send_now_post">Send Now</button>
															<button type="button" class="btn btn-default  btn-icon-round btn-center send_post_trash"><i class="fa fa-trash"></i></button>
														</div>
														</form>
													</li>
											 	 </ul>
											</div>
										</div>
								 	 </div> 
								</div>
							</li>
							@endforeach
						</ul>
						@endif
				</div>
			</div>
		</div>
	</div>
</div>

<script>
    setTimeout(function () {
        $('[data-toggle="tooltip"]').tooltip();
    }, 100);
</script>
@endsection