<?php defined('SYSPATH') OR die('No direct script access.') ?>

		<div id="main-region">
			<article class="body-wrapper">
				<div class="row">
			<section class="login-box">
				<div class="post-header">
					<h5>Register your account</h5>
				</div> <!-- end .post-header -->

				<div class="post-body">
					<div class="login-form-wrapper">

						<?php if (! empty($error)): ?>
						<div class="error">
							<?php echo HTML::entities($error)?>
						</div>
						<?php endif; ?>

						<div class="login-form">
							<?php echo Form::open('user/submit_register' . URL::query()); ?>
								<?php echo Form::hidden('csrf', Security::token()); ?>

								<?php echo Form::input('email', ! empty($form['email']) ? $form['email'] : '', array('placeholder' => 'Email', 'type' => 'email')); ?>
								<?php echo Form::input('verify_email', ! empty($form['verify_email']) ? $form['verify_email'] : '', array('placeholder' => 'Verify email', 'type' => 'email')); ?>
								<?php echo Form::input('username', ! empty($form['username']) ? $form['username'] : '', array('placeholder' => 'Username', 'required', 'aria-required' => 'true')); ?>
								<?php echo Form::input('password', '', array('placeholder' => 'Password', 'type' => 'password', 'required', 'aria-required' => 'true')); ?>

								<!--<div class="login-form-checkboxes">
									<label for="updates" class="medium-text">
										<input type="checkbox" name="updates" value="updates" align="bottom">
										stay informed with updates
									</label>

									<label for="terms" class="medium-text">
										<input type="checkbox" name="terms" value="terms" align="bottom">
										I agree to the <a href="#terms-and-conditions">terms &amp; conditions</a>
									</label>
								</div>-->

								<div class="register-button-wrapper">
									<?php echo Form::submit('submit', 'Register', array('id' => 'register-submit', 'class' => 'register-button')); ?>
								</div>

							</form> <!-- end .form -->
						</div> <!-- end .login-form -->

					</div> <!-- end .login-form-wrapper -->

					<div class="login-box-meta">
						<p class="medium-text">Already have an account? <a href="<?php echo URL::site('user/login' . URL::query()); ?>">Login</a></p>
					</div>

				</div> <!-- end .post-body -->

			</section> <!-- end .login-box -->

			</div>
			</article>
		</div>
