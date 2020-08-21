@extends('layouts.auth')
@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="auth-container">
				<div class="panel panel-default">
					<div class="panel-body">
						<nav class="navbar">
							<div class="container-fluid">
								<div class="navbar-header">
								  <a class="navbar-brand" href="/">
									<img src="/images/logo.png">
								  </a>
								</div>
								<div class="collapse navbar-collapse" >
									<ul class="nav navbar-nav navbar-right">
										<li class="active"><a href="/subscriptions">Payment</a></li>
										<li>
										<a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
											Logout
										</a>
										<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
											{{ csrf_field() }}
										</form>
										</li>
									</ul>
								</div>
							</div>
						</nav>
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="Register">
								<h3 class="text-center">Payment Information</h3>
								<div class="row">
									<div class="col-md-10 col-md-offset-1">

										<form action="" method="POST" id="payment-form" class="payment-form">
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
															  	<input name="number" class="form-control" type="text" size="20" data-stripe="number"  placeholder="Credit Card Number"> 
															</td>
															<td>
															  	<input name="cvc" class="form-control pl15" type="text" size="4" data-stripe="cvc"  placeholder="CVC"> 
															</td>
													  	</tr>
													  	<tr class="form-row">
															<td class="form-inline" colspan="2">
																<i class="input-icon fa fa-calendar"></i>
															  	<input class="form-control" name="month" type="text" size="2" data-stripe="exp_month"  placeholder="MM"> <span class="divi"> / </span>
															  	<input class="form-control pl15" name="year" type="text" size="4" data-stripe="exp_year"  placeholder="YY"> 
															</td>
													  	</tr>
													</table>
												</div>
											</div>
											<button type="submit" id="submit-payment" class="btn btn-default submit">Start Subscription <i class="fa-btn fa fa-angle-right"></i></button>
											<div class="trial">All plans come with a FREE 7-day trial</div>
										</form>

									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
