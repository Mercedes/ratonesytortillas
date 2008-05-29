<?php

	define('YAPB_EXECUTING_OS', ((strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') ? 'WIN' : 'NIX'));
	define('YAPB_SYSTEM_SEPARATOR', (YAPB_EXECUTING_OS == 'WIN') ? '\\' : '/');

	// We need WordPress Settings whatever scope this script is running
	require_once realpath(dirname(__file__) . 
		YAPB_SYSTEM_SEPARATOR . '..' .
		YAPB_SYSTEM_SEPARATOR . '..' .
		YAPB_SYSTEM_SEPARATOR . '..' .
		YAPB_SYSTEM_SEPARATOR . '..' . '/wp-config.php'
	);

	// Get the name of the YAPB plugin dir
	$pathTokens = explode(YAPB_SYSTEM_SEPARATOR, dirname(__FILE__));
	define('YAPB_PLUGINDIR_NAME', $pathTokens[count($pathTokens)-2]);

	define('YAPB_WP_ROOT_DIR',  
		realpath(
			dirname(__file__) . 
			YAPB_SYSTEM_SEPARATOR . '..' . 
			YAPB_SYSTEM_SEPARATOR . '..' . 
			YAPB_SYSTEM_SEPARATOR . '..' .
			YAPB_SYSTEM_SEPARATOR . '..'
		)
	);

	define('YAPB_PLUGINDIR',
		YAPB_WP_ROOT_DIR .
		YAPB_SYSTEM_SEPARATOR . 'wp-content' .
		YAPB_SYSTEM_SEPARATOR . 'plugins' .
		YAPB_SYSTEM_SEPARATOR . YAPB_PLUGINDIR_NAME .
		YAPB_SYSTEM_SEPARATOR
	);

	define('YAPB_PHPTHUMB_DIR', 'phpThumb-1.7.8');
	define('YAPB_CACHE_ROOT_DIR',
		YAPB_WP_ROOT_DIR . 
		YAPB_SYSTEM_SEPARATOR . 'wp-content' .
		YAPB_SYSTEM_SEPARATOR . 'plugins' .
		YAPB_SYSTEM_SEPARATOR . YAPB_PLUGINDIR_NAME .
		YAPB_SYSTEM_SEPARATOR . 'cache' .
		YAPB_SYSTEM_SEPARATOR
	);

	define('YAPB_TABLE_NAME',	$wpdb->prefix . 'yapbimage');
	define('YAPB_PLUGIN_PATH',	get_option('siteurl') . '/wp-content/plugins/' . YAPB_PLUGINDIR_NAME . '/');
	define('YAPB_TPL_PATH',		realpath(dirname(__file__) . '/../tpl') . YAPB_SYSTEM_SEPARATOR);

	define('YAPB_REWRITERULES', '1');			// flag to determine if plugin can change WP rewrite rules
	define('YAPB_QUERYVAR',		'images');		// get/post variable name for querying tag/keyword from WP
	define('YAPB_TAGURL',		'image');		// URL to use when querying tags

?>