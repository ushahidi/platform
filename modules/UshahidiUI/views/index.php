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
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Ushahidi V3</title>
    <meta name="description" content="Ushahidi V3">
    <!-- Mobile Viewport meta tags -->
    <meta name="viewport" content="width=device-width, initial-scale=0.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="apple-mobile-web-app-capable" content="yes" />

    <!-- Mobile viewport optimized: h5bp.com/viewport -->
    <meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width">

    <!--Change to app.min.css for production-->
    <link rel="stylesheet" type="text/css" href="<?php echo Media::url('css/global.css'); ?>"/>

    <!-- Font Awesome CSS CDN -->
    <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">

    <!--Change to Init.min.js below for production-->
    <script type="text/javascript" src="<?php echo Media::url('js/libs/require.js'); ?>" data-main="<?php echo Media::url('js/app/config/Init.js'); ?>"></script>
    <!-- <script type="text/javascript" src="<?php echo Media::url('js/app/config/Init.min.js'); ?>"></script> -->

    <!-- Custom Modernizr Build - add, subtract and rebuild at end of project -->
    <script src="<?php echo Media::url('js/libs/custom.modernizr.js'); ?>"></script>

    <!-- Place Typekit script here -->
    <!-- This allow fonts to load asynchronously, but requires custom CSS. The custom css can be found in scss/base/_general.scss-->
    <script type="text/javascript">
      (function() {
        var config = {
          kitId: 'nmg4nns',
          scriptTimeout: 3000
        };
        var h=document.getElementsByTagName("html")[0];h.className+=" wf-loading";var t=setTimeout(function(){h.className=h.className.replace(/(\s|^)wf-loading(\s|$)/g," ");h.className+=" wf-inactive"},config.scriptTimeout);var tk=document.createElement("script"),d=false;tk.src='//use.typekit.net/'+config.kitId+'.js';tk.type="text/javascript";tk.async="true";tk.onload=tk.onreadystatechange=function(){var a=this.readyState;if(d||a&&a!="complete"&&a!="loaded")return;d=true;clearTimeout(t);try{Typekit.load(config)}catch(b){}};var s=document.getElementsByTagName("script")[0];s.parentNode.insertBefore(tk,s)
      })();
    </script>
    <!-- end Typekit script -->



</head>
<body>
</body>
</html>