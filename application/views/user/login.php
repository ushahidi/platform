<html>
	<body>
	<h1>Login</h1>
	<?php echo Form::open('user/submit_login' . URL::query()); ?>
	<?php echo Form::hidden('csrf', Security::token()); ?>
	<ul class="login">
		<li>
			<?php echo Form::input('username', '', array('id' => 'login-username', 'placeholder' => 'username', 'required', 'aria-required' => 'true')); ?>
		</li>
		<li>
			<?php echo Form::input('password', '', array('id' => 'login-password', 'placeholder' => 'password', 'type' => 'password', 'required', 'aria-required' => 'true')); ?>
		</li>
		<li>
			<?php echo Form::submit('submit', 'Login', array('id' => 'login-submit')); ?>
		</li>
	</ul>
	</form>

	<h2><a href="<?php echo URL::site('user/register' . URL::query()); ?>">Register</a></h2>
	</body>
</html>