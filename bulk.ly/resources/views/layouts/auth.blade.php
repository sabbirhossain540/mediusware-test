<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>

<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TLNT7S');</script>
<script type="text/javascript">(function(o){var b="https://api.autopilothq.com/anywhere/",t="23556ef2086d413ca59b399cb5747679decfd69f94864c3b8fbe825622ff831f",a=window.AutopilotAnywhere={_runQueue:[],run:function(){this._runQueue.push(arguments);}},c=encodeURIComponent,s="SCRIPT",d=document,l=d.getElementsByTagName(s)[0],p="t="+c(d.title||"")+"&u="+c(d.location.href||"")+"&r="+c(d.referrer||""),j="text/javascript",z,y;if(!window.Autopilot) window.Autopilot=a;if(o.app) p="devmode=true&"+p;z=function(src,asy){var e=d.createElement(s);e.src=src;e.type=j;e.async=asy;l.parentNode.insertBefore(e,l);};if(!o.noaa){z(b+"aa/"+t+'?'+p,false)};y=function(){z(b+t+'?'+p,true);};if(window.attachEvent){window.attachEvent("onload",y);}else{window.addEventListener("load",y,false);}})({"app":true});</script><!-- End Google Tag Manager -->
<style type="text/css">
    .auth-container  input.check-toog.left-toog+label{
        padding-left: 50px;
    }
.auth-container input.check-toog.left-toog+label:before {

    left: 12px;
}
</style>
</head>


<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TLNT7S"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

    <div id="app">
        @yield('content')
    </div>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
    <script type="text/javascript">
        Stripe.setPublishableKey('<?php echo env('STRIPE_KEY'); ?>');
        $(function() {
            var $form = $('#payment-form');
            $form.find('.submit').click(function(event) {
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

                    setTimeout(function(){
                    $form.get(0).submit();
                    }, 5000)


                }
            };
            $('input[name=period]').change(function(){
                var period = $(this).val()
                $('.levels').hide();
                $('.levels.'+period).show();
                
            });
            $('input[name=level]').change(function(){
                $('input[name=level]').parent().removeClass('active focus');
                $(this).parent().addClass('active focus');
            });
            
            
            var getUrlParameter = function getUrlParameter(sParam) {
                var sPageURL = decodeURIComponent(window.location.search.substring(1)),
                    sURLVariables = sPageURL.split('&'),
                    sParameterName,
                    i;
            
                for (i = 0; i < sURLVariables.length; i++) {
                    sParameterName = sURLVariables[i].split('=');
            
                    if (sParameterName[0] === sParam) {
                        return sParameterName[1] === undefined ? true : sParameterName[1];
                    }
                }
            };
            
            var plan = getUrlParameter('plan');
            

            function setCookie(cname, cvalue, exdays) {
                var d = new Date();
                d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                var expires = "expires="+d.toUTCString();
                document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
            }
            
            if(plan){
                setCookie('plan', plan, 7);
            }
            
            
            function getCookie(cname) {
                var name = cname + "=";
                var ca = document.cookie.split(';');
                for(var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                    }
                    if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                    }
                }
                return "";
            }
            
            var getPlan = getCookie('plan');
            
            console.log(getPlan);
            
            if(getPlan=='agencyyear' || getPlan =='proplusyear' || getPlan =='proyear'){
               
                $('input[name=period][value=yearly]').parent().click();
                 $('input[name=period][value=yearly]').prop('checked', true);
            }
            
            if(getPlan=='agencymonth' || getPlan =='proplusmonth' || getPlan =='promonth'){
               
                 $('input[name=period][value=monthly]').parent().click();
                 $('input[name=period][value=monthly]').prop('checked', true);
            }
             
            $('input[data-value='+getPlan+']').parent().click();
            $('input[data-value='+getPlan+']').prop('checked', true);
            
            
            
            
            
            

            
        });
    </script>
</body>
</html>
