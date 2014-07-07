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
		<title><?php echo HTML::entities($site_name) ?></title>
		<meta name="description" content="<?php echo HTML::entities($site_name) ?>">
		<!-- Mobile Viewport meta tags -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<!-- Leaflet CSS -->
		<link rel="stylesheet" href="<?php echo Media::url('bower_components/leaflet/leaflet.css'); ?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo Media::url('bower_components/leaflet-locatecontrol/src/L.Control.Locate.css'); ?>"/>
		<!--[if lte IE 8]>
			<link rel="stylesheet" type="text/css" href="<?php echo Media::url('bower_components/leaflet-locatecontrol/src/L.Control.Locate.ie.css'); ?>"/>
		<![endif]-->
		<link rel="stylesheet" href="<?php echo Media::url('bower_components/leaflet.markercluster/dist/MarkerCluster.css'); ?>" />
		<link rel="stylesheet" href="<?php echo Media::url('bower_components/leaflet.markercluster/dist/MarkerCluster.Default.css'); ?>" />

		<link rel="stylesheet" href="<?php echo Media::url('bower_components/select2/select2.css'); ?>" />

		<?php if (Kohana::$environment == Kohana::PRODUCTION): ?>
		<link rel="stylesheet" type="text/css" href="<?php echo Media::url('css/style.css'); ?>"/>
		<?php else: ?>
		<link rel="stylesheet" type="text/css" href="<?php echo Media::url('css/test/style.css'); ?>"/>
		<?php endif; ?>

		<!-- Global site config -->
		<script type="text/javascript">
		  (function() {
		    window.config = <?php echo json_encode($config, JSON_FORCE_OBJECT); ?>;
		  })();
		</script>
		<!-- end global site config -->

		<!--Change to Init.min.js below for production-->
		<?php if (Kohana::$environment == Kohana::PRODUCTION): ?>
		<script type="text/javascript" src="<?php echo Media::url('bower_components/requirejs/require.min.js'); ?>" data-main="<?php echo Media::url('js/app/Main.min.js'); ?>"></script>
		<?php else: ?>
		<script type="text/javascript" src="<?php echo Media::url('bower_components/requirejs/require.js'); ?>" data-main="<?php echo Media::url('js/app/Main.js'); ?>"></script>
		<?php endif; ?>

		<!-- Google Font -->
		<link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
		<link href='//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700' rel='stylesheet' type='text/css'>

</head>
<body>
<noscript>Ushahidi requires Javascript to be enabled. Please enable Javascript and refresh the page.</noscript>
<?php if (Kohana::$environment !== Kohana::PRODUCTION AND !is_dir(__DIR__ . '/../media/bower_components')): ?>
<div id="app-failure-warning" class="app-warning">
	<h4>Bower Components Missing</h4>
	<p>
		Please check that you have followed the <a href="https://wiki.ushahidi.com/display/WIKI/Installing+Ushahidi+3.x">installation guide</a>
		completely, including installing <a href="http://howtonode.org/introduction-to-npm">NPM</a> and <a href="http://bower.io/">Bower</a>,
		and then running <code>bower install</code> in your terminal.
	</p>
	<p>
		If you have followed all of those steps, and are still seeing this message, please let us know via Github, email, or IRC!
	</p>
</div>
<?php else: ?>
<div id="app-is-loading" class="app-loading">
	<i class="fa fa-2x fa-refresh fa-spin"></i>
	<span class="hide">Loading...</span>
</div>
<?php endif; ?>
</body>
</html>
