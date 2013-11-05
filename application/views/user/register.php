<html>
	<body>
	<h1>Register</h1>
	<?php echo Form::open('user/submit_register' . URL::query()); ?>
	<?php echo Form::hidden('csrf', Security::token()); ?>
	<ul class="login">
		<li>
			<?php echo Form::input('username', '', array('placeholder' => 'username', 'required', 'aria-required' => 'true')); ?>
		</li>
		<li>
			<?php echo Form::input('password', '', array('placeholder' => 'password', 'type' => 'password', 'required', 'aria-required' => 'true')); ?>
		</li>
		<li>
			<?php echo Form::submit('submit', 'Register'); ?>
		</li>
	</ul>
	</form>

	<h2><a href="<?php echo URL::site('user/login' . URL::query()); ?>">Login</a></h2>
	</body>
</html>