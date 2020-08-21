<?php $__env->startSection('content'); ?>
<div class="container-fluid app-body">
	<h3>Social Accounts 

	<?php if($user->plansubs()): ?>
		<?php if($user->plansubs()['plan']->slug == 'proplusagencym' OR $user->plansubs()['plan']->slug == 'proplusagencyy' ): ?>
			<a href="https://bufferapp.com/oauth2/authorize?client_id=<?php echo e(env('BUFFER_CLIENT_ID')); ?>&redirect_uri=<?php echo e(env('BUFFER_REDIRECT')); ?>&response_type=code" class="btn btn-primary pull-right">Add Buffer Account</a>
		<?php endif; ?>
	<?php endif; ?>




	</h3>

	<div class="row">
		<div class="col-md-12">
			<table class="table table-hover social-accounts"> 
				<thead> 
					<tr><th>Account</th> <th>Last post sent</th> <th># of post sent</th> <th>Groups</th> <th>Active</th> </tr> 
				</thead> 
				<tbody> 
				<?php $__currentLoopData = $profiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php if( $profile['type'] != 'google' ): ?>
					<tr>
						<td width="350">
							<div class="media">
								<div class="media-left">
									<a href="">
										<span class="fa fa-<?php echo e($profile['type']); ?>"></span>
										<img width="50" class="media-object img-circle" src="<?php echo e($profile['avatar']); ?>" alt="">
									</a>
								</div>
								<div class="media-body media-middle" style="width: 180px;">
									<h4 class="media-heading"><?php echo e($profile['name']); ?></h4>
								</div>
							</div>
						</td> 
						<td><i class="fa fa-clock-o"></i> <span data-sent="<?php if(isset(unserialize($profile['post_sent'])['last_sent_at'])): ?> <?php echo e(strtotime(unserialize($profile['post_sent'])['last_sent_at'])); ?> <?php endif; ?>"></span></td> 
						<td>
						<?php if(isset(unserialize($profile['post_sent'])['count'])): ?>
						<?php if(unserialize($profile['post_sent'])['count']>0): ?> <?php echo e(unserialize($profile['post_sent'])['count']); ?> <?php else: ?> 0 <?php endif; ?>
						<?php else: ?>
						0
						<?php endif; ?>
						</td> 
						<td><?php echo e(count($profile->groupsact($profile->id))); ?> 
						<span class="dropdown">
							<a href="#" class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">(view)</a>
							<?php if(count($profile->groups($profile->id)) > 0): ?>
							<ul class="dropdown-menu dropdown-center dropdown-pop" aria-labelledby="dropdownMenu1">
								<li>
									<form method="post" id="social-account-id-group-<?php echo e($profile['account_id']); ?>" class="social-account-id-group">
										<?php echo e(csrf_field()); ?>

										<input type="hidden" name="account_id" value="<?php echo e($profile['id']); ?>">
										GROUPS OVERVIEW
										<div style="width: 450px;">
											<ul class="nav nav-tabs nav-justified" role="tablist">
												<li role="presentation" class="active"><a href="#upload-<?php echo e($profile['account_id']); ?>" aria-controls="upload-<?php echo e($profile['account_id']); ?>" role="tab" data-toggle="tab">Content Upload</a></li>
												<li role="presentation"><a href="#curation-<?php echo e($profile['account_id']); ?>" aria-controls="curation-<?php echo e($profile['account_id']); ?>" role="tab" data-toggle="tab">Content Curation</a></li>
												<li role="presentation"><a href="#rssauto-<?php echo e($profile['account_id']); ?>" aria-controls="rssauto-<?php echo e($profile['account_id']); ?>" role="tab" data-toggle="tab">Rss Automotion</a></li>
											</ul>
											<div class="tab-content">
												<div role="tabpanel" class="tab-pane active" id="upload-<?php echo e($profile['account_id']); ?>">
													<ul class="list-unstyled">
														<?php $__currentLoopData = $profile->groups($profile->id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<?php if($group->type=='upload'): ?>
																<?php if(in_array($profile['id'], unserialize($group->target_acounts))): ?>
																	<li><input id="<?php echo e($profile['account_id']); ?>-<?php echo e($group->id); ?>" class="check-toog left-toog" type="checkbox" value="<?php echo e($group->id); ?>" name="group_id[]" checked> <label for="<?php echo e($profile['account_id']); ?>-<?php echo e($group->id); ?>">  <?php echo e($group->name); ?></label> </li>
																<?php else: ?>
																	<li><input id="<?php echo e($profile['account_id']); ?>-<?php echo e($group->id); ?>" class="check-toog left-toog" type="checkbox" value="<?php echo e($group->id); ?>" name="group_id[]"> <label for="<?php echo e($profile['account_id']); ?>-<?php echo e($group->id); ?>"> <?php echo e($group->name); ?></label> </li>
																<?php endif; ?>
															<?php endif; ?>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>     	
													</ul>
												</div>
												<div role="tabpanel" class="tab-pane" id="curation-<?php echo e($profile['account_id']); ?>">
													<ul class="list-unstyled">
														<?php $__currentLoopData = $profile->groups($profile->id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<?php if($group->type=='curation'): ?>
																<?php if(in_array($profile['id'], unserialize($group->target_acounts))): ?>
																	<li><input id="<?php echo e($profile['account_id']); ?>-<?php echo e($group->id); ?>" class="check-toog left-toog" type="checkbox" value="<?php echo e($group->id); ?>" name="group_id[]" checked><label for="<?php echo e($profile['account_id']); ?>-<?php echo e($group->id); ?>"> <?php echo e($group->name); ?></label> </li>
																<?php else: ?>
																	<li><input id="<?php echo e($profile['account_id']); ?>-<?php echo e($group->id); ?>" class="check-toog left-toog" type="checkbox" value="<?php echo e($group->id); ?>" name="group_id[]"><label for="<?php echo e($profile['account_id']); ?>-<?php echo e($group->id); ?>"> <?php echo e($group->name); ?></label> </li>
																<?php endif; ?>
															<?php endif; ?>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>     	
													</ul>
												</div>
												<div role="tabpanel" class="tab-pane" id="rssauto-<?php echo e($profile['account_id']); ?>">
													<ul class="list-unstyled">
														<?php $__currentLoopData = $profile->groups($profile->id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<?php if($group->type=='rss-automation'): ?>
																<?php if(in_array($profile['id'], unserialize($group->target_acounts))): ?>
																	<li><input id="<?php echo e($profile['account_id']); ?>-<?php echo e($group->id); ?>" class="check-toog left-toog" type="checkbox" value="<?php echo e($group->id); ?>" name="group_id[]" checked><label for="<?php echo e($profile['account_id']); ?>-<?php echo e($group->id); ?>">  <?php echo e($group->name); ?></label> </li>
																<?php else: ?>
																	<li><input id="<?php echo e($profile['account_id']); ?>-<?php echo e($group->id); ?>" class="check-toog left-toog" type="checkbox" value="<?php echo e($group->id); ?>" name="group_id[]"> <label for="<?php echo e($profile['account_id']); ?>-<?php echo e($group->id); ?>">  <?php echo e($group->name); ?></label> </li>
																<?php endif; ?>
															<?php endif; ?>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>     	
													</ul>
												</div>
											</div>
										</div>
										<button type="submit" class="btn btn-default">Save</button>
									</form>
								</li>
							</ul>
							<?php endif; ?>
						</span>
						</td> 
						<td>
						<form method="post" action="" id="active-deactive-account-<?php echo e($profile['account_id']); ?>" class="active-deactive-account">
							<?php echo e(csrf_field()); ?>

							<input type="hidden" name="ids" value="<?php echo e($profile['id']); ?>">
							<input id="act-<?php echo e($profile['account_id']); ?>" type="checkbox" <?php if($profile['status'] == '1'): ?> checked <?php endif; ?> name="active_inactive" class="check-toog left-toog">
							<label for="act-<?php echo e($profile['account_id']); ?>"></label>
						</form>
						</td> 
					</tr>
					<?php endif; ?>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</tbody> 
			</table>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>