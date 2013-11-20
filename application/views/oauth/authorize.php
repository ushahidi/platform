<?php defined('SYSPATH') OR die('No direct script access.') ?>

		<div id="main-region">
			<article class="body-wrapper">
				<div class="row">

			<section class="login-box">
				<div class="post-header">
					<h5>Authorization</h5>
				</div> <!-- end .post-header -->

				<div class="post-body">
					<div class="login-form-wrapper">

							The application "<?php echo $params['client_id']; ?>" is asking for authorization with these scopes:
							<ul><?php foreach($scopes as $scope) { ?>
								<li><i class="fa  fa-check-square"></i><?php echo $scope; ?></li>
								<?php } ?>
							</ul>

								<form action="<?php echo Request::current()->url() . URL::query() ?>" method="post" class="authorize-form">
									<input id="authorizeButton" type="submit" class="authorize-button" value="Yes, I authorize this application" />
									<input type="hidden" name="authorize" value="1" />
								</form>

								<form id="cancel" action="<?php echo Request::current()->url() . URL::query() ?>" method="post" class="authorize-cancel-form">
									<input id="cancelButton" type="submit" class="cancel-button" value="Deny this application" />
									<input type="hidden" name="authorize" value="0" />
								</form>
						</ul>

					</div> <!-- end .login-form-wrapper -->

				</div> <!-- end .post-body -->

			</section> <!-- end .login-box -->

			</div>
			</article>
		</div>
