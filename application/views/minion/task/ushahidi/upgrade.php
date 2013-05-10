<?php if ( ! $quiet): ?>

Created 'Classic Report Form', id: <?php echo $form_id ?>
Imported <?php echo $category_count ?> tags
Imported <?php echo $post_count ?> posts

Max memory usage <?php echo $memory_used ?>

<?php if ($dry_run): ?>
This was a dry run, if it was a real run the following SQL would've been executed:
<?php endif; ?>
<?php endif; ?>

