<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->
<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>Ushahidi V3</title>
		<meta name="description" content="Ushahidi V3">
		<!-- Mobile Viewport meta tags -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<!-- Leaflet CSS -->
		<link rel="stylesheet" href="<?php echo Media::url('css/plugins/leaflet.css'); ?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo Media::url('js/libs/leaflet-locatecontrol/src/L.Control.Locate.css'); ?>"/>
		<!--[if lte IE 8]>
		    <link rel="stylesheet" href="<?php echo Media::url('css/plugins/leaflet.ie.css'); ?>" />
				<link rel="stylesheet" type="text/css" href="<?php echo Media::url('js/libs/leaflet-locatecontrol/src/L.Control.Locate.ie.css'); ?>"/>
		<![endif]-->

		<!-- Mobile viewport optimized: h5bp.com/viewport -->
		<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width">

		<!--Change to app.min.css for production-->
		<link rel="stylesheet" type="text/css" href="<?php echo Media::url('css/test/style.css'); ?>"/>
		<!--<link rel="stylesheet" type="text/css" href="<?php echo Media::url('css/style.css'); ?>"/>-->

		<!--Change to Init.min.js below for production-->
		<script type="text/javascript" src="<?php echo Media::url('js/libs/require.js'); ?>" data-main="<?php echo Media::url('js/app/config/Init.js'); ?>"></script>
		<!-- <script type="text/javascript" src="<?php echo Media::url('js/app/config/Init.min.js'); ?>"></script> -->

		<!-- Custom Modernizr Build - add, subtract and rebuild at end of project -->
		<script src="<?php echo Media::url('js/libs/custom.modernizr.js'); ?>"></script>

		<!-- cross browser CSS3 pseudo-classes and attribute selectors with Selectivizr -->
		<!--[if (gte IE 6)&(lte IE 8)]>
			<script type="text/javascript" src="js/vendor/selectivizr/selectivizr.js"></script>
			<noscript><link rel="stylesheet" href="[fallback css]" /></noscript>
		<![endif]-->

		<!-- Google Font -->
		<link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
		<link href='//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700' rel='stylesheet' type='text/css'>

</head>
<body>

<div class="content-wrapper  js-content-wrapper">
	<h1 class="visually-hidden">Main Section</h1>
	<section id="main" role="main">
		<div id="header-region">
			<header role="banner">
				<nav role="navigation" class="top-bar  top-bar-login" data-options="is_hover:false">
					<ul class="title-area">
						<!-- retina ready logo is a css background image -->
						<li class="name">
							<div class="logo-image"></div>
							<h1><a href="<?php echo URL::site(''); ?>">Deployment Name</a></h1>
						</li> <!-- end .name -->
					</ul> <!-- end .title-area -->

			    <section class="top-bar-section  top-bar-section-login">
						<!-- Right Nav Section -->
						<ul class="right">
							<li class="show-for-medium-up"><a class="inverse" href="<?php echo URL::site('user'); ?>"><i class="fa  fa-user  fa-lg"></i> LOGIN/REGISTER</a></li>
							<li class="show-for-small"><a class="inverse" href="<?php echo URL::site('user'); ?>"><i class="fa  fa-user  fa-lg"></i></a></li>
						</ul>
					</section> <!-- end .top-bar-section -->
				</nav> <!-- end .navigation -->
			</header> <!-- end .banner -->

			<!-- on mobile devices deployment name is moved below topbar nav for better layout-->
			<div class="name-small  show-for-small">
				<div class="logo-image"></div>
				<h4><a href="#">Deployment Name</a></h4>
			</div>
		</div>
		<div id="main-region">
			<article class="body-wrapper">
				<div class="row">
			<section class="login-box">
				<div class="post-header">
					<h5>Register your account</h5>
				</div> <!-- end .post-header -->

				<div class="post-body">
					<div class="login-form-wrapper">

						<div class="login-form">
							<?php echo Form::open('user/submit_register' . URL::query()); ?>
								<?php echo Form::hidden('csrf', Security::token()); ?>

								<?php echo Form::input('email', '', array('placeholder' => 'Email', 'type' => 'email')); ?>
								<?php echo Form::input('username', '', array('placeholder' => 'Username', 'required', 'aria-required' => 'true')); ?>
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

								<?php echo Form::submit('submit', 'Register', array('id' => 'register-submit', 'class' => 'submit-button')); ?>

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
	</section>
	<aside id="workspace-panel" class="workspace-panel" role="complementary">
	</aside>
</div>
<div id="footer-region"></div>

	</body>
</html>