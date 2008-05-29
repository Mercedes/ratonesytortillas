<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>
<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php wp_head(); ?>
</head>
<body>

<div class="wrap">
   <div class="roundtop">
	 <img src="<?php bloginfo('template_directory'); ?>/images/tl.gif" alt="" 
	 width="18" height="18" class="corner" 
	 style="display: none" />
   </div>
<div id="top">
<div id="header"><h1><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1></div>
<div id="flickr"><a href="http://www.flickr.com"><img src="<?php bloginfo('template_directory'); ?>/images/flickr.gif" alt="Flickr" width="128" height="39" border="0" /></a></div>
</div>

<div id="content">
   <div class="whitewrap">
   <div class="intop">
	 <img src="<?php bloginfo('template_directory'); ?>/images/int_l.gif" alt="" 
	 width="18" height="18" class="corner" 
	 style="display: none" />
   </div>
<div id="nav">
<ul class="nav">
<li><a href="<?php bloginfo('url'); ?>">Home</a></li>
<?php wp_list_pages('title_li='); ?>
</ul>
</div>