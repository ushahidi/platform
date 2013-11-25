<?php defined('SYSPATH') OR die('No direct script access.') ?>
<?php

// Unique error identifier
$error_id = uniqid('error');

?>
<section class="login-box">
	<div class="post-header">
		<h5>Uh oh! Looks like something went wrong...</h5>
	</div> <!-- end .post-header -->

	<div class="post-body">
		Error <?php echo $code ?>:</span> <span class="message"><?php echo htmlspecialchars( (string) $message, ENT_QUOTES, Kohana::$charset, TRUE); ?></span>
	</div> <!-- end .post-body -->

</section> <!-- end .login-box -->

