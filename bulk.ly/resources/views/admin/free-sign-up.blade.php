@extends('admin.layouts.app')



@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
			<div class="panel-body">
				<div class="row">
					<div class="col-md-3">
						<div class="list-group"> 
						  	<a class="list-group-item @if (\Request::route()->getName()=='admin') active @endif" href="/admin/">Overview</a>
						  	<a class="list-group-item @if (\Request::route()->getName()=='admin/manage-user') active @endif" href="/admin/manage-user/">Manage User</a>
						  	<a class="list-group-item @if (\Request::route()->getName()=='admin/membership-plan') active @endif" href="/admin/membership-plan">Membership Plan</a>
						  	<a class="list-group-item @if (\Request::route()->getName()=='admin/free-sign-up') active @endif" href="/admin/free-sign-up">Free Sign Up</a>
						  	
						</div>
					</div>

				


					<div class="col-md-9">
						<ul class="list-inline">
						<li>
							
							<a class="btn btn-primary" type="submit" id="free_sign_up" href="/admin/free-sign-up/create" > Create </a>
							
						</li>

							
							

						<!-- <li>
							<form>
								<input class="form-control" type="text" name="search" placeholder="Search">
								<button class="pull-right" style=" position: relative; margin-top: -27px; border: 0px; background: 0px;  padding-right: 12px; outline: none !important;"> <i class="glyphicon glyphicon-search"></i> </button>
							</form>
						</li> -->
					</ul>

					<table class="table table-bordered" > 
						<thead> 
							<tr> 
								 <th>URL</th> <th>Token Key</th> <th>Status</th>  <!-- <th>Trial Ends At</th> --> <th>Created At</th> <!--<th>Last payment date</th> --> <th>Action</th>
							</tr> 
						</thead> 
						<tbody> 

							@foreach($showAccounts as $FreeAccount )

							<tr>

								<td> <a href="/{{ $FreeAccount->url }} ">{{ $FreeAccount-> url }}</a></td>
								<td>{{ $FreeAccount-> token_key }}</td>
								<td>{{ $FreeAccount-> status }}</td>
								<!-- <td   data-countdown="2016/01/01"  >{{ $FreeAccount-> trial_ends_at }}</td> -->
								<td>{{ $FreeAccount-> created_at }}</td>

								<td>

									<a href="/admin/free-sign-up/delete/{{ $FreeAccount->id }}"   class="btn btn-sm btn-danger" >Delete</a>

									<?php
										if ($FreeAccount->status == 0) { ?>
											<a href=" /admin/free-sign-up/renew/{{ $FreeAccount->id }}" class="btn btn-warning btn-sm">Renew</a>

											<?php
										}


										else{ ?>

										

										<?php

										}

									?>
									
									
									
								</td>
																				
								
							</tr>

							@endforeach
							
						 </tbody> 
					 </table>


						

					</div>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>









<script>
	$('[data-countdown]').each(function() {
  var $this = $(this), finalDate = $(this).data('countdown');
  $this.countdown(finalDate, function(event) {
    $this.html(event.strftime('%D days %H:%M:%S'));
  });
});
</script>

@endsection