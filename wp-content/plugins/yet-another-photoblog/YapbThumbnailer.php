<?php

	/*	Script YapbThumbnailer
		Description: This script generates thumbnails on demand.

		It uses phpThumb directly and bypasses the caching overhead of
		the default phpThumb.php script. Since caching is now in the hand of YAPB,
		you won't see direct links to phpThumb.php. Your blog performance will
		increase drastically after the initial thumbnail generation.
		
		Thumbnails doesn't get generated in the main wp-php-thread since 
		we can get severe timeouts if a lot of images get requested concurrently.
		Instead we call this script every time a new thumbnail is needed.

		For security reasons, we only allow thumbnail generation of images
		already stored in the database. To generate a new Thumbnail, call this
		script with src=YAPBIMAGE_ID + all wanted image manipulation parameters

	*/

	/*  Copyright 2006 J.P.Jarolim (email : yapb@johannes.jarolim.com)

		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation; either version 2 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	*/

	require_once realpath(dirname(__file__) . '/includes/YapbConstants.script.php');
	require_once realpath(dirname(__file__) . '/lib/YapbImage.class.php');
	require_once realpath(dirname(__file__) . '/lib/YapbLogger.class.php');

	$log = new YapbLogger();

	// First of all, let's check the given post_id and
	// get the image if possible

	if (isset($_GET['post_id'])) {

		$post_id = $_GET['post_id'];
		$log->debug('post_id=' . $post_id);

		if (($image = YapbImage::getInstanceFromDb($post_id)) != null) {

			$log->debug('Found YAPB image');

			// These are the parameters that can be used through this script
			// I do define this parameters here since i want full control
			// over all parameters given to the YapbImage->transform method

			$parameters = array();
			$directMappings = array(
				'w','h','wp','hp','wl','hl','ws','hs','f', 'q',
				'sx','sy','sw','sh','zc','bc','bg','fltr', 'err',
				'xto','ra','ar','sfn','aoe','far','iar','maxb'
			);

			for ($i=0, $len=count($directMappings); $i<$len; $i++) {

				$key = $directMappings[$i];
				if (isset($_GET[$key])) {

					$value = $_GET[$key];

					// Let's cache this parameter
					// We need it later on

					if ($key == 'fltr') {
						// fltr is the only overloaded parameter
						// we append every single one
						for ($j=0, $jlen=count($value); $j<$jlen; $j++) {
							$log->debug('fltr[]=' . $value[$j]);
							array_push($parameters, 'fltr[]=' . $value[$j]);
						}
						
					} else {
						
						$log->debug($key . '=' . $value);
						array_push($parameters, $key . '=' . $value);
						
					}

					

				}

			}

			// This is the main part of this script:
			// The call to the YapbImage->transform method

			$success = $image->transform($parameters, $log);
			
		} else {

			$log->warn('no image found for post_id ' . $post_id);

		}

	} else {

		$log->warn('Give me at last a post_id parameter, man!');

	}

	// Finally: Success dependant output

	if ($success == false) {
	
		// No success: Output of the errorLog cached by 
		// the YapbLogger
		
		echo '<h1>Thumbnail generation unsuccessful</h1>';
		echo '<ul><li>' . $log->toString('</li><li>') . '</li></ul>';


	} else {

		// Success: We forward to the thumbnail
		header('Location: ' . $success);

	}

?>