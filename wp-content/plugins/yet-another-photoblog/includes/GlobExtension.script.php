<?php

	/** 
	 * This is a replacement for glob on servers that are running a php version < 4.3
	 * found on php.net / code changed to mirror the glob function standard behaviour
	 * Thanks to x_terminat_or_3 at yahoo dot country:fr for providing this code snippet
	 **/

	if(!function_exists('glob')) {

		function glob($pattern) {
			
			// get pathname (everything up until the last / or \)
			$path = $output = null;
			
			if (PHP_OS == 'WIN32') $slash = '\\';
			else $slash = '/';

			$lastpos = strrpos($pattern, $slash);

			if (!($lastpos === false)) {
				$path = substr($pattern, 0, $lastpos); // negative length means take from the right
				$pattern = substr($pattern, $lastpos+1);
			} else {
				// no dir info, use current dir
				$path = getcwd();
			}
			
			$handle = @opendir($path);
			if ($handle === false) return false;
			while ($dir = readdir($handle)) {
				if (pattern_match($pattern, $dir)) $output[] = $path . $slash . $dir;
			}
			closedir($handle);

			if (is_array($output)) return $output;
			return false;
		
		}

	}

?>