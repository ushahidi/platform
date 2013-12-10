<?php defined('SYSPATH') OR die('No direct script access.') ?>

		<div id="main-region">
			<article class="body-wrapper">
				<div class="row">

			<section class="login-box">
				<div class="post-header">
					<h5>Login</h5>
				</div> <!-- end .post-header -->

				<div class="post-body">
					<div class="login-form-wrapper">

						<?php if (! empty($error)): ?>
						<div class="error">
							<?php echo HTML::entities($error)?>
						</div>
						<?php endif; ?>

						<div class="login-form">
							<?php echo Form::open('user/submit_login' . URL::query()); ?>
							<?php echo Form::hidden('csrf', Security::token()); ?>
								<?php echo Form::input('username', ! empty($form['username']) ? $form['username'] : '', array('id' => 'login-username', 'placeholder' => 'Username', 'required', 'aria-required' => 'true')); ?>
								<?php echo Form::input('password', '', array('id' => 'login-password', 'placeholder' => 'Password', 'type' => 'password', 'required', 'aria-required' => 'true')); ?>
								<div class="login-button-wrapper">
									<?php echo Form::submit('submit', 'Login', array('id' => 'login-submit', 'class' => 'login-button')); ?>
								</div>

								<!-- <p class="medium-text">CrowdmapID is supported</p> -->

							</form> <!-- end .form -->
						</div> <!-- end .login-form -->

					</div> <!-- end .login-form-wrapper -->

					<div class="login-box-meta">
						<p class="medium-text">Don't have an account? <a href="<?php echo URL::site('user/register' . URL::query()); ?>">Register</a></p>
					</div>

				</div> <!-- end .post-body -->

			</section> <!-- end .login-box -->

			</div>
			</article>
		</div>
