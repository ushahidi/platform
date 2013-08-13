<html>
	<body>
	<h1>Login</h1>
	<?php echo Form::open('login/submit' . URL::query()); ?>
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
	
	
	<h1>Register</h1>
	<?php echo Form::open('login/register' . URL::query()); ?>
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
	</body>
</html>