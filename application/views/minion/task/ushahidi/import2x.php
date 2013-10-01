<?php if ( ! $quiet): ?>

Created 'Classic Report Form', ID: <?php echo $form_id ?>.
Imported <?php echo $category_count ?> tags.
Imported <?php echo $post_count ?> posts.
Imported <?php echo $user_count ?> users.

<?php if ( $debug): ?>
Max memory usage <?php echo $memory_used ?>
<?php endif; ?>

<?php endif; ?>

