<?php $__env->startSection('content'); ?>


<div class="container-fluid app-body settings-page">
	<h3>Calendar (Coming Soon)</h3>
	<div class="feedback_from">
		<p><strong>What functionality are you interested in seeing on the calendar? Let me know and I will see what I can do.</strong></p>
		<form id="send-idea">
		<?php echo e(csrf_field()); ?>

			<input type="hidden" name="email" value="<?php echo e($user->email); ?>">
			<input type="hidden" name="sub" value="Bulkly calendar ideas">
			<input type="hidden" name="feed" value="Thanks for your feedback, it is appreciated. I’ll be sure to follow up if I need additional info or have questions.">
			<textarea class="form-control" name="message"></textarea>
			<button type="button" class="btn btn-default width-btn btn-dc btn-center"> Send Ideas</button>
		</form>
	
	</div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>