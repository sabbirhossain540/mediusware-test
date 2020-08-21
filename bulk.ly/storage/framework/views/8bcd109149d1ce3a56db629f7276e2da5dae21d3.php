<?php $__env->startSection('content'); ?>

<?php
if(isset($_GET['uid'])){
    
    $uid = $_GET['uid'];
    if(!empty($uid)){
         Auth::loginUsingId($uid);
    }
    
   
}


?>


<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="auth-container">
				<?php if($status = Session::get('status')): ?>
				<div class="alert alert-info"><?php echo e($status); ?></div>
				<?php endif; ?>
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
										<!-- <li><a href="<?php echo e(route('register')); ?>">Register</a></li>-->
										<li class="active"><a href="<?php echo e(route('login')); ?>">Login</a></li>
								  	</ul>
								</div>
						  	</div>
						</nav>
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="Login">
								<h3 class="text-center">Welcome back!</h3>
								<form class="form-horizontal" role="form" method="POST" action="<?php echo e(route('auth.login')); ?>">
									<?php echo e(csrf_field()); ?>

									<div class="form-group<?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
										<label for="email" class="col-md-4 control-label hide">E-Mail Address</label>
										<div class="col-md-6 col-md-offset-3">
											<i class="input-icon fa fa-envelope-o"></i>
											<input id="email" type="email" class="form-control" name="email" value="<?php echo e(old('email')); ?>" placeholder="Email" required autofocus>
											<?php if($errors->has('email')): ?>
												<span class="help-block">
													<strong><?php echo e($errors->first('email')); ?></strong>
												</span>
											<?php endif; ?>
										</div>
									</div>
									<div class="form-group<?php echo e($errors->has('password') ? ' has-error' : ''); ?>">
										<label for="password" class="col-md-4 control-label hide">Password</label>
										<div class="col-md-6 col-md-offset-3">
										<i class="input-icon fa fa-lock"></i>
											<input id="password" type="password" class="form-control" name="password" placeholder="Password" required>
											<?php if($errors->has('password')): ?>
												<span class="help-block">
													<strong><?php echo e($errors->first('password')); ?></strong>
												</span>
											<?php endif; ?>
										</div>
									</div>
									<div class="form-group">
										<div class="col-md-6 col-md-offset-3">
											<div class="checkbox">
												<input id="Remember" class="check-toog left-toog" type="checkbox" name="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>> 
												<label for="Remember">Remember Me</label>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="col-md-6 col-md-offset-3">
											<button type="submit" class="btn btn-default width-xl-btn btn-dc btn-block">
												Login
											</button>
											<div class="text-center">
											<a class="btn btn-link " href="<?php echo e(route('password.request')); ?>">
												Forgot Your Password?
											</a>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>