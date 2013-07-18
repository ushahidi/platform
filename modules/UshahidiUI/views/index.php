<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7 ui-mobile-rendering" lang="en"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8 ui-mobile-rendering" lang="en"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9 ui-mobile-rendering" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js ui-mobile-rendering" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <title>Marionette-Require-Boilerplate Lite</title>
    <meta name="description" content="Lightweight Marionette and Require.js Boilerplate Project">

    <!-- Mobile viewport optimized: h5bp.com/viewport -->
    <meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width">

    <!--Change to app.min.css for production-->
    <link rel="stylesheet" type="text/css" href="<?php echo Media::url('css/app.css'); ?>"/>

    <!--Change to Init.min.js below for production-->
    <script type="text/javascript" src="<?php echo Media::url('js/libs/require.js'); ?>" data-main="<?php echo Media::url('js/app/config/Init.js'); ?>"></script>
    <!-- <script type="text/javascript" src="<?php echo Media::url('js/app/config/Init.min.js'); ?>"></script> -->


</head>
<body>
</body>
</html>