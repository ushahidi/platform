<?php defined('SYSPATH') OR die('No direct script access.') ?>
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
		<title><?php echo HTML::entities($site_name); ?> : Error</title>
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

	<?php include Kohana::find_file('views', 'header'); ?>
		<div id="main-region">
			<article class="body-wrapper">
				<div class="row">
					<?php echo $content; ?>
				</div>
			</article>
		</div>
	<?php include Kohana::find_file('views', 'footer'); ?>

</body>
</html>