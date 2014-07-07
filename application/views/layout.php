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
		<title><?php echo HTML::entities($site_name); ?></title>
		<meta name="description" content="Ushahidi V3">
		<!-- Mobile Viewport meta tags -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="apple-mobile-web-app-capable" content="yes" />

		<?php if (Kohana::$environment == Kohana::PRODUCTION): ?>
		<link rel="stylesheet" type="text/css" href="<?php echo Media::url('css/style.css'); ?>"/>
		<?php else: ?>
		<link rel="stylesheet" type="text/css" href="<?php echo Media::url('css/test/style.css'); ?>"/>
		<?php endif; ?>

		<!-- Google Font -->
		<link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
		<link href='//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700' rel='stylesheet' type='text/css'>

</head>
<body>

	<?php echo $header; ?>
	<?php echo $content; ?>
	<?php echo $footer; ?>

</body>
</html>