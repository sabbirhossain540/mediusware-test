<!DOCTYPE html>
<html lang="<?php echo e(config('app.locale')); ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'Laravel')); ?></title>
    <link rel="stylesheet" type="text/css" href="//fast.appcues.com/widget.css"/>
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet"/>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">


    <link rel=”stylesheet” href=” https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
    <script src=”https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>


    
    <script type="text/javascript">
        window.Laravel = <?php echo json_encode([
			'csrfToken' => csrf_token(),
			'APP_URL' => env('APP_URL'),
		]); ?>;
    </script>
    <script type="text/javascript">
        (function (w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-TLNT7S');
    </script>
    <script type="text/javascript">
        (function (o) {
            var b = "https://api.autopilothq.com/anywhere/",
                t = "23556ef2086d413ca59b399cb5747679decfd69f94864c3b8fbe825622ff831f",
                a = window.AutopilotAnywhere = {
                    _runQueue: [],
                    run: function () {
                        this._runQueue.push(arguments);
                    }
                },
                c = encodeURIComponent,
                s = "SCRIPT",
                d = document,
                l = d.getElementsByTagName(s)[0],
                p = "t=" + c(d.title || "") + "&u=" + c(d.location.href || "") + "&r=" + c(d.referrer || ""),
                j = "text/javascript",
                z, y;
            if (!window.Autopilot) window.Autopilot = a;
            if (o.app) p = "devmode=true&" + p;
            z = function (src, asy) {
                var e = d.createElement(s);
                e.src = src;
                e.type = j;
                e.async = asy;
                l.parentNode.insertBefore(e, l);
            };
            if (!o.noaa) {
                z(b + "aa/" + t + '?' + p, false)
            }
            y = function () {
                z(b + t + '?' + p, true);
            }
            if (window.attachEvent) {
                window.attachEvent("onload", y);
            } else {
                window.addEventListener("load", y, false);
            }
        })({
            "app": true
        });
    </script>

    <script>
        (function (u, s, e, r, g) {
            u[r] = u[r] || [];
            u[r].push({
                'ug.start': new Date().getTime(), event: 'embed.js',
            });
            var f = s.getElementsByTagName(e)[0],
                j = s.createElement(e);
            j.async = true;
            j.src = 'https://static.userguiding.com/media/user-guiding-' + g + '-embedded.js';
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'userGuidingLayer', '3592590ID');
    </script>
</head>
<body>
<?php
if(\Auth::check()){

$user = \Bulkly\User::find(\Auth::id());
$user_meta = unserialize($user->user_meta);

if(isset($user_meta['temp_subs'])) {
if($user_meta['temp_subs'] === true){
?>
<script type="text/javascript">
    var tempSubs = {'tempSubs': true};
</script>
<?php echo $__env->make('subscriptions.subscriptions-min', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php
} else {
?>
<script type="text/javascript">
    var tempSubs = {'tempSubs': false};
</script>
<?php
}

}
elseif($user->bfriday == 1){
?>
<?php echo $__env->make('subscriptions.subscriptions-friday', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php
}
else {
?>
<script type="text/javascript">
    var tempSubs = {'tempSubs': false};
</script>
<?php
}


if(isset($user_meta['temp_user'])) {
if($user_meta['temp_user'] === true){
?>
<script type="text/javascript">
    var TempUser = {'tempUser': true};
</script>
<div class="auth-container"
     style="position: fixed;z-index: 555000;background: #f5f5f5;width: 100%;margin: 0 auto;top: 0;bottom: 0;">
    <div class="panel">

        <form id="update-temp-user" class="form-horizontal" role="form" method="POST"
              action="https://app.bulk.ly/update-temp-user"
              style="position: absolute;width: 100%;max-width: 600px;background: #ffffff;padding: 30px;transform: translate(-50%, -50%);top: 50%;left: 50%;">
            <?php echo e(csrf_field()); ?>

            <input type="hidden" name="user_id" value="<?php echo e(\Auth::id()); ?>">
            <h3 class="text-center">Create Account</h3>
            <p class="text-center">By creating an account, you'll be able to login to Bulkly to access your imported
                Buffer data.</p>
            <br>
            <div class="temp_user_feedback text-center"></div>
            <br>
            <div class="form-group">
                <label for="email" class="col-md-4 control-label hide">E-Mail Address</label>
                <div class="col-md-6 col-md-offset-3">
                    <i class="input-icon fa fa-envelope-o"></i>
                    <input id="email" type="email" class="form-control" name="email" value="" placeholder="Email"
                           required autofocus>
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-md-4 control-label hide">Password</label>
                <div class="col-md-6 col-md-offset-3">
                    <i class="input-icon fa fa-lock"></i>
                    <input id="password" type="password" class="form-control" name="password" placeholder="Password"
                           required>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-6 col-md-offset-3">
                    <button type="submit" class="btn btn-default width-xl-btn btn-dc btn-block">
                        Create Account
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php
} else {
?>
<script type="text/javascript">
    var TempUser = {'tempUser': false};
</script>
<?php
}
} else {
?>
<script type="text/javascript">
    var TempUser = {'tempUser': false};
</script>
<?php
}

}
?>


<script type="text/javascript">
    Autopilot.run("associate", {
        Email: "<?php echo \Auth::user()->email; ?>",
        FirstName: "<?php echo \Auth::user()->first_name; ?>",
        LastName: "<?php echo \Auth::user()->last_name; ?>",
        custom: {
            "string--Logged--In": "true"
        }
    });
</script>
<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TLNT7S" height="0" width="0"
            style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->
<div id="app">
    <div class="ajax-working">
        <div class="sk-fading-circle">
            <div class="sk-circle1 sk-circle"></div>
            <div class="sk-circle2 sk-circle"></div>
            <div class="sk-circle3 sk-circle"></div>
            <div class="sk-circle4 sk-circle"></div>
            <div class="sk-circle5 sk-circle"></div>
            <div class="sk-circle6 sk-circle"></div>
            <div class="sk-circle7 sk-circle"></div>
            <div class="sk-circle8 sk-circle"></div>
            <div class="sk-circle9 sk-circle"></div>
            <div class="sk-circle10 sk-circle"></div>
            <div class="sk-circle11 sk-circle"></div>
            <div class="sk-circle12 sk-circle"></div>
           
        </div>
    </div>
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">
                    <i class="fa fa-caret-left"></i> <i class="fa fa-bars"></i>
                </a>
            </div>
            <div class="navbar-collapse">
                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    <?php if(Auth::guest()): ?>
                        <li><a href="<?php echo e(route('login')); ?>">Login</a></li>
                        <li><a href="<?php echo e(route('register')); ?>">Register</a></li>
                    <?php else: ?>
                        <li>
                            <a href="<?php echo e(route('support')); ?>"><span class="fa fa-envelope"></span></a>
                        </li>
                        <li>
                            <a id="my-widget" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-expanded="false"></a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-expanded="false">
                                <?php if(count(\Bulkly\User::find(Auth::id())->socialaccounts) > 0): ?>
                                    <img class="navavatar"
                                         src="<?php echo e(Bulkly\User::find(Auth::id())->socialaccounts[0]->avatar); ?>">
                                <?php else: ?>
                                    <img class="navavatar" src="/images/noavater.png">
                                <?php endif; ?>
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu navd" role="menu">
                                <li>
                                    <a href="<?php echo e(route('auth.logout')); ?>">
                                        Logout
                                    </a>
                                    
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <style type="text/css">
        .dropdown-menu.navd:before {
            content: none !important;
        }
    </style>
    <nav class="navbar left-navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="<?php echo e(url('/')); ?>">
                    <img src="/images/logo.png">
                </a>
            </div>
            <?php $route = \Request::route()->getName(); ?>
            <ul class="nav navbar-nav">
                <?php if( \Auth::user()->email == env('SU')): ?>
                    <li><a href="<?php echo e(url('/admin')); ?>"> <i class="fa fa-user"></i>Admin</a></li>
                <?php endif; ?>
                <li <?php if($route=='home'): ?> class="active" <?php endif; ?>><a href="<?php echo e(url('/')); ?>"> <i class="fa fa-home"></i> Home</a>
                </li>
                <?php if(session()->has('buffer_token')): ?>
                    <li <?php if($route=='content-upload' || $route=='content-pending' || $route=='content-active' || $route=='content-completed'): ?> class="active" <?php endif; ?>>
                        <a href="<?php echo e(url('/content-upload')); ?>" class="toggle"> <i class="fa fa-upload"></i> Content Upload</a>
                    </li>
                    <li <?php if($route=='content-curation' || $route=='content-curation-pending' || $route=='content-curation-active' || $route=='content-curation-completed'): ?> class="active" <?php endif; ?>>
                        <a href="<?php echo e(url('/content-curation')); ?>" class="toggle"> <i class="fa fa-file-text-o"></i> Content
                            Curation</a></li>
                    <li <?php if($route=='rss-automation' || $route=='rss-automation-pending' || $route=='rss-automation-active' || $route=='rss-automation-completed'): ?> class="active" <?php endif; ?>>
                        <a href="<?php echo e(url('/rss-automation')); ?>" class="toggle"> <i class="fa fa-rss"></i> RSS
                            Automation</a></li>
                    <li <?php if($route=='analytics'): ?> class="active" <?php endif; ?>><a href="<?php echo e(url('/analytics')); ?>"> <i
                                    class="fa fa-line-chart"></i> Analytics</a></li>
                    <li <?php if($route=='calendar'): ?> class="active" <?php endif; ?>><a href="<?php echo e(url('/calendar')); ?>"> <i
                                    class="fa fa-calendar"></i> Calendar</a></li>
                    <li <?php if($route=='social-accounts'): ?> class="active" <?php endif; ?>><a href="<?php echo e(url('/social-accounts')); ?>">
                            <i class="fa fa-user"></i>Social Accounts</a></li>
                    <li <?php if($route=='settings'): ?> class="active" <?php endif; ?>><a href="<?php echo e(url('/settings')); ?>"> <i
                                    class="fa fa-gear"></i> Settings</a></li>

                    <li <?php if($route=='history'): ?> class="active" <?php endif; ?>><a href="<?php echo e(url('/history')); ?>"> <i
                                    class="fa fa-home"></i> History</a></li>
                <?php endif; ?>
            </ul>
        </div><!-- /.container-fluid -->
    </nav>
    <?php echo $__env->yieldContent('content'); ?>
</div>

<script src="//fast.appcues.com/widget-bundle.js" type="text/javascript"></script>

<script src="<?php echo e(asset('js/app.js')); ?>"></script>
<script>

    var hash = window.location.hash;
    if (hash) {
        var result = hash.split('=');
        var result = result[1].split('&');
        if (result[0]) {
            console.log(result[0]);
            var data = new FormData();
            data.append('_token', $('meta[name="csrf-token"]').attr('content'));
            data.append('rebrandly_key', result[0]);

            $.ajax({
                type: "POST",
                url: '/rebrandly_key',
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                success: function success(msg) {
                    window.location.href = 'https://app.bulk.ly/settings';
                },
                error: function error(xhr, ajaxOptions, thrownError) {
                    if (xhr.status) {

                    }
                }
            });

        }
    }


    $('.Disconnected_Rebrandly').click(function () {

        console.log('ok');
        var data = new FormData();
        data.append('_token', $('meta[name="csrf-token"]').attr('content'));
        data.append('rebrandly_key', '');

        $.ajax({
            type: "POST",
            url: '/rebrandly_key',
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            success: function success(msg) {
                window.location.reload();
            },
            error: function error(xhr, ajaxOptions, thrownError) {
                if (xhr.status) {

                }
            }
        });

        return false;

    })

    function ajaxStart() {
        $('.ajax-working').fadeIn();
    }

    function ajaxEnd() {
        $('.ajax-working').fadeOut();
    }

    function objectifyForm(formArray) {
        var returnArray = {};
        for (var i = 0; i < formArray.length; i++) {
            returnArray[formArray[i]['name']] = formArray[i]['value'];
        }
        return returnArray;
    }

    var rebrandUp = $('#UpdateRbrandDomain');
    rebrandUp.submit(function () {
        ajaxStart();
        var data = objectifyForm(rebrandUp.serializeArray());
        console.log(data);
        $.ajax({
            type: "POST",
            url: '/rebrandly-domain',
            data: data,
            success: function success(msg) {
                ajaxEnd();
            },
            error: function error(xhr, ajaxOptions, thrownError) {
                if (xhr.status) {
                    ajaxEnd();
                }
            }
        });
        return false;
    });


    $('#ExportContent').click(function () {
        $(this).parents('form').submit();
    });


    $('#file-reupload').change(function () {
        ajaxStart();
        var file = $(this);
        var csv = file[0].files;
        var ext = file.val().split('.').pop().toLowerCase();
        if ($.inArray(ext, ['csv']) == -1) {
            alert('Whoops! Please try saving your file as a CSV and upload again');
            return false;
        }

        var data = new FormData();
        data.append('_token', $('meta[name="csrf-token"]').attr('content'));
        data.append('file', csv[0]);

        $.ajax({
            url: '/csv-to-reupload',
            type: 'POST',
            data: data,
            async: true,
            cache: false,
            contentType: false,
            processData: false,
            success: function success(data) {
                console.log(data);
                window.location.reload();
                ajaxEnd();

            },
            error: function error(xhr, ajaxOptions, thrownError) {
                if (xhr.status) {
                    ajaxEnd();
                }
            }
        });

    });

    $(function () {

        $(window).on('load', function () {
            var modal = $(".dropdown-menu.dropdown-pop.post-update");
            var body = $(window);
            // Get modal size
            var w = modal.width();
            var h = modal.height();
            // Get window size
            var bw = body.width();
            var bh = body.height();

            // Update the css and center the modal on screen
            modal.css({
                "position": "fixed",
                "top": ((bh - h) / 2) + "px",
                "left": ((bw - w) / 2) + "px"
            })
        });

        $(window).on('resize', function () {
            var modal = $(".dropdown-menu.dropdown-pop.post-update");
            var body = $(window);
            // Get modal size
            var w = modal.width();
            var h = modal.height();
            // Get window size
            var bw = body.width();
            var bh = body.height();

            // Update the css and center the modal on screen
            modal.css({
                "position": "fixed",
                "top": ((bh - h) / 2) + "px",
                "left": ((bw - w) / 2) + "px"
            })
        });


        //dd('Debugging Server. Please wait for couple of minutes');


        $('.skip_post_older_val').text($('input[name=skip_post_older]').val());
        $('input[name=skip_post_older]').change(function () {
            $('.skip_post_older_val').text($(this).val());
        });
        var rng_one = document.querySelector('input[name=skip_post_older]');
        if (rng_one) {
            var read = function read(evtType) {
                rng_one.addEventListener(evtType, function () {
                    window.requestAnimationFrame(function () {
                        $('.skip_post_older_val').text(rng_one.value);
                    });
                });
            };
            read('mousedown');
            read('mousemove');
        }


        $('.skip_post_newer_val').text($('input[name=skip_post_newer]').val());
        $('input[name=skip_post_newer]').change(function () {
            $('.skip_post_newer_val').text($(this).val());
        });

        var rng_two = document.querySelector('input[name=skip_post_newer]');
        if (rng_two) {
            var read = function read(evtType) {
                rng_two.addEventListener(evtType, function () {
                    window.requestAnimationFrame(function () {
                        $('.skip_post_newer_val').text(rng_two.value);
                    });
                });
            };
            read('mousedown');
            read('mousemove');
        }


    })


</script>
<?php
if(\Auth::check() && !isset($cards_extra)){
$user = \Bulkly\User::find(\Auth::id());
$user_meta = unserialize($user->user_meta);
if(isset($user_meta['temp_subs'])) {
if($user_meta['temp_subs'] === true){
?>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">
    Stripe.setPublishableKey('<?php echo env('STRIPE_KEY'); ?>');

    var $form = $('#payment-form');
    $form.find('.submit').click(function (event) {
        $form.find('.submit').prop('disabled', true);
        Stripe.card.createToken($form, stripeResponseHandler);
        return false;
    });

    function stripeResponseHandler(status, response) {
        var $form = $('#payment-form');
        if (response.error) {
            console.log(response.error.message);
            $form.find('.payment-errors').text(response.error.message).show();
            $form.find('.submit').prop('disabled', false);
        } else {
            var token = response.id;
            console.log(token);
            $form.append($('<input type="hidden" name="stripeToken">').val(token));

            $form.find('.payment-errors').text('Your payment was successful. We\'re redirecting you back to your Bulkly account...').show().removeClass('alert-danger').addClass('alert-success');

            setTimeout(function () {
                $form.get(0).submit();
            }, 5000)

        }
    }
</script>
<?php
}
}
}
?>
<script>
    $(function () {
        $('input[name="slot_amount"]').change(function () {
            console.log($(this).val());
            $('.slot_amount').text($(this).val());
        });
        var slot_amount = document.querySelector("input[name='slot_amount']");
        if (slot_amount) {
            var read = function read(evtType) {
                slot_amount.addEventListener(evtType, function () {
                    window.requestAnimationFrame(function () {
                        $('.slot_amount').text(slot_amount.value);
                    });
                });
            };
            read("mousedown");
            read("mousemove");
        }
    })
</script>


<script type="text/javascript">
    $(function(){
        var homePostingFrequencyTarget = document.getElementById("homePostingFrequency");
        if (homePostingFrequencyTarget !== null) {
            var freqParam = {
                _token : $('meta[name="csrf-token"]').attr('content')
            };
            $.ajax({
                type: "POST",
                url: '<?php echo e(route('homePostingFrequency')); ?>',
                data: freqParam,
                success: function(res) {
                    if(res.status === 2000){
                        var ctx2 = document.getElementById("homePostingFrequency");
                        if (ctx2 !== null) {
                            var homePostingFrequency = new Chart(homePostingFrequencyTarget, {
                                type: 'line',
                                data: {
                                    labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                                    datasets: [
                                        {
                                            label: '',
                                            fill: false,
                                            lineTension: 0.1,
                                            backgroundColor: "rgba(75,192,192,.5)",
                                            borderColor: "rgba(75,192,192,1)",
                                            borderCapStyle: 'butt',
                                            borderDash: [],
                                            borderDashOffset: 0.0,
                                            borderJoinStyle: 'miter',
                                            pointBorderColor: "rgba(75,192,192,1)",
                                            pointBackgroundColor: "#fff",
                                            pointBorderWidth: 1,
                                            pointHoverRadius: 5,
                                            pointHoverBackgroundColor: "rgba(75,192,192,1)",
                                            pointHoverBorderColor: "rgba(220,220,220,1)",
                                            pointHoverBorderWidth: 2,
                                            pointRadius: 1,
                                            pointHitRadius: 50,
                                            data: res.data
                                        }
                                    ]
                                },
                                options: {
                                    scales: {
                                        yAxes: [{
                                            stacked: true
                                        }]
                                    }
                                }
                            });
                        }
                    }
                }
            });
        }
    });
</script>
</body>
</html>
