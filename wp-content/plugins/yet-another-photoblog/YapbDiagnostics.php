<?php

	require_once realpath(dirname(__file__) . '/includes/YapbConstants.script.php');
	require_once realpath(dirname(__file__) . '/lib/Yapb.class.php');

	// Instance Yapb

	$yapb = new Yapb();

	/**
	 * Function checks existance, read- and writeability of a given directory
	 * @param string $path Absolute systempath of the directory
	 **/
	function checkDirectory($path) {
		// Check if this path represents a directory
		if (is_dir($path)) {
			// Check if this directory is readable
			if (is_readable($path)) {
				// Check if this directory is writeable
				if (is_writable($path)) {
					// If we have an *nix system - Check if the directory is executable
					if ((YAPB_EXECUTING_OS == 'NIX') && function_exists('is_executable')) {
						if (is_executable($path)) {
							echo '<span style="color:green;"><strong>OK</strong> (*nix)</span>';
						} else {
							echo '<span style="color:red;"><strong>Not executable!</strong> Please set directory permission of ' . $path . ' to <strong>777</strong>.</span>';
						}
					} else {
						echo '<span style="color:green;"><strong>OK</strong> (Windows)</span>';
					}
				} else {
					echo '<span style="color:red;"><strong>Not writable!</span> Please set directory permission of ' . $path . ' to <strong>777</strong>.</span>';
				}
			} else {
				echo '<span style="color:red;"><strong>Not readable!</strong> Please set directory permission of ' . $path . ' to <strong>777</strong>.</span>';
			}
		} else {
			echo '<span style="color:red;"><strong>Not existant!</strong> Please create directory ' . $path . '</span>';
		}
	}


	/**
	 * Function compares current WordPress version against YAPB requirements
	 * @param string $installedWordPressVersion
	 **/
	function checkWpVersion($installedWordPressVersion) {

		global $yapb;
		
		$version = explode('.', $installedWordPressVersion);
		$required = explode('.', $yapb->requiredWordPressVersion);

		$works = true;
		for ($i=0, $len=count($required); $i<$len; $i++) {
			if ($version[$i] < $required[$i]) {
				$works = false;
				break;
			}
		}

		if ($works) {
			echo '<span style="color:green;"><strong>OK</strong> (' . $installedWordPressVersion . ')</span>';
		} else {
			echo '<span style="color:red;"><strong>' . $installedWordPressVersion . '</strong> (YAPB ' . $yapb->pluginVersion . ' requires WordPress ' . $yapb->requiredWordPressVersion . ' or higher)</span>';
		}

	}


?>
<html>
<head>
	<title>YAPB Diagnostics</title>
	<style type="text/css">
		body {
			background-color:white; 
			padding-bottom:20px;
		}
		h1 { color:#0066CC; }
		h3 { color:#999999; }
		th {
			text-align:left;
			padding:5px;
			background-color:#efefef;
			border-left:1px solid gray;
			border-bottom:1px solid gray;
		}
		td {
			padding:5px;
			border-left:1px solid gray;
			vertical-align:top;
		}
	</style>
</head>
<body>
	
	<h1>YAPB Diagnostics</h1>

	<h2>Version Informations</h2>
	<ul>
		<li>YAPB Version: <strong><?php echo $yapb->pluginVersion ?></strong></li>
		<li>WordPress version: <strong><?php echo get_bloginfo('version') ?></strong></li>
		<li>WordPress version tested up to: <strong><?php echo $yapb->highestTestedWordPressVersion ?></strong></li>
		<li>WordPress version required at least: <strong><?php echo $yapb->requiredWordPressVersion ?></strong></li>
	</ul>

	<h2>Automatic Diagnostics</h2>

	<ol>
		<li>WordPress Version: <?php checkWpVersion(get_bloginfo('version')) ?></li>
		<li>WordPress Upload Directory: 
			<?php
				$upload_path = get_settings('upload_path');
				$wp_upload_path = YAPB_WP_ROOT_DIR . YAPB_SYSTEM_SEPARATOR . preg_replace('#/|\\\#', YAPB_SYSTEM_SEPARATOR, $upload_path);
				checkDirectory($wp_upload_path);
			?>
		</li>
		<li>
			Yapb Cache Dir: <?php checkDirectory(YAPB_CACHE_ROOT_DIR) ?>
		</li>
	</ol>

	<h2>Debugging Information</h2>

	<h3>Server Info</h3>
	<table border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th>Option</th>
				<th>Value</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>realpath(__file__)</td>
				<td><?php echo realpath(__file__) ?></td>
			</tr>
			<tr>
				<td>HTTP_HOST</td>
				<td><?php echo $_SERVER['HTTP_HOST'] ?></td>
			</tr>
			<tr>
				<td>SERVER_ADDR</td>
				<td><?php echo $_SERVER['SERVER_ADDR'] ?></td>
			</tr>
			<tr>
				<td>SERVER_PORT</td>
				<td><?php echo $_SERVER['SERVER_PORT'] ?></td>
			</tr>
			<tr>
				<td>DOCUMENT_ROOT</td>
				<td><?php echo $_SERVER['DOCUMENT_ROOT'] ?></td>
			</tr>
			<tr>
				<td>SCRIPT_FILENAME</td>
				<td><?php echo $_SERVER['SCRIPT_FILENAME'] ?></td>
			</tr>
			<tr>
				<td>REQUEST_URI</td>
				<td><?php echo $_SERVER['REQUEST_URI'] ?></td>
			</tr>
			<tr>
				<td>SCRIPT_NAME</td>
				<td><?php echo $_SERVER['SCRIPT_NAME'] ?></td>
			</tr>
			<tr>
				<td>upload_max_filesize</td>
				<td><?php echo ini_get('upload_max_filesize') ?></td>
			</tr>
			<tr>
				<td>post_max_size</td>
				<td><?php echo ini_get('post_max_size') ?></td>
			</tr>
			<tr>
				<td>memory_limit</td>
				<td>
					<?php if (function_exists('memory_get_usage')): ?>
						<?php echo ini_get('memory_limit') ?>
					<?php else: ?>
						php memory limit not detected
					<?php endif ?>
				</td>
			</tr>
		</tbody>
	</table>

	<h3>WordPress Options</h3>
	<table border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th>Option Name</th>
				<th>Option Value</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Version</td>
				<td><?php echo bloginfo('version') ?></td>
			</tr>
			<tr>
				<td>blogname</td>
				<td><?php echo get_settings('blogname') ?></td>
			</tr>
			<tr>
				<td>siteurl</td>
				<td><?php echo get_settings('siteurl') ?></td>
			</tr>
			<tr>
				<td>home</td>
				<td><?php echo get_settings('home') ?></td>
			</tr>
			<tr>
				<td>upload_dir</td>
				<td><?php echo $upload_path ?></td>
			</tr>
		</tbody>
	</table>

	<h3>YAPB Globals</h3>
	<table border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th>Global Name</th>
				<th>Value</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>YAPB_EXECUTING_OS</td>
				<td><?php echo YAPB_EXECUTING_OS ?></td>
			</tr>
			<tr>
				<td>YAPB_SYSTEM_SEPARATOR</td>
				<td><?php echo YAPB_SYSTEM_SEPARATOR ?></td>
			</tr>
			<tr>
				<td>YAPB_WP_ROOT_DIR</td>
				<td><?php echo YAPB_WP_ROOT_DIR ?></td>
			</tr>
			<tr>
				<td>YAPB_PHPTHUMB_DIR</td>
				<td><?php echo YAPB_PHPTHUMB_DIR ?></td>
			</tr>
			<tr>
				<td>YAPB_PLUGINDIR_NAME</td>
				<td><?php echo YAPB_PLUGINDIR_NAME ?></td>
			</tr>
			<tr>
				<td>YAPB_CACHE_ROOT_DIR</td>
				<td><?php echo YAPB_CACHE_ROOT_DIR ?></td>
			</tr>
			<tr>
				<td>YAPB_TABLE_NAME</td>
				<td><?php echo YAPB_TABLE_NAME ?></td>
			</tr>
			<tr>
				<td>YAPB_PLUGIN_PATH</td>
				<td><?php echo YAPB_PLUGIN_PATH ?></td>
			</tr>
			<tr>
				<td>YAPB_TPL_PATH</td>
				<td><?php echo YAPB_TPL_PATH ?></td>
			</tr>
		</tbody>
	</table>

</body>
</htmL>