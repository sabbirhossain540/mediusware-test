
<div class="dropdown">
	<div id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn">
		<span class="fa fa-ellipsis-v"></span>
	</div>
	<ul class="dropdown-menu dropdown-pop post-to" aria-labelledby="dLabel">
	   <li>
			POST TO
			<ul class="list-inline">
				<?php $__currentLoopData = $group->targertservices(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $targertservice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<?php if($targertservice != 'google'): ?>
				<li><i class="fa fa-<?php echo e($targertservice); ?>"></i></li>
				<?php endif; ?>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</ul>  
		   <br>
			<table class="table">
				<tr>
					<td width="200">GROUP</td><td width="200" align="right"> <?php echo e($group->name); ?></td>
				</tr>
				<tr>
					<td width="200"># OF POSTS</td><td width="200" align="right"> <?php echo e(count($group->posts)); ?></td>
				</tr>
				
				<tr>
					<td width="200">FREQUENCY</td><td width="200" align="right"> <?php echo e($group->frequency); ?> times <?php echo e($group->interval); ?></td>
				</tr>
				<?php if($group->status=='1'): ?>
				<tr>
					<td width="200">POST LAST SENT</td><td width="200" align="right"> 
					<?php
					$buffpost = \Bulkly\BufferPosting::where('group_id', $group->id)->orderBy('id', 'desc')->first();
					if($buffpost) {
					if($buffpost->sent_at){  
						?>
						<?php echo e(\Carbon\Carbon::parse($buffpost->sent_at)->diffForHumans(\Carbon\Carbon::now(), true)); ?> ago
						<?php
					}
					}
					?>
					</td>
				</tr>
				<tr>
					<td width="200">NEXT TIME TO SEND</td><td width="200" align="right"> 
					<?php if($group->next_schedule_time): ?>
						<?php echo e(\Carbon\Carbon::parse($group->next_schedule_time)->diffForHumans(\Carbon\Carbon::now(), true)); ?> from now
					<?php endif; ?>
					</td>
				</tr>
				 <?php endif; ?>
				
				
				
				<tr>
					<td width="200">RECYCLED POST</td><td width="200" align="right"><?php if($group->recycle =='1'): ?> Yes <?php else: ?> No <?php endif; ?></td>
				</tr>
			</table>
			
			<?php if($group->type=='upload'): ?>
			<a href="<?php echo e(route('content-completed', $group->id)); ?>" class="btn btn-default width-btn btn-dc">
			<?php endif; ?>
			<?php if($group->type=='curation'): ?>
			<a href="<?php echo e(route('content-curation-completed', $group->id)); ?>" class="btn btn-default width-btn btn-dc">
			<?php endif; ?>
			<?php if($group->type=='rss-automation'): ?>
			<a href="<?php echo e(route('rss-automation-completed', $group->id)); ?>" class="btn btn-default width-btn btn-dc">
			<?php endif; ?>
			Edit Group
			</a>
			<form id="group-delete-<?php echo e($group->id); ?>" class="group-delete" method="POST" style="display: inline-block;">
				<?php echo e(csrf_field()); ?>

				<input type="hidden" name="group_id" value="<?php echo e($group->id); ?>">
				<button type="submit" class="btn btn-default btn-icon-round"><span class="fa fa-trash-o"></span></button>
			</form>

	   </li>
	</ul>
</div>