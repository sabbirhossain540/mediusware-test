@extends('layouts.app')
@section('content')
    <div class="container-fluid app-body settings-page">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        <h3>Settings
            <a href="https://bufferapp.com/oauth2/authorize?client_id={{env('BUFFER_CLIENT_ID')}}&redirect_uri={{env('BUFFER_REDIRECT')}}&response_type=code"
               class="btn btn-primary pull-right">Reconnect to Buffer</a>
        </h3>
        <div class="row">
            <div class="col-md-12">
                <h4>Total posts : {!! $totalposts or 0 !!}<h4>
                <h4>Remaining posts : {!! $remaininposts or 0 !!}</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Account</div>
                    <div class="panel-body">
                        <div class="row">
                            <form method="POST" id="update-user">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" value="{{$user->id}}">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <i class="input-icon fa fa-user"></i>
                                        <input type="text" name="first_name" class="form-control"
                                               value="{{$user->first_name}}" placeholder="First Name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="text" name="last_name" class="form-control nlp"
                                               value="{{$user->last_name}}" placeholder="Last Name">
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <i class="input-icon fa fa-envelope-o"></i>
                                        <input type="email" name="email" class="form-control" value="{{$user->email}}"
                                               placeholder="Email" autocomplete="false">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <i class="input-icon fa fa-lock"></i>

                                        <input type="password" class="form-control" name="password"
                                               placeholder="Password" autocomplete="false">
                                    </div>
                                </div>
                                <div class="col-sm-12 text-center">
                                    <button type="submit" class="btn btn-default">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">Time zone</div>
                    <div class="panel-body">
                        <?php
                        $timezones = array();
                        foreach (timezone_abbreviations_list() as $abbr => $timezone) {
                            foreach ($timezone as $val) {
                                if (isset($val['timezone_id'])) {
                                    array_push($timezones, $val['timezone_id']);
                                }
                            }
                        }
                        $timezones = array_unique($timezones);
                        sort($timezones);
                        ?>
                        <form action=" {{ route('saveTimezone') }} " method="POST" id="timezone-form">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="user_id" value="{{$user->id}}">
                            <div class="form-group">
                                <select class="form-control  nlp" name="timezone">
                                    <?php foreach ($timezones as $key => $timezone): ?>
                                    <option <?php if ($user->timezone == $timezone) {
                                        echo "selected";
                                    } ?> value="<?php echo $timezone;  ?>"> <?php echo $timezone;  ?> </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="col-sm-12 text-center">
                                <button type="button" class="btn btn-default submit">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Url Shortening</div>
                    <div class="panel-body text-center">
                        @if($user->rebrandly_key)
                            <?php
                            $domains = null;
                            /*try {
                                $client = new \GuzzleHttp\Client;
                                $result = $client->request('GET', 'https://api.rebrandly.com/v1/domains', [
                                    'headers' => [
                                        'Authorization' => 'Bearer ' . $user->rebrandly_key
                                    ]
                                ]);

                                $domains = json_decode($result->getBody());

                            } catch (\GuzzleHttp\Exception\ClientException $e) {

                            }*/



                            ?>
                            <form id="UpdateRbrandDomain" method="post" action="">

                                {{ csrf_field() }}

                                <select class="form-control" name="rebrandly_domain">
                                    <option>Select Branded Url</option>
                                    @if($domains)
                                        @foreach ($domains as $domain)
                                            <option <?php if ($user->rebrandly_domain == $domain->id) {
                                                echo 'selected="selected"';
                                            } ?> value="{{$domain->id}}">{{$domain->fullName}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <table width="100%">
                                    <tr>
                                        <td align="center">
                                            <button class="btn btn-defaul" type="submit"> Save</button>
                                        </td>
                                        <td align="center"><a class="btn btn-default submit Disconnected_Rebrandly"
                                                              href="#">Disconnect Rebrandly</a></td>
                                    </tr>
                                </table>
                            </form>
                        @else
                            <a class="btn btn-default submit"
                               href="https://oauth.rebrandly.com/connect/authorize?client_id=7b787dbb-22ce-4568-a42d-64ea391c1e9b&redirect_uri=https%3A%2F%2Fapp.bulk.ly%2Fsettings&response_type=token&scope=rbapi">Connect
                                to Rebrandly</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                @if($cards)
                    <div class="panel panel-default">
                        <div class="panel-heading">Billing & Subscription</div>

                        <div class="panel-body">
                            <ul class="list-unstyled m-0">

                                @foreach ($cards as $key => $card)
                                    <li>{{ $key + 1}}. {{ $card->brand}} :- {{ $card->last4}}</li>
                                @endforeach

                            </ul>
                            <hr>
                            <form action="/subscriptions" method="POST" id="payment-form-pp" class="payment-form">
                                <div class="payment-errors alert alert-danger"></div>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="row plans">
                                    <?php
                                    $bfplan = array('promonth-bf','proplusmonth-bf','agencymonth-bf','proyear-bf', 'proplusyear-bf', 'agencyyear-bf');
                                    ?>
                                    <div class="col-sm-12 text-center">
                                        <div class="prices-button">
                                            <div class="btn-group-container">
                                                <div class="btn-group" data-toggle="buttons">
                                                    <label class="btn btn-default @if(isset($user->subscriptions[0]) && ($user->subscriptions[0]->stripe_plan == 'freemonth' || $user->subscriptions[0]->stripe_plan == 'promonth' || $user->subscriptions[0]->stripe_plan == 'proplusyear' || $user->subscriptions[0]->stripe_plan == 'agencymonth')) active @endif">
                                                        <input type="radio" name="period" value="monthly" checked> Monthly
                                                    </label>
                                                    <label class="btn btn-default @if(isset($user->subscriptions[0]) && ($user->subscriptions[0]->stripe_plan == 'freeyear' || $user->subscriptions[0]->stripe_plan == 'proyear' || $user->subscriptions[0]->stripe_plan == 'proplusmonth' || $user->subscriptions[0]->stripe_plan == 'agencyyear')) active @endif">
                                                        <input type="radio" name="period" value="yearly"> Yearly
                                                    </label>
                                                </div>
                                                <div class="coppare_links text-center">
                                                    <a target="_blank" href="#">&nbsp;</a>
                                                </div>
                                            </div>
                                            <br><br><br>
                                            <div class="btn-group-container">
                                                <div class="btn-group levels monthly active" data-toggle="buttons">
                                                    @foreach($plans_m as $key => $plan)
                                                        @if(in_array($plan->slug, $bfplan))
                                                        @else
                                                            <label class="btn btn-default @if($key==0) active @endif">
                                                                <span>{{$plan->name}}</span>
                                                                <input data-value="{{$plan->slug}}" type="radio" name="id" value="{{$plan->slug}}|{{$plan->name}}" @if($key==0) checked @endif> ${{$plan->price}}
                                                            </label>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                <div class="btn-group levels yearly " data-toggle="buttons">
                                                    @foreach($plans_y as $key => $plan)
                                                        @if(in_array($plan->slug, $bfplan))
                                                        @else
                                                            @if($plan->price == '0')
                                                                <?php
                                                                if(isset($_GET['plan'])){
                                                                if($_GET['plan']=='free'){
                                                                ?>
                                                                <label class="btn btn-default">
                                                                    <span>{{$plan->name}}</span>
                                                                    <input data-value="{{$plan->slug}}" type="radio" name="id" value="{{$plan->slug}}|{{$plan->name}}"> ${{$plan->price}}
                                                                </label>
                                                                <?php
                                                                }
                                                                }
                                                                ?>
                                                            @else
                                                                <label class="btn btn-default">
                                                                    <span>{{$plan->name}}</span>
                                                                    <input data-value="{{$plan->slug}}" type="radio" name="id" value="{{$plan->slug}}|{{$plan->name}}"> ${{$plan->price}}
                                                                </label>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                                <div class="coppare_links text-center">
                                                    <a target="_blank" href="https://bulk.ly/pricing/ ">Compare plans</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <br>
                                        <table class="table">
                                            <tr class="form-row" >
                                                <td width="50%">
                                                    <i class="input-icon fa fa-credit-card"></i>
                                                    <input class="form-control" type="text" size="20" data-stripe="number"  value="**********{{$cards[0]['last4']}}" placeholder="Credit Card Number">
                                                </td>
                                                <td>
                                                    <input class="form-control pl15" type="text" size="4" data-stripe="cvc"  placeholder="CVC">
                                                </td>
                                            </tr>
                                            <tr class="form-row">
                                                <td class="form-inline" colspan="2">
                                                    <i class="input-icon fa fa-calendar"></i>
                                                    <input class="form-control" type="text" size="2" data-stripe="exp_month" value="{{$cards[0]['exp_month']}}" placeholder="MM "> <span class="divi"> / </span>
                                                    <input class="form-control pl15" type="text" size="4" value="{{$cards[0]['exp_year']}}" data-stripe="exp_year"  placeholder="YY">
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <button type="button" id="submit-payment" class="btn btn-default submit">Update Subscription <i class="fa-btn fa fa-angle-right"></i></button>
                                <div class="trial">All plans come with a FREE 7-day trial</div>
                            </form>
                            <br>
                            @if(isset($user->subscriptions) && count($user->subscriptions))
                                @if(isset($user->subscriptions) && $user->subscriptions[0]->ends_at)
                                    <div class="col-sm-12 text-center">
                                        <button type="button" class="btn btn-link btn-danger">Closed</button>
                                    </div>
                                @else
                                    <div class="col-sm-12 text-center">
                                        <button type="button" class="btn btn-default close-account">Close Account</button>
                                    </div>
                                @endif
                            @endif
                            @if($user->plansubs() != null)
                                @if($user->plansubs()['subscription']->stripe_plan)
                                    <form id="close-account-from" action="/close-account" method="post">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="user_id" value="{{$user->id}}">
                                        <input type="hidden" name="user_plan" value="{{$user->subscriptions[0]->name}}">
                                    </form>
                                @endif
                            @else
                                <button type="button" class="btn btn-danger btn-block close-account">Delete Account</button>
                                <form id="close-account-from" action="/close-account" method="post">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="user_id" value="{{$user->id}}">
                                    <input type="hidden" name="type" value="delete">
                                </form>
                            @endif
                            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
                            <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
                            <script type="text/javascript">
                                Stripe.setPublishableKey('<?php echo env('STRIPE_KEY'); ?>');
                                var $form = $('#payment-form-pp');
                                $form.find('.submit').click(function (event) {
                                    console.log('amieami');
                                    $form.find('.submit').prop('disabled', true);
                                    Stripe.card.createToken($form, stripeResponseHandler);
                                    return false;
                                });
                                function stripeResponseHandler(status, response) {
                                    console.log(response);
                                    var $form = $('#payment-form-pp');
                                    if (response.error) {
                                        console.log(response.error.message);
                                        $form.find('.payment-errors').text(response.error.message).show();
                                        $form.find('.submit').prop('disabled', false);
                                        /*setTimeout(function () {
                                            $form.get(0).submit();
                                        }, 1000)*/
                                    } else {
                                        var token = response.id;
                                        $form.append($('<input type="hidden" name="stripeToken">').val(token));
                                        $form.append($('<input type="hidden" name="card_brand">').val(response.card.brand));
                                        $form.append($('<input type="hidden" name="card_last_four">').val(response.card.last4));

                                        $form.find('.payment-errors').text('Your payment was successful. We\'re redirecting you back to your Bulkly account...').show().removeClass('alert-danger').addClass('alert-success');

                                        setTimeout(function () {
                                            $form.get(0).submit();
                                        }, 1000)

                                    }
                                };
                            </script>
                        </div>
                    </div>
                @else
                    <div class="panel panel-default">
                        <div class="panel-heading">Billing & Subscription</div>
                        <div class="panel-body">

                            <form action="/subscriptions" method="POST" id="payment-form-pp" class="payment-form">
                                <div class="payment-errors alert alert-danger"></div>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="row plans">
                                    <?php
                                    $bfplan = array('promonth-bf','proplusmonth-bf','agencymonth-bf','proyear-bf', 'proplusyear-bf', 'agencyyear-bf');
                                    ?>
                                    <div class="col-sm-12 text-center">
                                        <div class="prices-button">
                                            <div class="btn-group-container">
                                                <div class="btn-group" data-toggle="buttons">
                                                    <label class="btn btn-default active">
                                                        <input type="radio" name="period" value="monthly" checked> Monthly
                                                    </label>
                                                    <label class="btn btn-default ">
                                                        <input type="radio" name="period" value="yearly"> Yearly
                                                    </label>
                                                </div>
                                                <div class="coppare_links text-center">
                                                    <a target="_blank" href="#">&nbsp;</a>
                                                </div>
                                            </div>
                                            <br><br><br>
                                            <div class="btn-group-container">
                                                <div class="btn-group levels monthly active" data-toggle="buttons">
                                                    @foreach($plans_m as $key => $plan)
                                                        @if(in_array($plan->slug, $bfplan))
                                                        @else
                                                            <label class="btn btn-default @if($key==0) active @endif">
                                                                <span>{{$plan->name}}</span>
                                                                <input data-value="{{$plan->slug}}" type="radio" name="id" value="{{$plan->slug}}|{{$plan->name}}" @if($key==0) checked @endif> ${{$plan->price}}
                                                            </label>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                <div class="btn-group levels yearly " data-toggle="buttons">
                                                    @foreach($plans_y as $key => $plan)
                                                        @if(in_array($plan->slug, $bfplan))
                                                        @else
                                                            @if($plan->price == '0')
                                                                <?php
                                                                if(isset($_GET['plan'])){
                                                                if($_GET['plan']=='free'){
                                                                ?>
                                                                <label class="btn btn-default">
                                                                    <span>{{$plan->name}}</span>
                                                                    <input data-value="{{$plan->slug}}" type="radio" name="id" value="{{$plan->slug}}|{{$plan->name}}"> ${{$plan->price}}
                                                                </label>
                                                                <?php
                                                                }
                                                                }
                                                                ?>
                                                            @else
                                                                <label class="btn btn-default">
                                                                    <span>{{$plan->name}}</span>
                                                                    <input data-value="{{$plan->slug}}" type="radio" name="id" value="{{$plan->slug}}|{{$plan->name}}"> ${{$plan->price}}
                                                                </label>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                                <div class="coppare_links text-center">
                                                    <a target="_blank" href="https://bulk.ly/pricing/ ">Compare plans</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <br>
                                        <table class="table">
                                            <tr class="form-row" >
                                                <td width="50%">
                                                    <i class="input-icon fa fa-credit-card"></i>
                                                    <input class="form-control" type="text" size="20" data-stripe="number"  placeholder="Credit Card Number">
                                                </td>
                                                <td>
                                                    <input class="form-control pl15" type="text" size="4" data-stripe="cvc"  placeholder="CVC">
                                                </td>
                                            </tr>
                                            <tr class="form-row">
                                                <td class="form-inline" colspan="2">
                                                    <i class="input-icon fa fa-calendar"></i>
                                                    <input class="form-control" type="text" size="2" data-stripe="exp_month"  placeholder="MM "> <span class="divi"> / </span>
                                                    <input class="form-control pl15" type="text" size="4" data-stripe="exp_year"  placeholder="YY">
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <button type="button" id="submit-payment" class="btn btn-default submit">Start Subscription <i class="fa-btn fa fa-angle-right"></i></button>
                                <div class="trial">All plans come with a FREE 7-day trial</div>
                            </form>
                            <br>
                            <div class="col-sm-12 text-center">
                                <button type="button" class="btn btn-danger close-account" data-type="delete">Delete Account</button>
                            </div>
                            <form id="close-account-from" action="/close-account" method="post">
                                {{ csrf_field() }}
                                <input type="hidden" name="user_id" value="{{$user->id}}">
                                <input type="hidden" name="type" value="delete">
                            </form>
                            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
                            <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
                            <script type="text/javascript">
                                Stripe.setPublishableKey('<?php echo env('STRIPE_KEY'); ?>');
                                var $form = $('#payment-form-pp');
                                $form.find('.submit').click(function (event) {
                                    console.log('amieami');
                                    $form.find('.submit').prop('disabled', true);
                                    Stripe.card.createToken($form, stripeResponseHandler);
                                    return false;
                                });
                                function stripeResponseHandler(status, response) {
                                    console.log(response);
                                    var $form = $('#payment-form-pp');
                                    if (response.error) {
                                        console.log(response.error.message);
                                        $form.find('.payment-errors').text(response.error.message).show();
                                        $form.find('.submit').prop('disabled', false);
                                        setTimeout(function () {
                                            $form.get(0).submit();
                                        }, 1000)
                                    } else {
                                        var token = response.id;
                                        $form.append($('<input type="hidden" name="stripeToken">').val(token));
                                        $form.append($('<input type="hidden" name="card_brand">').val(response.card.brand));
                                        $form.append($('<input type="hidden" name="card_last_four">').val(response.card.last4));

                                        $form.find('.payment-errors').text('Your payment was successful. We\'re redirecting you back to your Bulkly account...').show().removeClass('alert-danger').addClass('alert-success');

                                        setTimeout(function () {
                                            $form.get(0).submit();
                                        }, 5000)

                                    }
                                };
                            </script>
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-md-4">

                @if($invoices)
                    <div class="panel panel-default">
                        <div class="panel-heading">Invoice</div>
                        <div class="panel-body">
                            <table class="table b-00">
                                @foreach ($invoices as $invoice)
                                    <tr>
                                        <?php
                                        $months = array('January', 'February', 'March', 'April', 'May', 'Jun', 'July', 'August', 'September', 'October', 'November', 'December');
                                        ?>
                                        <td>
                                            <div class="media">
                                                <div class="media-left">
                                                    <span class="montone left">{{ substr($months[$invoice->date()->month - 1], 0, 1) }}</span>
                                                </div>
                                                <div class="media-body">
                                                    <span class="montone"> <h4>{{ $invoice->date()->toFormattedDateString() }}</h4></span>
                                                    <p>
                                                        Paid {{ str_replace('before', 'ago', $invoice->date()->diffForHumans(\Carbon\Carbon::now()))}}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $invoice->total() }}</td>
                                        <td><a href="/user/invoice/{{ $invoice->id }}">Download</a></td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
