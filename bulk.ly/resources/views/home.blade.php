@extends('layouts.app')
@section('content')
    <div class="container-fluid app-body app-home">
        @if($user->name)
            <h1> Hello {{ucwords($user->first_name) }}!</h1>
        @endif
        <div class="row">
            <div class="col-md-12">
            @if($user->plansubs())
                <?php

                $timestamp = $user->plansubs()['subscription']->current_period_start;
                if (!$timestamp) {
                    $timestamp = date('Y-m');
                    $timestamp = date('Y-m-d H:i:s', strtotime($timestamp));

                }
                $user_current_pph = \Bulkly\BufferPosting::where('user_id', $user->id)->where('created_at', '>', $timestamp)->count();
                ?>
                @if($user->plansubs()['plan'])
                    @if( $user_current_pph > $user->plansubs()['plan']->ppm)
                        <!--
    				<div class="alert alert-danger text-center" role="alert"> 
    					Whoops! You've reached your monthly limit of {{$user->plansubs()['plan']->ppm}} which is the number of posts you can send to Buffer. <b>Need to send more?</b> <a href="/settings">Visit your settings page to upgrade your account</a>.
    					</div> 
    				-->
                    @endif
                @endif
            @else
                <!--<div class="alert alert-danger text-center" role="alert"> You have not any active subscription plan now</a>.
				</div> -->

                @endif



                @unless(session()->has('buffer_token'))
                    <div class="panel panel-default">
                        <div class="panel-body notconnected">
                            <div class="alert text-center" role="alert">
                                <!--<h4>Please connect your Buffer Account</h4>
                                <p>When you click the button below, you'll grant access to Bulkly to add social media updates on your behalf.</p> -->

                                <h4>Ready to get started? <br> Simply connect Bulkly with your Buffer account</h4>
                                <br><br>
                                <p><a class="btn btn-default width-btn btn-dc"
                                      href="https://bufferapp.com/oauth2/authorize?client_id={{env('BUFFER_CLIENT_ID')}}&redirect_uri={{env('BUFFER_REDIRECT')}}&response_type=code">Connect
                                        Your Buffer account</a></p>
                            </div>
                        </div>
                    </div>
                @endunless
            </div>
        </div>
        @if(session()->has('buffer_token'))


            <div class="row">
                <div class="col-md-5">
                    <div class="panel panel-default channel-activity">
                        <div class="panel-body">
                            <h3>Channel Activity</h3>
                            <div class="media">
                                <div class="media-left">
                                    <canvas id="myChart" width="230" height="230">
                                    </canvas>
                                    <span class="total-post"></span>
                                </div>
                                <div class="media-body media-middle">
                                    <ul class="list-unstyled">
                                        @foreach($services as $key => $service)
                                            <li data-count=""><i
                                                        class="{{$service->account_service}} fa fa-circle"></i> {{ucwords($service->account_service)}}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <script type="text/javascript">
                                var data = {
                                    labels: [@foreach($services as $key => $service)"{{$service->account_service}}",@endforeach],
                                    datasets: [
                                        {
                                            data: [@foreach($services as $key => $service)"{{$service->count}}",@endforeach],
                                            backgroundColor: ["#25396e", "#5584ff", "#5ccae7", "#3755a4",]
                                        }]
                                };
                            </script>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h3>Posting Frequency</h3>
                            <div style="overflow: hidden; margin-top: 35px;" class="layer">
                                <canvas style="margin-top: -25px; " id="homePostingFrequency" width="500" height="140">

                                </canvas>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row home-block">
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h3>Recent Activity</h3>
                            <div class="activities">

                                @foreach($activities as $activity)
                                    @if(\Bulkly\SocialAccounts::find($activity->account_id) != null)
                                        <div class="panel panel-default activity">
                                            <div class="panel-body">
                                                <div class="media">
                                                    <div class="media-left media-middle">
                                                        <i class="fa fa-{{\Bulkly\SocialAccounts::find($activity->account_id)->type}}"></i>
                                                        <img width="50"
                                                             src="{{\Bulkly\SocialAccounts::find($activity->account_id)->avatar}}">
                                                    </div>
                                                    <div class="media-body media-middle">
                                                        <h4 class="media-heading"> posted
                                                            <strong> {{ substr($activity->post_text, 0, 30) }}
                                                                ... </strong>
                                                        </h4>
                                                        <p><i class="fa fa-clock-o"></i> <span
                                                                    data-sent="{{strtotime($activity->sent_at)}}"></span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h3>Recent Groups</h3>
                            @foreach ($user->groups as $group)
                                @if($group->status == 0)
                                    <div class="panel panel-default group-single" data-id="{{$group->id}}"
                                         data-status="pending">
                                        <div class="panel-body">
                                            <div class="media">
                                                <div class="media-left media-middle">
                                                    @if($group->type=='upload')
                                                        <a href="{{route('content-pending', $group->id)}}">
                                                            @endif
                                                            @if($group->type=='curation')
                                                                <a href="{{route('content-curation-pending', $group->id)}}">
                                                                    @endif
                                                                    @if($group->type=='rss-automation')
                                                                        <a href="{{route('rss-automation-pending', $group->id)}}">
                                                                            @endif
                                                                            {{ substr($group->name, 0, 1) }}
                                                                        </a>
                                                </div>
                                                <div class="media-body media-middle">
                                                    @if($group->type=='upload')
                                                        <a href="{{route('content-pending', $group->id)}}">
                                                            @endif
                                                            @if($group->type=='curation')
                                                                <a href="{{route('content-curation-pending', $group->id)}}">
                                                                    @endif
                                                                    @if($group->type=='rss-automation')
                                                                        <a href="{{route('rss-automation-pending', $group->id)}}">
                                                                            @endif
                                                                            <h4 class="media-heading">{{$group->name}}</h4>
                                                                            <p><i class="fa fa-clock-o"></i>
                                                                                <small> Schedule not set</small>
                                                                            </p>
                                                                        </a>
                                                </div>
                                                <div class="media-left media-middle">
                                                    @include('group.grouppop')
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-body  group-items completed">
                            <h3>Completed Groups</h3>
                            @foreach ($user->groups as $group)
                                @if($group->status == 2)
                                    <div class="panel panel-default group-single" data-id="{{$group->id}}"
                                         data-status="completed">
                                        <div class="panel-body">
                                            <div class="media">
                                                <div class="media-left media-middle">
                                                    @if($group->type=='upload')
                                                        <a href="{{route('content-completed', $group->id)}}">
                                                            @endif
                                                            @if($group->type=='curation')
                                                                <a href="{{route('content-curation-completed', $group->id)}}">
                                                                    @endif
                                                                    @if($group->type=='rss-automation')
                                                                        <a href="{{route('rss-automation-completed', $group->id)}}">
                                                                            @endif
                                                                            {{ substr($group->name, 0, 1) }}
                                                                        </a>
                                                </div>
                                                <div class="media-body media-middle">
                                                    @if($group->type=='upload')
                                                        <a href="{{route('content-completed', $group->id)}}">
                                                            @endif
                                                            @if($group->type=='curation')
                                                                <a href="{{route('content-curation-completed', $group->id)}}">
                                                                    @endif
                                                                    @if($group->type=='rss-automation')
                                                                        <a href="{{route('rss-automation-completed', $group->id)}}">
                                                                            @endif
                                                                            <h4 class="media-heading">{{$group->name}}</h4>
                                                                            <p><i class="fa fa-check-circle-o"></i>
                                                                                <small> Completed</small>
                                                                            </p>
                                                                        </a>
                                                </div>
                                                <div class="media-left media-middle">
                                                    @include('group.grouppop')
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>


        @endif
    </div>
@endsection