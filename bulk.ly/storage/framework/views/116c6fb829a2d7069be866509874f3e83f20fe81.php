<?php $__env->startSection('content'); ?>
<div class="container-fluid app-body settings-page">
	<h3>Buffer Post</h3>
	<div class="row">
		<div class="col-md-12">
			<div class="col-md-4">
				 <form class="input-group"  method="GET" action="<?php echo e(route('bufferPosts.index')); ?>">
                  <input type="text" class="form-control" name="search" placeholder="Search" value="<?php echo e(request()->query('search')); ?>">
                </form>
			</div>
			<div class="col-md-4">
				<form class="input-group"  method="GET" action="<?php echo e(route('bufferPosts.index')); ?>">
                  <input type="date" class="form-control" name="datesearch" placeholder="date" onselect="HandleDataSearch()">
                </form>
			</div>
			<div class="col-md-4">
				<form class="input-group"  method="GET" action="<?php echo e(route('bufferPosts.index')); ?>">
                  <select class="form-control" name="groupSearch" id="groupSearch" placeholder="groupSearch">
                  	<option value="">All Group</option>
                  	<option value="upload">Upload</option>
                  	<option value="curation">Curation</option>
                  	<option value="rss-automation">RSS Automation</option>
                  </select>
                </form>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<table class="table table-hover social-accounts"> 

				<thead> 
					<tr><th>Group Name</th> <th>Group Type</th> <th>Account Name</th> <th>Post Text</th> <th>Time</th> </tr> 
				</thead> 
				<tbody> 
					<?php $__currentLoopData = $bufferPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $BufferPost): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr>
							<td><?php echo e($BufferPost->groupInfo->name); ?></td>
							<td><?php echo e($BufferPost->groupInfo->type); ?></td>
							<td><?php echo e($BufferPost->accountInfo->name); ?></td>
							<td><?php echo e($BufferPost->post_text); ?></td>
							<td><?php echo e($BufferPost->sent_at); ?></td>
						</tr>

					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

				</tbody> 

			</table>
			<?php echo e($bufferPosts->appends([request()->query('search')])->links()); ?>

		</div>
	</div>
</div>

<script type="text/javascript">
	
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>