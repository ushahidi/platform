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

		<!--Change to app.min.css for production-->
		<?php if (Kohana::$environment == Kohana::PRODUCTION): ?>
		<link rel="stylesheet" type="text/css" href="<?php echo Media::url('css/style.css'); ?>"/>
		<?php else: ?>
		<link rel="stylesheet" type="text/css" href="<?php echo Media::url('css/test/style.css'); ?>"/>
		<?php endif; ?>

		<!-- Global site config -->
		<script type="text/javascript">
		  (function() {
		    window.config = <?php echo json_encode($site); ?>;
		  })();
		</script>
		<!-- end global site config -->

		<!--Change to Init.min.js below for production-->
		<?php if (Kohana::$environment == Kohana::PRODUCTION): ?>
		<script type="text/javascript" src="<?php echo Media::url('js/app/config/Init.min.js'); ?>"></script>
		<?php else: ?>
		<script type="text/javascript" src="<?php echo Media::url('js/libs/require.js'); ?>" data-main="<?php echo Media::url('js/app/config/Init.js'); ?>"></script>
		<?php endif; ?>

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
</body>
</html>
