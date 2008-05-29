<?php

	/**
	 * To be used in the loop only
	 * 
	 * Function returns if this post is a photoblog post
	 * You may use this function to generate conditional
	 * output wheter this is a photoblog post or not
	 **/
	function yapb_is_photoblog_post() {
		global $post;

		// If YAPB wasn't able to attach the image in the first place
		// TODO: Convert to this and remove the automatic attachment via hook

		if (!$post->image) {
			if (!is_null($image = YapbImage::getInstanceFromDb($post->ID))) {
				$post->image = $image;
			}
		}

		if ($post->image) return true;
		else return false;
		
	}

	/**
	 * To be used in the loop only
	 * 
	 * Function returns an image tag showing the originally uploaded image according to the given parameters
	 * Sample usage:
	 *
	 *     echo yapb_get_image(
	 *         '<div>', 
	 *         array(
	 *             'alt' => 'My marvelous image',
	 *             'class' => 'marvelous_css_class',
	 *             'id' => 'some_marvelous_id',
	 *             'border' => '0'
	 *         ),
	 *         '</div>'
	 *     );
	 * 
	 * @param string $before HTML to be rendered before the thumbnail
	 * @param string $parameters a assoziative array of parameters that should get included in the image
	 * @param string $after HTML to be rendered after the thumbnail
	 */
	function yapb_get_image($before, $parameters = array('alt' => ''), $after) {

		global $post;
		$result = '';
		
		if (yapb_is_photoblog_post()) {

			$parameters = YapbImage::prepareInnerHtmlParameters($parameters);
			
			// now we insert/overwrite our parameters

			$parameters['src'] = $post->image->getFullHref();
			$parameters['width'] = $post->image->width;
			$parameters['height'] = $post->image->height;

			// now we build the whole html to be returned

			$result = $before . '<img';

			foreach ($parameters as $key => $value) {
				$result .= ' ' . $key . '="' . $value . '"';
			}
			
			$result .= ' />' . $after;
		}

		return $result;

	}

	/**
	 * To be used in the loop only
	 * 
	 * Function prints an image tag showing the originally uploaded image according to the given parameters
	 * Sample usage:
	 *
	 *     yapb_image(
	 *         '<div>', 
	 *         array(
	 *             'alt' => 'My marvelous image',
	 *             'class' => 'marvelous_css_class',
	 *             'id' => 'some_marvelous_id',
	 *             'border' => '0'
	 *         ),
	 *         '</div>'
	 *     );
	 * 
	 * @param string $before HTML to be rendered before the thumbnail
	 * @param string $parameters a assoziative array of parameters that should get included in the image
	 * @param string $after HTML to be rendered after the thumbnail
	 */
	function yapb_image($before, $parameters = array('alt' => ''), $after) {
		echo yapb_get_image($before, $parameters, $after);
	}

	/**
	 * To be used in the loop only
	 * 
	 * Function returns an thumbnail image tag according to the given parameters
	 * Sample usage:
	 *
	 *     echo yapb_get_thumbnail(
	 *         '<div>', 
	 *         array(
	 *             'alt' => 'My marvelous image',
	 *             'class' => 'marvelous_css_class',
	 *             'id' => 'some_marvelous_id',
	 *             'border' => '0'
	 *         ),
	 *         '</div>',
	 *         array(
	 *             'w=250',
	 *             'q=90'
	 *         ),
	 *         'myClass'
	 *     );
	 * 
	 * @param string $before HTML to be rendered before the thumbnail
	 * @param string $parameters a assoziative array of parameters that should get included in the image
	 * @param string $after HTML to be rendered after the thumbnail
	 * @param array $phpThumbConfiguration The phpThumb configuration
	 * @param string $cssClass [optional];provide an additional CSS Classname
	 */
	function yapb_get_thumbnail($before, $parameters = array('alt' => ''), $after, $phpThumbConfiguration = array('w=200','q=90'), $cssClassname='') {

		global $post;
		$result = '';
		
		if (yapb_is_photoblog_post()) {

			$parameters = YapbImage::prepareInnerHtmlParameters($parameters);
			
			// now we insert/overwrite our parameters

			$parameters['src'] = $post->image->getThumbnailHref($phpThumbConfiguration);
			$parameters['width'] = $post->image->getThumbnailWidth($phpThumbConfiguration);
			$parameters['height'] = $post->image->getThumbnailHeight($phpThumbConfiguration);

			// If there was an additional CSS Classname given:
			// Thanks to Jorge Otero for the bug report

			if ($cssClassname != '') {

				if (array_key_exists('class', $parameters)) {

					// We already have this attribute
					$parameters['class'] .= ' ' . $cssClassname;

				} else {

					// We create the attibute
					$parameters['class'] = $cssClassname;

				}

			}


			// now we build the whole html to be returned

			$result = $before . '<img';

			foreach ($parameters as $key => $value) {
				$result .= ' ' . $key . '="' . $value . '"';
			}
			
			$result .= ' />' . $after;
			
		}
		
		// Oh yeah baby, yeah: The officially first hook for external plugins ;-)

		return apply_filters('yapb_get_thumbnail', $result);

	}
	
	/**
	 * To be used in the loop only
	 * 
	 * Function prints an thumbnail image tag according to the given parameters
	 * Sample Usage: Accordingly to yapb_get_thumbnail(...) just without the echo in front
	 * 
	 * @param string $before HTML to be rendered before the thumbnail
	 * @param array $parameters a assoziative array of parameters that should get included in the image
	 * @param string $after HTML to be rendered after the thumbnail
	 * @param array $phpThumbConfiguration The phpThumb configuration
	 * @param string $cssClassname optional;provide an additional CSS Classname
	 */
	function yapb_thumbnail($before, $parameters = array('alt' => ''), $after, $phpThumbConfiguration = array('w=200','q=90'), $cssClassname='') {
		echo yapb_get_thumbnail($before, $altText, $after, $phpThumbConfiguration, $cssClassname);
	}
	
	/**
	 * To be used in the loop only
	 * 
	 * functions returns a list of the exif tokens if available
	 *
	 * @param boolean $flagUnfiltered No EXIF-tag filtering if true - Return all EXIF tokens
	 * @return assoziative array containing all (filtered) EXIF tokens
	 **/
	function yapb_get_exif($flagUnfiltered=false) {

		global $post;
		$result = array();

		// If YAPB wasn't able to attach the image in the first place
		// TODO: Convert to this and remove the automatic attachment via hook

		if (yapb_is_photoblog_post()) {
			$exif = ExifUtils::getExifData($post->image, $flagUnfiltered);
			if (!empty($exif)) {
				foreach ($exif as $key => $value) {
					$result[$key] = $value;
				}
			}
		}

		// And again: A nice little filter hook
		return apply_filters('yapb_get_exif', $result);

	}

	/**
	 * To be used in the loop only
	 * 
	 * functions prints a li-list of the exif tokens if available
	 *
	 * @param string $liClass CSS class of the li tags 
	 * @param string $keyValueSeparator HTML between EXIF key and EXIF value
	 * @param string $htmlBeforeKey HTML to be rendered before the EXIF key
	 * @param string $htmlAfterKey HTML to be rendered after the EXIF key
	 * @param string $htmlBeforeValue HTML to be rendered before the EXIF value
	 * @param string $htmlAfterValue HTML to be rendered after the EXIF value
	 * @param boolean $flagUnfiltered No EXIF-tag filtering if true - Return all EXIF tokens
	 **/
	function yapb_exif($liClass='', $keyValueSeparator=':', $htmlBeforeKey='', $htmlAfterKey='', $htmlBeforeValue='', $htmlAfterValue='', $flagUnfiltered=false) {
		$exif = yapb_get_exif($flagUnfiltered);
		foreach ($exif as $key => $value) {
			echo '<li' . (empty($liClass) ? '' : ' class="' . $liClass . '"') . '>' .
				$htmlBeforeKey . $key . $htmlAfterKey .
				$keyValueSeparator .
				$htmlBeforeValue . $value . $htmlAfterValue .
				'</li>' . "\n";
		}
	}

	/**
	 * To be used in the loop only
	 *
	 * Returns if the current posts image returns exif data
	 * @return boolean
	 */
	function yapb_has_exif($flagUnfiltered=false) {
		$exif = yapb_get_exif($flagUnfiltered);
		if (count($exif) > 0) return true;
		else return false;
	}

	
	/**
	 * To be used in the loop only
	 * 
	 * Returns a listitems with links to alternatively provided image sizes
	 * It will only return image sizes lower or equal that the original uploaded
	 * file. The array availableSizes contains should contain a list of all
	 * sizes to be provided: The number gets mapped to the longer side of the image.
	 *
	 * @param array $availableSizes An array containing all max. sizes to be made available
	 */
	function yapb_get_alternative_image_formats($limits = array(1600, 1024, 800, 640, 320)) {

		global $post;
		$result = '';

		if (yapb_is_photoblog_post()) {

			$width = $post->image->width;
			$height = $post->image->height;

			for ($i=0, $len=count($limits); $i<$len; $i++) {
				
				$limit = $limits[$i];
				$targetWidth = null;
				$targetHeight = null;

				if ($width >= $height) {

					// Landscape

					if ($width >= $limit) {
						$targetWidth = $limit;
						$targetHeight = round($limit * $height / $width);
					}

				} else {

					// Portrait

					if ($height >= $limit) {
						$targetHeight = $limit;
						$targetWidth = round($width * $limit / $height);
					}

				}

				if (!is_null($targetWidth) && !is_null($targetHeight)) {

					if (($targetWidth == $width) || ($targetHeight == $height)) {

						$result .= '<li class="' . $liClass . '"><a href="' . $post->image->getFullHref() . '" target="_blank">' . $targetWidth . 'x' . $targetHeight . '</a></li>' . "\n";

					} else {

						$thumbnailConfig = array(
							'w=' . $targetWidth,
							'q=90',
							'fltr[]=usm|60|0.5|3'
						);
						$result .= '<li class="' . $liClass . '"><a href="' . $post->image->getThumbnailHref($thumbnailConfig) . '" target="_blank">' . $targetWidth . 'x' . $targetHeight . '</a></li>' . "\n";

					}

				}

			}

		}

		return apply_filters('yapb_alternative_image_formats', $result);

	}

	/**
	 * To be used in the loop only
	 * 
	 * Returns a listitems with links to alternatively provided image sizes
	 * It will only return image sizes lower or equal that the original uploaded
	 * file. The array availableSizes contains should contain a list of all
	 * sizes to be provided: The number gets mapped to the longer side of the image.
	 *
	 * @param array $availableSizes An array containing all max. sizes to e made available
	 */
	function yapb_alternative_image_formats($limits = array(1600, 1024, 800, 640, 320), $liClass='yapb_alternative_format') {
		echo yapb_get_alternative_image_formats($limits, $liClass);
	}

?>