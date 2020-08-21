<style type="text/css">
	.subs_min_cont{
		display: none;position: fixed;z-index: 555000;background: rgba(245, 248, 250, 0.93);width: 100%;margin: 0 auto;top: 0;bottom: 0;
	}
	.subs_min {
	position: absolute;
    z-index: 5555555;
    background: #fff;
    left: 50%;
    top: 50%;
    max-width: 545px;
    transform: translate(-50%,-50%);
    box-shadow: 0px 12px 20px 5px rgba(0, 0, 0, 0.05);
	}
</style>


<?php 
 $SocialAccountsCount = \Bulkly\SocialAccounts::where('user_id', \Auth::id())->count();

?>

<div class="subs_min_cont">
<div class="subs_min">
<h3 class="text-center">Start Your 7 Day Free Trial <span class="close pull-right" style="position: absolute; right: 28px;">&times;</span></h3>

	<div class="col-md-10 col-md-offset-1">
<p class="text-center">Activate your Bulkly account with a free 7 day trial and see how easy it is to keep your Buffer account full of great content to share.</p>
		<form action="/subscriptions" method="POST" id="payment-form" class="payment-form">
			<div class="payment-errors alert alert-danger"></div>
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			@if(isset($group))
				<input type="hidden" name="group_id" value="{{$group->id}}">
			@endif

			<div class="row plans">
				<?php

				$plans_m = \Bulkly\Plan::where('type', 'Month')->orderBy('price')->get();
				$plans_y = \Bulkly\Plan::where('type', 'Year')->orderBy('price')->get();


				$bfplan = array('promonth-bf','proplusmonth-bf','agencymonth-bf','proyear-bf', 'proplusyear-bf', 'agencyyear-bf');
				?>
				<div class="col-sm-12 text-center">
					<div class="prices-button">
						<div class="btn-group-container" style="margin-bottom: 35px;">
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
						<div class="btn-group-container">
							<div class="btn-group levels monthly active" data-toggle="buttons">
								@foreach($plans_m as $key => $plan)
                                    @if(in_array($plan->slug, $bfplan))
                                    @else

									<label class="btn btn-default <?php if( 0 < $SocialAccountsCount && $SocialAccountsCount < 10 &&  $plan->slug == 'promonth') { echo 'active';} else if ( 9 < $SocialAccountsCount && $SocialAccountsCount < 20 &&  $plan->slug == 'proplusmonth') {echo 'active';} else if ( 19 < $SocialAccountsCount &&  $plan->slug == 'agencymonth') { echo 'active';} ?>">
										<span>{{$plan->name}}</span>
										<input data-value="{{$plan->slug}}" type="radio" name="id" value="{{$plan->slug}}|{{$plan->name}}" <?php if( 0 < $SocialAccountsCount && $SocialAccountsCount < 10 &&  $plan->slug == 'promonth') { echo 'checked';} else if ( 9 < $SocialAccountsCount && $SocialAccountsCount < 20 &&  $plan->slug == 'proplusmonth') {echo 'checked';} else if ( 19 < $SocialAccountsCount &&  $plan->slug == 'agencymonth') { echo 'checked';} ?>> ${{$plan->price}}
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
								<i class="input-icon fa fa-user"></i>
							  	<input class="form-control" type="text" name="first_name" data-stripe="name" placeholder="First name"> 
							</td>
							<td>
							  	<input class="form-control pl15" type="text" name="last_name"  placeholder="Last name"> 
							</td>
					  	</tr>

						<tr class="form-row" >
							<td width="50%">
								<i class="input-icon fa fa-credit-card"></i>
							  	<input class="form-control" type="text" size="20" data-stripe="number"  placeholder="Credit Card #"> 
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
			<button type="submit" id="submit-payment" class="btn btn-default submit">Activate My Bulkly Account <i class="fa-btn fa fa-angle-right"></i></button>
			<div class="trial">All plans come with a FREE 7-day trial</div>
		</form>

	</div>
</div>

</div>