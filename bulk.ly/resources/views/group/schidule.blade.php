<ul class="dropdown-menu dropdown-center schedule-update" aria-labelledby="Schedule">
    <li>
        <form class="container-fluid" id="schedule-update" method="POST" action="">
            {{ csrf_field() }}
            <input type="hidden" name="group_id" value="{{$group->id}}">
            <div class="schedule">
                <div class="radio text-center">
                    <h4>Set posting frequency</h4>

                    <label class="radio-inline">
                        <input type="radio" name="postingFrequency" value="hourly"
                               @if ($group->interval=='hourly') checked @endif> <span>Hourly</span>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="postingFrequency" value="daily"
                               @if ($group->interval=='daily') checked @endif> <span>Daily</span>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="postingFrequency" value="weekly"
                               @if ($group->interval=='weekly') checked @endif> <span>Weekly</span>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="postingFrequency" value="monthly"
                               @if ($group->interval=='monthly') checked @endif> <span>Monthly</span>
                    </label>
                </div>
                <div class="form-group" style="margin-bottom: 0px;">
                    <input class="form-control" type="range" name="quantity" min="1" max="31"
                           value="@if($group->frequency){{$group->frequency}}@endif">
                    <div class="text-center">
                        <span class="quan"></span> X
                        </span>
                    </div>
                    <input class="form-control" type="hidden" name="interval">
                    <div class="form-group inline-form-group">
                        <input class="check-toog left-toog" type="checkbox" name="start" id="Start" checked>
                        <label for="Start">Start Date</label>
                        <br>
                        <table class="table b-00 start">
                            <tr>
                                <td class="pl-0">
                                    <?php

                                    if (is_null($group->start_time)) {
                                        $group->start_time = date('Y-m-d H:i:s');
                                    }

                                    if ($group->status == 2) {
                                        $car = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
                                    } else {
                                        $carcurr = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $group->start_time);
                                        $car = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));

                                        if ($carcurr > $car) {
                                            $car = $carcurr;
                                        } else {
                                            $car = $car;

                                        }
                                    }







                                    $car->setTimeZone(new \DateTimeZone(\Auth::user()->timezone));



                                    $group->start_time = $car->toDateTimeString();

                                    //print_r($group->start_time);


                                    ?>
                                    <select class="form-control month" name="month">
                                        <option value="*">Month</option>
                                        <option @if(explode('-', $group->start_time)[1]=='01') selected="selected"
                                                @endif value="00">Jan
                                        </option>
                                        <option @if(explode('-', $group->start_time)[1]=='02') selected="selected"
                                                @endif value="01">Feb
                                        </option>
                                        <option @if(explode('-', $group->start_time)[1]=='03') selected="selected"
                                                @endif value="02">Mar
                                        </option>
                                        <option @if(explode('-', $group->start_time)[1]=='04') selected="selected"
                                                @endif value="03">Apr
                                        </option>
                                        <option @if(explode('-', $group->start_time)[1]=='05') selected="selected"
                                                @endif value="04">May
                                        </option>
                                        <option @if(explode('-', $group->start_time)[1]=='06') selected="selected"
                                                @endif value="05">Jun
                                        </option>
                                        <option @if(explode('-', $group->start_time)[1]=='07') selected="selected"
                                                @endif value="06">Jul
                                        </option>
                                        <option @if(explode('-', $group->start_time)[1]=='08') selected="selected"
                                                @endif value="07">Aug
                                        </option>
                                        <option @if(explode('-', $group->start_time)[1]=='09') selected="selected"
                                                @endif value="08">Sep
                                        </option>
                                        <option @if(explode('-', $group->start_time)[1]=='10') selected="selected"
                                                @endif value="09">Oct
                                        </option>
                                        <option @if(explode('-', $group->start_time)[1]=='11') selected="selected"
                                                @endif value="10">Nov
                                        </option>
                                        <option @if(explode('-', $group->start_time)[1]=='12') selected="selected"
                                                @endif value="11">Dec
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control day" name="day">
                                        <option value="*">Day</option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='1') selected="selected"
                                                @endif value="1">1
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='2') selected="selected"
                                                @endif value="2">2
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='3') selected="selected"
                                                @endif value="3">3
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='4') selected="selected"
                                                @endif value="4">4
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='5') selected="selected"
                                                @endif value="5">5
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='6') selected="selected"
                                                @endif value="6">6
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='7') selected="selected"
                                                @endif value="7">7
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='8') selected="selected"
                                                @endif value="8">8
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='9') selected="selected"
                                                @endif value="9">9
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='10') selected="selected"
                                                @endif value="10">10
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='11') selected="selected"
                                                @endif value="11">11
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='12') selected="selected"
                                                @endif value="12">12
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='13') selected="selected"
                                                @endif value="13">13
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='14') selected="selected"
                                                @endif value="14">14
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='15') selected="selected"
                                                @endif value="15">15
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='16') selected="selected"
                                                @endif value="16">16
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='17') selected="selected"
                                                @endif value="17">17
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='18') selected="selected"
                                                @endif value="18">18
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='19') selected="selected"
                                                @endif value="19">19
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='20') selected="selected"
                                                @endif value="20">20
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='21') selected="selected"
                                                @endif value="21">21
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='22') selected="selected"
                                                @endif value="22">22
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='23') selected="selected"
                                                @endif value="23">23
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='24') selected="selected"
                                                @endif value="24">24
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='25') selected="selected"
                                                @endif value="25">25
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='26') selected="selected"
                                                @endif value="26">26
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='27') selected="selected"
                                                @endif value="27">27
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='28') selected="selected"
                                                @endif value="28">28
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='29') selected="selected"
                                                @endif value="29">29
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='30') selected="selected"
                                                @endif value="30">30
                                        </option>
                                        <option @if(explode(' ', explode('-', $group->start_time)[2])[0]=='31') selected="selected"
                                                @endif value="31">31
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control year" name="year">
                                        <option value="*">Year</option>
                                        <option @if(explode('-', $group->start_time)[0]=='2017') selected="selected"
                                                @endif value="2017">2017
                                        </option>
                                        <option @if(explode('-', $group->start_time)[0]=='2018') selected="selected"
                                                @endif value="2018">2018
                                        </option>
                                        <option @if(explode('-', $group->start_time)[0]=='2019') selected="selected"
                                                @endif value="2019">2019
                                        </option>
                                        <option @if(explode('-', $group->start_time)[0]=='2020') selected="selected"
                                                @endif value="2020">2020
                                        </option>
                                    </select>
                                </td>
                                <td class="pr-0">
                                    <select class="form-control hour" name="hour">
                                        <option value="*">Time</option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '00') selected="selected"
                                                @endif value="00">12 AM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '01') selected="selected"
                                                @endif value="01">01 AM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '02') selected="selected"
                                                @endif value="02">02 AM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '03') selected="selected"
                                                @endif value="03">03 AM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '04') selected="selected"
                                                @endif value="04">04 AM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '05') selected="selected"
                                                @endif value="05">05 AM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '06') selected="selected"
                                                @endif value="06">06 AM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '07') selected="selected"
                                                @endif value="07">07 AM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '08') selected="selected"
                                                @endif value="08">08 AM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '09') selected="selected"
                                                @endif value="09">09 AM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '10') selected="selected"
                                                @endif value="10">10 AM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '11') selected="selected"
                                                @endif value="11">11 AM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '12') selected="selected"
                                                @endif value="12">12 PM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '13') selected="selected"
                                                @endif value="13">01 PM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '14') selected="selected"
                                                @endif value="14">02 PM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '15') selected="selected"
                                                @endif value="15">03 PM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '16') selected="selected"
                                                @endif value="16">04 PM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '17') selected="selected"
                                                @endif value="17">05 PM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '18') selected="selected"
                                                @endif value="18">06 PM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '19') selected="selected"
                                                @endif value="19">07 PM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '20') selected="selected"
                                                @endif value="20">08 PM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '21') selected="selected"
                                                @endif value="21">09 PM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '22') selected="selected"
                                                @endif value="22">10 PM
                                        </option>
                                        <option @if(explode(':', explode(' ', $group->start_time)[1])[0] == '23') selected="selected"
                                                @endif value="23">11 PM
                                        </option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        <input type="hidden" name="start_time">
                    </div>

                    <?php
                    if (isset($group->end_time)) {
                        $car = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $group->end_time);
                        $car->setTimeZone(new \DateTimeZone(\Auth::user()->timezone));
                        $group->end_time = $car->toDateTimeString();
                    }
                    ?>

                    <div class="form-group inline-form-group">
                        <input class="check-toog left-toog" type="checkbox" name="end" id="End"
                               @if ($group->end_time) checked @else  @endif>
                        <label for="End">End Date</label>
                        <br>
                        @if ($group->end_time)
                            <table class="table b-00 end">
                                <tr>
                                    <td class="pl-0">
                                        <select class="form-control month" name="month">
                                            <option value="*">Month</option>
                                            <option @if(explode('-', $group->end_time)[1]=='01') selected="selected"
                                                    @endif value="00">Jan
                                            </option>
                                            <option @if(explode('-', $group->end_time)[1]=='02') selected="selected"
                                                    @endif value="01">Feb
                                            </option>
                                            <option @if(explode('-', $group->end_time)[1]=='03') selected="selected"
                                                    @endif value="02">Mar
                                            </option>
                                            <option @if(explode('-', $group->end_time)[1]=='04') selected="selected"
                                                    @endif value="03">Apr
                                            </option>
                                            <option @if(explode('-', $group->end_time)[1]=='05') selected="selected"
                                                    @endif value="04">May
                                            </option>
                                            <option @if(explode('-', $group->end_time)[1]=='06') selected="selected"
                                                    @endif value="05">Jun
                                            </option>
                                            <option @if(explode('-', $group->end_time)[1]=='07') selected="selected"
                                                    @endif value="06">Jul
                                            </option>
                                            <option @if(explode('-', $group->end_time)[1]=='08') selected="selected"
                                                    @endif value="07">Aug
                                            </option>
                                            <option @if(explode('-', $group->end_time)[1]=='09') selected="selected"
                                                    @endif value="08">Sep
                                            </option>
                                            <option @if(explode('-', $group->end_time)[1]=='10') selected="selected"
                                                    @endif value="09">Oct
                                            </option>
                                            <option @if(explode('-', $group->end_time)[1]=='11') selected="selected"
                                                    @endif value="10">Nov
                                            </option>
                                            <option @if(explode('-', $group->end_time)[1]=='12') selected="selected"
                                                    @endif value="11">Dec
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control day" name="day">
                                            <option value="*">Day</option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='1') selected="selected"
                                                    @endif value="1">1
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='2') selected="selected"
                                                    @endif value="2">2
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='3') selected="selected"
                                                    @endif value="3">3
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='4') selected="selected"
                                                    @endif value="4">4
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='5') selected="selected"
                                                    @endif value="5">5
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='6') selected="selected"
                                                    @endif value="6">6
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='7') selected="selected"
                                                    @endif value="7">7
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='8') selected="selected"
                                                    @endif value="8">8
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='9') selected="selected"
                                                    @endif value="9">9
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='10') selected="selected"
                                                    @endif value="10">10
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='11') selected="selected"
                                                    @endif value="11">11
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='12') selected="selected"
                                                    @endif value="12">12
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='13') selected="selected"
                                                    @endif value="13">13
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='14') selected="selected"
                                                    @endif value="14">14
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='15') selected="selected"
                                                    @endif value="15">15
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='16') selected="selected"
                                                    @endif value="16">16
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='17') selected="selected"
                                                    @endif value="17">17
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='18') selected="selected"
                                                    @endif value="18">18
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='19') selected="selected"
                                                    @endif value="19">19
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='20') selected="selected"
                                                    @endif value="20">20
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='21') selected="selected"
                                                    @endif value="21">21
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='22') selected="selected"
                                                    @endif value="22">22
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='23') selected="selected"
                                                    @endif value="23">23
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='24') selected="selected"
                                                    @endif value="24">24
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='25') selected="selected"
                                                    @endif value="25">25
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='26') selected="selected"
                                                    @endif value="26">26
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='27') selected="selected"
                                                    @endif value="27">27
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='28') selected="selected"
                                                    @endif value="28">28
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='29') selected="selected"
                                                    @endif value="29">29
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='30') selected="selected"
                                                    @endif value="30">30
                                            </option>
                                            <option @if(explode(' ', explode('-', $group->end_time)[2])[0]=='31') selected="selected"
                                                    @endif value="31">31
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control year" name="year">
                                            <option value="*">Year</option>
                                            <option @if(explode('-', $group->end_time)[0]=='2017') selected="selected"
                                                    @endif value="2017">2017
                                            </option>
                                            <option @if(explode('-', $group->end_time)[0]=='2018') selected="selected"
                                                    @endif value="2018">2018
                                            </option>
                                            <option @if(explode('-', $group->end_time)[0]=='2019') selected="selected"
                                                    @endif value="2019">2019
                                            </option>
                                            <option @if(explode('-', $group->end_time)[0]=='2020') selected="selected"
                                                    @endif value="2020">2020
                                            </option>
                                        </select>
                                    </td>
                                    <td class="pr-0">
                                        <select class="form-control hour" name="hour">
                                            <option value="*">Time</option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '00') selected="selected"
                                                    @endif value="00">12 AM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '01') selected="selected"
                                                    @endif value="01">01 AM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '02') selected="selected"
                                                    @endif value="02">02 AM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '03') selected="selected"
                                                    @endif value="03">03 AM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '04') selected="selected"
                                                    @endif value="04">04 AM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '05') selected="selected"
                                                    @endif value="05">05 AM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '06') selected="selected"
                                                    @endif value="06">06 AM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '07') selected="selected"
                                                    @endif value="07">07 AM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '08') selected="selected"
                                                    @endif value="08">08 AM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '09') selected="selected"
                                                    @endif value="09">09 AM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '10') selected="selected"
                                                    @endif value="10">10 AM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '11') selected="selected"
                                                    @endif value="11">11 AM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '12') selected="selected"
                                                    @endif value="12">12 PM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '13') selected="selected"
                                                    @endif value="13">01 PM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '14') selected="selected"
                                                    @endif value="14">02 PM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '15') selected="selected"
                                                    @endif value="15">03 PM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '16') selected="selected"
                                                    @endif value="16">04 PM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '17') selected="selected"
                                                    @endif value="17">05 PM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '18') selected="selected"
                                                    @endif value="18">06 PM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '19') selected="selected"
                                                    @endif value="19">07 PM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '20') selected="selected"
                                                    @endif value="20">08 PM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '21') selected="selected"
                                                    @endif value="21">09 PM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '22') selected="selected"
                                                    @endif value="22">10 PM
                                            </option>
                                            <option @if(explode(':', explode(' ', $group->end_time)[1])[0] == '23') selected="selected"
                                                    @endif value="23">11 PM
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        @else
                            <table class="table b-00 end">
                                <tr>
                                    <td class="pl-0">
                                        <select class="form-control month" name="month">
                                            <option value="*">Month</option>
                                            <option value="00">Jan</option>
                                            <option value="01">Feb</option>
                                            <option value="02">Mar</option>
                                            <option value="03">Apr</option>
                                            <option value="04">May</option>
                                            <option value="05">Jun</option>
                                            <option value="06">Jul</option>
                                            <option value="07">Aug</option>
                                            <option value="08">Sep</option>
                                            <option value="09">Oct</option>
                                            <option value="10">Nov</option>
                                            <option value="11">Dec</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control day" name="day">
                                            <option value="*">Day</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                            <option value="7">7</option>
                                            <option value="8">8</option>
                                            <option value="9">9</option>
                                            <option value="10">10</option>
                                            <option value="11">11</option>
                                            <option value="12">12</option>
                                            <option value="13">13</option>
                                            <option value="14">14</option>
                                            <option value="15">15</option>
                                            <option value="16">16</option>
                                            <option value="17">17</option>
                                            <option value="18">18</option>
                                            <option value="19">19</option>
                                            <option value="20">20</option>
                                            <option value="21">21</option>
                                            <option value="22">22</option>
                                            <option value="23">23</option>
                                            <option value="24">24</option>
                                            <option value="25">25</option>
                                            <option value="26">26</option>
                                            <option value="27">27</option>
                                            <option value="28">28</option>
                                            <option value="29">29</option>
                                            <option value="30">30</option>
                                            <option value="31">31</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control year" name="year">
                                            <option value="*">Year</option>
                                            <option value="2017">2017</option>
                                            <option value="2018">2018</option>
                                            <option value="2019">2019</option>
                                            <option value="2020">2020</option>
                                        </select>
                                    </td>
                                    <td class="pr-0">
                                        <select class="form-control hour" name="hour">
                                            <option value="*">Time</option>
                                            <option value="00">12 AM</option>
                                            <option value="01">01 AM</option>
                                            <option value="02">02 AM</option>
                                            <option value="03">03 AM</option>
                                            <option value="04">04 AM</option>
                                            <option value="05">05 AM</option>
                                            <option value="06">06 AM</option>
                                            <option value="07">07 AM</option>
                                            <option value="08">08 AM</option>
                                            <option value="09">09 AM</option>
                                            <option value="10">10 AM</option>
                                            <option value="11">11 AM</option>
                                            <option value="12">12 PM</option>
                                            <option value="13">01 PM</option>
                                            <option value="14">02 PM</option>
                                            <option value="15">03 PM</option>
                                            <option value="16">04 PM</option>
                                            <option value="17">05 PM</option>
                                            <option value="18">06 PM</option>
                                            <option value="19">07 PM</option>
                                            <option value="20">08 PM</option>
                                            <option value="21">09 PM</option>
                                            <option value="22">10 PM</option>
                                            <option value="23">11 PM</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        @endif
                        <input type="hidden" name="end_time">
                    </div>
                    <div class="form-group" style="margin-bottom: 0px;">
                        <input class="check-toog left-toog" type="checkbox" name="top_buffer_queue"
                               @if($group->top_buffer_queue == 1) checked @endif
                               value="1"
                               id="top_buffer_queue">
                        <label for="top_buffer_queue" style="font-size: 12px">Sent to Top Buffer Queue</label>
                    </div>
                    <div class="form-group" style="margin-bottom: 0px;">
                        <input class="check-toog left-toog" type="checkbox" value="1" name="enable_slot"
                               @if($group->enable_slot == 1) checked @endif id="enable_slot">
                        <label for="enable_slot" style="font-size: 12px">Only Send if Queue has at least 'X' Empty Slots</label>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="range" name="slot_amount" min="0" max="100" value="{{$group->slot_amount}}">
                        <div class="text-center">
                            <span class="slot_amount">{{$group->slot_amount}}</span></span>
                        </div>
                    </div>
                    <div class="form-group text-center" style="margin-bottom: 0px;">
                        <button type="submit" class="btn btn-default width-btn btn-dc">Save</button>
                    </div>
                </div>
        </form>
    </li>
</ul>
