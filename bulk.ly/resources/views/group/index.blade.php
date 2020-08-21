@extends('layouts.app')
@section('content')
    <div class="container-fluid app-body">
        <div class="row">
            <div class="col-sm-4 group-col">
                <h3>Pending <span class="count"></span></h3>
                <div class="panel panel-default">
                    @if ($type=='upload')
                        <div class="panel-body text-center">
                            <br>
                            <div class="dropdown">
                                <form id="csv-to-content-upload" action="" method="POST" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <label class="btn btn-default width-btn btn-dc" for="file-upload">+ Upload
                                            Content (CSV)</label>
                                        <input class="hide" id="file-upload" type="file" name="csv">
                                    </div>
                                </form>
                            </div>
                            <a target="_blank" href="https://bulk.ly/csv/bulkly-content-upload.csv">
                                <small>Click here for a sample CSV file</small>
                            </a>
                            <br>
                            <br>
                            <a class="btn btn-default width-btn import_from_buffer" href="#">+ Import From Buffer</a>
                            <br>
                            <br>
                            <div class="dropdown">
                                <button id="AddContentOnline" type="button" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false" class="btn btn-default width-btn">
                                    + Add Content Online
                                </button>
                                <ul class="dropdown-menu dropdown-center dropdown-pop add-content-online-dropdown"
                                    aria-labelledby="AddContentOnline">
                                    <form id="add-content-online" method="POST">
                                        {{ csrf_field() }}
                                        <div class="form-group">STATUS UPDATE:</div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="text" id="text"
                                                   placeholder="Type in your social media update here...">
                                        </div>
                                        <div class="form-group">
                                            <input type="url" class="form-control" name="url" id="url"
                                                   placeholder="URL: Enter a link to add to your update (optional)">
                                        </div>
                                        <div class="form-group">
                                            <input type="url" class="form-control" name="image" id="image"
                                                   placeholder="Image: Enter a URL of an image you would like to attach to your update (optional)">
                                        </div>
                                        <button type="submit" class="btn btn-default width-xl-btn btn-center btn-dc">
                                            Save
                                        </button>
                                    </form>
                                </ul>
                            </div>
                            <br>
                        </div>
                    @endif

                    @if ($type=='curation')
                        <div class="panel-body text-center">
                            <br>
                            <div class="dropdown">
                                <form id="csv-to-curation-upload" method="POST" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <label for="file-curation" class="btn btn-default width-btn btn-dc">+ Upload RSS
                                            Feeds</label>
                                        <input class="hide" id="file-curation" type="file" name="csv">
                                    </div>
                                </form>
                            </div>
                            <a target="_blank" href="https://bulk.ly/csv/bulkly-content-curation.csv">
                                <small>Click here for a sample CSV file</small>
                            </a>
                            <br>
                            <br>
                            <br>
                            <br>

                            <div class="dropdown">
                                <button id="AddContentOnline" type="button" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false" class="btn btn-default width-btn">
                                    + Add Rss Feeds Online
                                </button>
                                <ul class="dropdown-menu dropdown-center dropdown-pop drop-red"
                                    aria-labelledby="AddContentOnline">
                                    <form id="add-curation-online" method="POST">
                                        {{ csrf_field() }}
                                        <div class="form-group">RSS URL:</div>
                                        <div class="form-group">
                                            <input type="url" class="form-control" name="url" id="url"
                                                   placeholder="Enter the RSS feed URL to curate content from here...">
                                        </div>
                                        <button type="submit" class="btn btn-default width-btn btn-dc btn-center">
                                            Save
                                        </button>
                                    </form>
                                </ul>
                            </div>


                            <br>
                        </div>
                    @endif

                    @if ($type=='rss-automation')
                        <div class="panel-body text-center">
                            <br>
                            <div class="dropdown">
                                <form id="csv-to-rss-automation-upload" method="POST" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <label for="file-rss-automation" class="btn btn-default width-btn btn-dc"> +
                                            Upload RSS Feeds</label>
                                        <input id="file-rss-automation" type="file" name="csv" class="hide">
                                    </div>
                                </form>
                            </div>
                            <br>
                            <a target="_blank" href="https://bulk.ly/csv/bulkly-rss-automation.csv">
                                <small>Click here for a sample CSV file</small>
                            </a>
                            <br>
                            <br>
                            <br>
                            <br>
                            <div class="dropdown">
                                <button id="AddContentOnline" type="button" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false" class="btn btn-default width-btn">
                                    + Add Rss Feeds Online
                                </button>
                                <ul class="dropdown-menu dropdown-center dropdown-pop drop-red"
                                    aria-labelledby="AddContentOnline">
                                    <form id="add-rss-automation-online" method="POST">
                                        {{ csrf_field() }}
                                        <div class="form-group">RSS:</div>
                                        <div class="form-group">
                                            <input type="url" class="form-control" name="url"
                                                   placeholder="Enter the RSS feed URL to automatically source content from here...">
                                        </div>
                                        <div class="hashtag rss">
                                            <div class="form-group">
                                                <span class="fa fa-facebook"></span>
                                                <input type="text" class="form-control" name="fb"
                                                       placeholder="FACEBOOK HASHTAG: Enter a hashtag to add to your Facebook update (optional)">
                                            </div>
                                            {{--<div class="form-group">
                                                <span class="fa fa-google-plus"></span>
                                                <input type="text" class="form-control" name="g"
                                                       placeholder="GOOGLE+ HASHTAG: Enter a hashtag to add to your Google+ update (optional)">
                                            </div>--}}
                                            <div class="form-group">
                                                <span class="fa fa-linkedin"></span>
                                                <input type="text" class="form-control" name="in"
                                                       placeholder="LinkedIn HASHTAG: Enter a hashtag to add to your LinkedIn update (optional)">
                                            </div>
                                            <div class="form-group">
                                                <span class="fa fa-twitter"></span>
                                                <input type="text" class="form-control" name="tw"
                                                       placeholder="Twitter HASHTAG: Enter a hashtag to add to your Twitter update (optional)">
                                            </div>
                                        </div>
                                        <button type="submit" class="btn width-btn btn-dc btn-center"> Save</button>
                                    </form>
                                </ul>
                            </div>
                            <br>
                        </div>
                    @endif


                </div>
                <div class="group-items pending" data-status="pending">
                    @foreach ($user->groups as $group)
                        @if($group->status == 0 && $group->type==$type)
                            <div class="panel panel-default group-single"
                                 onclick="selectThisGroup(this, event)"
                                 oncontextmenu="event.stopPropagation();event.preventDefault();reqForEdit()"
                                 data-id="{{$group->id}}"
                                 data-status="pending">
                                <div class="panel-body">
                                    <div class="media">
                                        <div class="media-left media-middle">
                                            @if($type=='upload')
                                                <a href="{{route('content-pending', $group->id)}}">
                                                    @endif
                                                    @if($type=='curation')
                                                        <a href="{{route('content-curation-pending', $group->id)}}">
                                                            @endif
                                                            @if($type=='rss-automation')
                                                                <a href="{{route('rss-automation-pending', $group->id)}}">
                                                                    @endif
                                                                    {{ substr($group->name, 0, 1) }}
                                                                </a>
                                        </div>
                                        <div class="media-body media-middle">
                                            @if($type=='upload')
                                                <a href="{{route('content-pending', $group->id)}}">
                                                    @endif
                                                    @if($type=='curation')
                                                        <a href="{{route('content-curation-pending', $group->id)}}">
                                                            @endif
                                                            @if($type=='rss-automation')
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


            <div class="col-sm-4 group-col">
                <h3>Active <span class="count"></span></h3>
                <div class="group-items active" data-status="active">
                    @foreach ($user->groups as $group)
                        @if($group->status == 1 && $group->type==$type)
                            <div class="panel panel-default group-single"
                                 onclick="selectThisGroup(this, event)"
                                 oncontextmenu="event.stopPropagation();event.preventDefault();reqForEdit()"
                                 data-id="{{$group->id}}"
                                 data-status="active">
                                <div class="panel-body">
                                    <div class="media">
                                        <div class="media-left media-middle">

                                            @if($group->type=='upload')
                                                <a href="{{route('content-active', $group->id)}}">
                                                    @endif
                                                    @if($group->type=='curation')
                                                        <a href="{{route('content-curation-active', $group->id)}}">
                                                            @endif
                                                            @if($group->type=='rss-automation')
                                                                <a href="{{route('rss-automation-active', $group->id)}}">
                                                                    @endif

                                                                    {{ substr($group->name, 0, 1) }}
                                                                </a>
                                        </div>
                                        <div class="media-body media-middle">


                                            @if($group->type=='upload')
                                                <a href="{{route('content-active', $group->id)}}">
                                                    @endif
                                                    @if($group->type=='curation')
                                                        <a href="{{route('content-curation-active', $group->id)}}">
                                                            @endif
                                                            @if($group->type=='rss-automation')
                                                                <a href="{{route('rss-automation-active', $group->id)}}">
                                                                    @endif
                                                                    <h4 class="media-heading">{{$group->name}}</h4>
                                                                    <p>
                                                                        <i class="fa fa-clock-o"></i>
                                                                        <small>
                                                                            @if(!$group->start_time)
                                                                                Schedule not set
                                                                            @else
                                                                                {{$group->frequency}} @if ($group->frequency=='1')
                                                                                    post @else posts @endif
                                                                                per  @if($group->interval=='hourly')
                                                                                    hour @elseif($group->interval=='daily')
                                                                                    day @elseif($group->interval=='weekly')
                                                                                    week @elseif ($group->interval=='monthly')
                                                                                    month @endif
                                                                            @endif
                                                                        </small>
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


            <div class="col-sm-4 group-col">
                <h3>Completed <span class="count"></span></h3>
                <div class="group-items completed" data-status="completed">
                    @foreach ($user->groups as $group)
                        @if($group->status == 2 && $group->type==$type)
                            <div class="panel panel-default group-single"
                                 onclick="selectThisGroup(this, event)"
                                 oncontextmenu="event.stopPropagation();event.preventDefault();reqForEdit()"
                                 data-id="{{$group->id}}"
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

    <div class="modal fade" id="group-ids-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="group-bl-ids" onsubmit="removeGroupIds(this, event)">
                    {{csrf_field()}}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Remove Selected Group(s)</h4>
                    </div>
                    <div class="modal-body">
                        <p>
                            Are you sure you want to delete all the selected groups?
                            Once deleted, we can't recover the groups.
                            <br><br>
                            You can also close this notice and <code>ctrl + click</code> on groups
                            you want to exclude from selecting. Then right click to delete your groups when ready.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="ids" value="">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="group-ids-selection-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">What group(s) would you like to select?</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" onchange="selectAllGroupType()" id="allGroup-Selection"> All
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" onchange="selectThisGroupType()" id="pendingGroup-Selection"> Pending
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" onchange="selectThisGroupType()" id="activeGroup-Selection"> Active
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" onchange="selectThisGroupType()" id="completedGroup-Selection"> Completed
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="ids" value="">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="deselectAll()">Cancel</button>
                    <button type="submit" class="btn btn-primary" data-dismiss="modal" onclick="reqForEdit()">Select</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        var allSelection = 0;
        // ==========================
        // Group wise Group Delete
        // ==========================
        function removeGroupIds(trigger, e) {
            e.stopPropagation();
            e.preventDefault();
            var ids = [];
            var idsContent = $('.bl-selected');
            $.each(idsContent, function (i, v) {
                ids.push($(v).attr('data-id'));
            });
            var form = $('#group-bl-ids');
            form.find('input[name="ids"]').val(ids.join(','));
            var data = form.serializeArray();
            $.ajax({
                type: "POST",
                url: '/group-delete/selected',
                data: data,
                success: function success(msg) {
                    window.location.href = '';
                },
                error: function error(xhr, ajaxOptions, thrownError) {
                    alert('Something is not right. Please try again.')
                }
            });
        }

        function reqForEdit() {
            var modal = $('#group-ids-modal');
            var ids = $('.bl-selected');
            if (ids.length > 0) {
                modal.modal('show');
            }
        }
        function deselectAll() {
            $('.group-single').removeClass('bl-selected');
            allSelection = 0;
            $('#allGroup-Selection').prop('checked', false);
            $('#pendingGroup-Selection').prop('checked', false);
            $('#activeGroup-Selection').prop('checked', false);
            $('#completedGroup-Selection').prop('checked', false);
        }
        function selectThisGroupType(){
            var all = $('#allGroup-Selection');
            var pendingV = $('#pendingGroup-Selection').prop('checked');
            var activeV = $('#activeGroup-Selection').prop('checked');
            var completedV = $('#completedGroup-Selection').prop('checked');

            if(pendingV===true && activeV===true && completedV===true){
                all.prop('checked', true);
                $('.group-single').removeClass('bl-selected').addClass('bl-selected');
            } else {
                all.prop('checked', false);
                if(pendingV === true){
                    $('.group-items[data-status="pending"]').find('.group-single').removeClass('bl-selected').addClass('bl-selected');
                } else {
                    $('.group-items[data-status="pending"]').find('.group-single').removeClass('bl-selected');
                }
                if(activeV === true){
                    $('.group-items[data-status="active"]').find('.group-single').removeClass('bl-selected').addClass('bl-selected');
                } else {
                    $('.group-items[data-status="active"]').find('.group-single').removeClass('bl-selected');
                }
                if(completedV === true){
                    $('.group-items[data-status="completed"]').find('.group-single').removeClass('bl-selected').addClass('bl-selected');
                } else {
                    $('.group-items[data-status="completed"]').find('.group-single').removeClass('bl-selected');
                }
            }
        }
        function selectAllGroupType(){
            var all = $('#allGroup-Selection');
            var pending = $('#pendingGroup-Selection');
            var active = $('#activeGroup-Selection');
            var completed = $('#completedGroup-Selection');

            if(all.prop('checked')===true){
                pending.prop('checked', true);
                active.prop('checked', true);
                completed.prop('checked', true);
                $('.group-single').removeClass('bl-selected').addClass('bl-selected');
            } else {
                pending.prop('checked', false);
                active.prop('checked', false);
                completed.prop('checked', false);
                $('.group-single').removeClass('bl-selected');
            }
        }
        function selectThisGroup(trigger, e) {
            if (e.ctrlKey) {
                e.stopPropagation();
                e.preventDefault();
                var trigger = $(trigger);
                var type = trigger.attr('data-select');
                if (type != 1) {
                    trigger.attr('data-select', 1);
                    trigger.removeClass('bl-selected');
                    trigger.addClass('bl-selected');
                } else {
                    trigger.attr('data-select', 0);
                    trigger.removeClass('bl-selected');
                }
                if(allSelection === 0){
                    allSelection = 1;
                    $('#group-ids-selection-modal').modal('show');
                }
            }
        }
    </script>

@endsection
