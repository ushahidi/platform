<?php

// Sanity check, install should only be checked from index.php
defined('SYSPATH') or exit('Install tests must be loaded from within index.php!');

$failed = FALSE;
$test_groups = array(
	'Preflight' => array(
		'PHP Version' => function( & $message)
		{
			$message = 'Ushahidi requires PHP 5.4 or newer, this is ' .  PHP_VERSION;
			return version_compare(PHP_VERSION, '5.4', '>=');
		},
		'Security' => function( & $message)
		{
			$check = array(
				'ctype' => 'ctype_digit',
				'filter' => 'filter_list',
				'hash' => 'hash',
				);

			foreach ($check as $extension => $function)
			{
				if ( ! function_exists($function)) {
					$message = "The [{$missing}] PHP extension is required for security";
					return FALSE;
				}
			}
			$message = "PHP has basic security requirements";
			return TRUE;
		},
		'i18n' => function( & $message)
		{
			if ( ! @preg_match('/^.$/u', 'ñ'))
			{
				$missing = 'PCRE UTF-8';
			}
			elseif ( ! @preg_match('/^\pL$/u', 'ñ'))
			{
				$missing = 'PCRE Unicode';
			}
			elseif ( ! extension_loaded('iconv'))
			{
				$missing = 'iconv';
			}
			elseif (extension_loaded('mbstring') AND (ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING))
			{
				$missing = 'mbstring without overloading';
			}

			if (isset($missing))
			{
				$message = "PHP requires [{$missing}] for proper international support";
			}
			else
			{
				$message = "PHP has UTF-8 and Unicode support enabled";
			}

			return ! isset($missing);
		},
		'System' => function( & $message)
		{
			$message = "The [system] directory has the required dependencies";
			return (is_dir(SYSPATH) AND is_file(SYSPATH.'classes/Kohana'.EXT));
		},
		'Application' => function( & $message)
		{
			$message = "The [application] directory contains the Ushahidi application";
			return (is_dir(APPPATH) AND is_file(APPPATH.'bootstrap'.EXT));
		},
		'Caching' => function( & $message)
		{
			$message = "The application [cache] directory must be writable";
			return (is_dir(APPPATH) AND is_dir(APPPATH.'cache') AND is_writable(APPPATH.'cache'));
		},
		'Logging' => function( & $message)
		{
			$message = "The application [logs] directory must be writable";
			return (is_dir(APPPATH) AND is_dir(APPPATH.'logs') AND is_writable(APPPATH.'logs'));
		},
	),
	'Installer' => array(
		'Bootstrap' => function( & $message)
		{
			// Bootstrap the application
			return is_file(APPPATH.'bootstrap'.EXT);
		},
		'Database' => function ( & $message)
		{
			$config = Kohana::$config->load('database')->default;
			return ! empty($config);
		},
	),
);

$tests_total = 0;
$tests_done = 0;

foreach ($test_groups as $a) {
	foreach ($a as $b) {
		$tests_total += count($b);
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Ushahidi Installation</title>

	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

	<style type="text/css">
		*, *:before, *:after {
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
		}

		body {
			background: #eff0f3;
			color: #1f1f1f;
			padding: 0;
			margin: 0;
			font-family: "Open Sans","Helvetica Neue","Helvetica",Helvetica,Arial,sans-serif;
			font-weight: normal;
			font-style: normal;
			line-height: 1;
			position: relative;
			cursor: default;
		}

		h1, h2, h3, h4, h5, h6, .post-form-wrapper label {
			font-family: "Open Sans","Helvetica Neue","Helvetica",Helvetica,Arial,sans-serif;
			font-weight: 700;
			font-style: normal;
			color: #565656;
			text-rendering: optimizeLegibility;
			margin-top: 0.2rem;
			margin-bottom: 0.5rem;
			line-height: 1.4;
		}

		h2 {
			font-size: 2.3125rem;
		}

		h3 {
			font-size: 1.6875rem;
		}

		p {
			font-family: inherit;
			font-weight: normal;
			font-size: 1rem;
			line-height: 1.6;
			margin: 0 0 1.25rem 0;
			text-rendering: optimizeLegibility;
		}
		
		.hide {
			display: none;
		}

		#powerTip {
			cursor: default;
			background-color: #333;
			background-color: rgba(0, 0, 0, 0.8);
			border-radius: 6px;
			color: #fff;
			display: none;
			padding: 10px;
			position: absolute;
			white-space: nowrap;
			z-index: 2147483647;
			font-size: 0.8em;
		}

		#powerTip:before {
			content: "";
			position: absolute;
		}

		#powerTip.s:before {
			border-right: 5px solid transparent;
			border-left: 5px solid transparent;
			left: 50%;
			margin-left: -5px;
			border-bottom: 10px solid #333;
			border-bottom: 10px solid rgba(0, 0, 0, 0.8);
			top: -10px;
		}

		#powerTip.sw-alt:before, #powerTip.se-alt:before {
			border-top: none;
			border-left: 5px solid transparent;
			border-right: 5px solid transparent;
			left: 10px;
			border-bottom: 10px solid #333;
			border-bottom: 10px solid rgba(0, 0, 0, 0.8);
			bottom: auto;
			top: -10px;
		}

		#powerTip.se-alt:before {
			left: auto;
			right: 32px;
		}

		.body-wrapper {
			padding-left: 0.9375rem;
			padding-right: 0.9375rem;
			width: 100%;
			float: left;
			margin-top: 20px;
		}

		.row {
			width: 100%;
			margin-left: auto;
			margin-right: auto;
			margin-top: 0;
			margin-bottom: 0;
			max-width: 80rem;
		}

		.row:before, .row:after {
			content: " ";
			display: table;
		}

		.ushahidi-installer {
			padding-left: 0.9375rem;
			padding-right: 0.9375rem;
			width: 50%;
			float: left;
			margin-left: auto;
			margin-right: auto;
			float: none !important;
			margin-top: 100px;
		}

		.ushahidi-installer .installer-header {
			padding-left: 0.9375rem;
			padding-right: 0.9375rem;
			width: 100%;
			float: left;
			background: #323a45;
			padding: 20px;
			color: #fcfcfc;
		}

		.ushahidi-installer .installer-logo-wrapper {
			padding-left: 0.9375rem;
			padding-right: 0.9375rem;
			width: 16.66667%;
			float: left;
		}

		.ushahidi-installer .installer-logo-wrapper .installer-logo {
			background: url("data:image/png;base64,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") no-repeat;
			background-size: 100%;
			height: 81px;
			width: 100%;
			margin: 10px 0 0;
			float: left;
		}

		.ushahidi-installer .installer-title-wrapper {
			padding-left: 0.9375rem;
			padding-right: 0.9375rem;
			width: 83.33333%;
			float: left;
		}

		.ushahidi-installer .installer-title-wrapper .installer-title {
			color: #fcfcfc;
		}

		.ushahidi-installer .installer-check-list {
			padding-left: 0;
			padding-right: 0;
			width: 100%;
			float: left;
			background: white;
			margin: 0;
			list-style: none;
		}

		.ushahidi-installer .installer-check-list .installer-check-list__item {
			border-bottom: 1px solid #eff0f3;
			padding: 15px 30px;
			color: #9a9797;
			line-height: 1.6em;
		}

		.ushahidi-installer .installer-check-list .installer-check-list__item:before {
			float: right;
			display: inline-block;
			font-family: FontAwesome;
			font-size: 20px;
			font-style: normal;
			font-weight: normal;
			content: "\f058";
			color: #3cb81c;
			position: relative;
			top: 4px;
		}

		.ushahidi-installer .installer-check-list .fail.installer-check-list__item:before {
			content: "\f057";
			color: #c50813;
		}

		.ushahidi-installer footer {
			padding-left: 0.9375rem;
			padding-right: 0.9375rem;
			width: 100%;
			float: left;
			padding: 0;
		}

		.ushahidi-installer footer.installer-fail {
			background-color: #c50813;
			color: #fcfcfc;
		}

		.ushahidi-installer footer.installer-pass {
			background-color: #3cb81c;
			color: #fcfcfc;
		}

		.ushahidi-installer footer.installer-pass .install-footer {
			padding-left: 0.9375rem;
			padding-right: 0.9375rem;
			width: 100%;
			float: left;
			background-color: #36a619;
		}

		.ushahidi-installer footer.installer-pass .install-footer .install-button {
			border-style: solid;
			border-width: 1px;
			cursor: pointer;
			font-family: inherit;
			font-weight: bold;
			line-height: normal;
			margin: 0 0 1.25rem;
			position: relative;
			text-decoration: none;
			text-align: center;
			-webkit-appearance: none;
			-webkit-border-radius: 0;
			display: inline-block;
			padding-top: 0.5625rem;
			padding-right: 1.125rem;
			padding-bottom: 0.625rem;
			padding-left: 1.125rem;
			font-size: 0.8125rem;
			background-color: #eff0f3;
			border-color: #f1f1f4;
			color: #333333;
			border-radius: 3px;
			margin: 10px 0;
			float: right;
		}

		.ushahidi-installer footer.installer-pass .install-footer .install-button:hover, .ushahidi-installer footer.installer-pass .install-footer .install-button:focus {
			background-color: #f1f1f4;
		}

		.ushahidi-installer footer.installer-pass .install-footer .install-button:hover, .ushahidi-installer footer.installer-pass .install-footer .install-button:focus {
			color: #333333;
		}

		.ushahidi-installer footer.installer-installing {
			background-color: #ffad19;
			color: #fcfcfc;
		}

		.ushahidi-installer footer .message-wrapper {
			padding-left: 0.9375rem;
			padding-right: 0.9375rem;
			width: 100%;
			float: left;
			padding: 40px;
		}

		.ushahidi-installer footer .footer-icon {
			padding-left: 0.9375rem;
			padding-right: 0.9375rem;
			width: 16.66667%;
			float: left;
			font-size: 1.875rem;
			text-align: center;
		}

		.ushahidi-installer footer .footer-icon .fa {
			margin-top: 6px;
		}

		.ushahidi-installer footer .footer-message {
			padding-left: 0.9375rem;
			padding-right: 0.9375rem;
			width: 83.33333%;
			float: left;
		}

		.ushahidi-installer footer .footer-message .header {
			color: #fcfcfc;
		}
	</style>

</head>
<body>

	<article class="body-wrapper">
		<div class="row">
			<div class="ushahidi-installer">

				<header class="installer-header">

					<div class="installer-logo-wrapper">
						<div class="installer-logo"></div>
					</div> <!-- end .installer-logo-wrapper -->

					<div class="installer-title-wrapper">
						<h2 class="installer-title">Ushahidi Installer</h2>

						<p class="installer-message">The following tests have to be run to determine if Ushahidi will work on this server</p>
					</div> <!-- end .installer-title-wrapper -->

				</header> <!-- end .installer-header -->


				<ol class="installer-check-list">
					<?php
					foreach ($test_groups as $group => $tests) {
						foreach ($tests as $name => $test) {
							$success = $test($message);
							if ( ! $success)
							{
								$failed = TRUE;
							}
							$tests_done++;
							?>
							<li class="installer-check-list__item  <?php if (!$success) { echo 'fail'; } ?>" title="<?php echo $message ?>"
								><?php echo $name; ?></li>
							<?php
						}
						if ($failed)
						{
							break; // do not check any more groups
						}
					}
					if ($failed AND ($tests_total - $tests_done) > 0): ?>
						<li class="fail" title="Additional checks skipped due to failures"
							><?php echo ($tests_total - $tests_done) ?> Tests Remaining</li>
					<?php endif ?>
				</ol>

	<?php if ($failed): ?>
		<footer class="installer-fail">
			<div class="message-wrapper">
				<aside class="footer-icon"><i class="fa  fa-warning"></i></aside>
				<div class="footer-message">
					<h3 class="header">Application Error</h3>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et</p>
				</div>
			</div>
		</footer> <!-- end .installer-fail -->
	<?php else: ?>
		<footer class="installer-pass">
			<div class="message-wrapper">
				<aside class="footer-icon"><i class="fa  fa-check-circle"></i></aside>
				<div class="footer-message">
					<h3 class="header">Ushahidi is Ready</h3>
				</div>
			</div>
			<div class="install-footer hide">
				<button class="install-button">Install</button>
			</div>
		</footer> <!-- end .installer-pass -->

		<footer class="installer-installing hide">
			<div class="message-wrapper">
				<aside class="footer-icon"><i class="fa  fa-rocket"></i></aside>
				<div class="footer-message">
					<h3 class="header">Ushahidi is Installing</h3>
				</div>
			</div>
		</footer> <!-- end .installer-installing -->
	<?php endif ?>

			</div> <!-- end .ushahidi-installer -->
		</div> <!-- end .row -->
	</article> <!-- end .body-wrapper -->

	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-powertip/1.2.0/jquery.powertip.min.js"></script>
	<script type="text/javascript">
	$(function() {
		$('li[title]').powerTip({
			placement: 'se-alt',
			smartPlacement: true
			});
	});
	</script>

</body>
</html>
