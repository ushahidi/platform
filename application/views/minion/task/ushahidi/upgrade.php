<?php if ( ! $quiet): ?>
Executed blah


<?php if ($dry_run): ?>
This was a dry run, if it was a real run the following SQL would've been executed:
<?php endif; ?>
<?php endif; ?>
<?php foreach ($dry_run_sql as $group => $migrations): ?>

<?php $group_padding = str_repeat('#', strlen($group)); ?>
##################<?php echo $group_padding ?>##
# Begin Location: <?php echo $group; ?> #
##################<?php echo $group_padding ?>##

<?php foreach ($migrations as $timestamp => $sql): ?>
# Begin <?php echo $timestamp; ?>

<?php foreach ($sql as $query): ?>

<?php echo $query;?>;
<?php endforeach; ?>

# End <?php echo $timestamp; ?>

<?php endforeach; ?>

################<?php echo $group_padding ?>##
# End Location: <?php echo $group; ?> #
################<?php echo $group_padding ?>##
<?php endforeach; ?>
