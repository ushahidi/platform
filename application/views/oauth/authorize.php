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
							<li class="show-for-medium-up"><a class="inverse" href="<?php echo URL::site('user/logout'); ?>"><i class="fa  fa-user  fa-lg"></i> LOGOUT</a></li>
							<li class="show-for-small"><a class="inverse" href="<?php echo URL::site('user/logout'); ?>"><i class="fa  fa-user  fa-lg"></i></a></li>
						</ul>
					</section> <!-- end .top-bar-section -->
				</nav> <!-- end .navigation -->
			</header> <!-- end .banner -->
		</div>
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
	</section>
	<aside id="workspace-panel" class="workspace-panel" role="complementary">
	</aside>
</div>
<div id="footer-region"></div>

	</body>
</html>

<html>
	<body>

	</body>
</html>