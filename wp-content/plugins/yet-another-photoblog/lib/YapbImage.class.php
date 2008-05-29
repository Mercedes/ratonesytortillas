<?php

	/*	Class YapbImage
		Description: This class bundles all image functionality
		needed by YAPB like database persistance and thumbnailing.
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

	class YapbImage {

		// Database fields 

		/**
		 * The id of the image
		 * @var number id
		 */		
		var $id = null;
		
		/**
		 * The id of the post this image is belonging to 
		 * @var number
		 */
		var $post_id = null;
		
		/**
		 * The internal URI of the image
		 * @var string
		 */
		var $uri = '';
		
		/**
		 * The width of the original image
		 *
		 * @var number >= 0
		 */
		var $width = 0;
		
		/**
		 * The height of the original image
		 *
		 * @var number >=0
		 */
		var $height = 0;
		
		//
		// private vars: Don't use them from outside
		// 

		/**
		 * Internal thumb info cache
		 * 
		 * @var array
		 */
		var $_thumbInfoCache = array();

		/**
		 * Constructor
		 *
		 * @param number $id the id of the image (null if this is a new instance not persisted to db yet)
		 * @param number $post_id the id of the post this image is belonging to
		 * @param string $uri the URI of the image 
		 * @return YapbImage
		 */
		function YapbImage($id=null, $post_id, $uri) {
			$this->id = $id;
			$this->post_id = $post_id;
			$this->uri = $uri;
			$this->_fetchImagesize();
		}

		/**
		 * This method should be called statically
		 * returns an YapbImage Object filled with the 
		 * fields of the DB row result
		 * 
		 * @param ezSQL_row_object $rowObject
		 * @return YapbImage
		 **/
		function getInstanceFromDbRow($rowObject) {
			return new YapbImage($rowObject->id, $rowObject->post_id, $rowObject->URI);
		}

		/**
		 * This method should be called statically
		 * It returns the image assigned
		 * to the given post_id
		 * 
		 * @param number $post_id
		 * @return YapbImage
		 **/
		function getInstanceFromDb($post_id = null) {

			if (($post_id != null) && ($post_id != '')) {

				global $wpdb;

				if (!is_null($image = $wpdb->get_row('SELECT * FROM ' . YAPB_TABLE_NAME . ' WHERE post_id = ' . $post_id))) {
					return YapbImage::getInstanceFromDbRow($image);
				} else {
					return null;
				}

			} else {

				return null;
			
			}

		}

		/**
		 * This method returns the absolute file system path to this YapbImage instance
		 * You may provide an URI and you get the absolute path of this URI instead
		 *
		 * @param string $uri provide null to get the URI of this YapbImage instance
		 * @return string the absolute file system path to the requested URI/YapbImage
		 */
		function systemFilePath($uri=null) {
			if (is_null($uri)) $uri = $this->uri;
			return YAPB_WP_ROOT_DIR . substr($uri, strpos($uri, '/wp-content'));
		}

		/**
		 * This method returns the HREF to the originally uploaded image
		 * 
		 * @return string the href to the original image
		 **/
		function getFullHref() {
			$siteUrl = get_option('siteurl');
			$serverURL = preg_replace('#(http://[^/]+).*#', '$1', $siteUrl);
			return $serverURL . $this->uri;
		}

		/**
		 * This method returns the HREF to a thumbnail defined through 
		 * the given parameters array
		 *
		 * @param array $parameters provide the phpThumb parameters
		 * @param boolean $xhtmlOverride override the users xhtml setting manually (needed for feeds for example)
		 * @return string the href to the thumbnail
		 */
		function getThumbnailHref($parameters=array(), $xhtmlOverride=true) {

			// Get if the user needs xhtml code
			$xhtml = get_option('yapb_display_images_xhtml');

			if (file_exists($this->_getUniqueThumbnailPath($parameters))) {

				// The thumbnail already exists: We return the direct path to the image
				// For the attentive reader: This is the "performance boost" part ;-)
				return $this->_getUniqueThumbnailHref($parameters);

			} else {

				// If the thumbnail doesn't exist yet, we return the path to the YapbThumbnailer script
				$ampersand = (($xhtml && $xhtmlOverride) ? '&amp;' : '&');
				return YAPB_PLUGIN_PATH . 'YapbThumbnailer.php?post_id=' . $this->post_id . $ampersand . implode($ampersand, $parameters);

			}

		}

				/**
				 * This method unifies the list of given parameters
				 * so we have a standardized format
				 * 
				 * @param array $parameters
				 * @return array an unified list of parameters 
				 **/
				function _getUnifiedParameters(&$parameters) {
					
					$result = array();

					// This is the list of allowed phpThumb parameters
					$directMappings = array(
						'w','h','wp','hp','wl','hl','ws','hs','f', 'q',
						'sx','sy','sw','sh','zc','bc','bg','fltr[]', 'err',
						'xto','ra','ar','sfn','aoe','far','iar','maxb'
					);

					$temp = array();
					$filters = array();
					
					for ($i=0, $len=count($parameters); $i<$len; $i++) {
						preg_match('/([^=]+)=(.*)/', $parameters[$i], $match);
						$key = $match[1];
						$value = $match[2];
						if (in_array($key, $directMappings)) {
							if ($key == 'fltr[]') {
								array_push($filters, $value);
							} else {
								$temp[$key] = $value;
							}
						}
					}

					foreach ($temp as $key => $value) {
						array_push($result, $key.'='.$value);
					}
					for ($i=0, $len=count($filters); $i<$len; $i++) {
						array_push($result, 'fltr[]=' . $filters[$i]);
					}

					sort($result, SORT_STRING);
					return $result;

				}

				/**
				 * This method generates the system-wide unique (as unique
				 * as md5 hashes can be) identifier for a general thumbnail.
				 * This identifier shouldn't include any filetype suffix.
				 * 
				 * JP 01012008: 
				 * The identifier now starts with the original filename (minus the suffix)
				 * Special chars get replaced by an underscore
				 * 
				 * Notice: If we would want to change the general name-scheme, we 
				 * would do perform changes here.
				 * 
				 * @param array $parameters
				 * @return string the unique thumbnail identifier
				 **/
				function _getUniqueThumbnailIdentifier(&$parameters) {
					
					$unifiedParameters = $this->_getUnifiedParameters($parameters);

					if (count($parameters) == 0); // $log->warn('no given parameters');
					if (count($unifiedParameters) == 0); // $log->warn('no unified parameters');
					
					$debugTokens = array();
					foreach ($unifiedParameters as $key => $value) {
						$debugTokens[] = $key . ' = ' . $value;
					}
					
					// SEO: We start thumbnail filenames with the original filename
					// minus the filetype suffix
					
					$basename = basename($this->uri);
					$basename = substr($basename, 0, strrpos($basename, '.'));
					
					// Additionial: Original filenames may be very odd names
					// containing spaces and other special chars
					// That's why i replace all chars i don't like
					
					$basename = strtolower(
						preg_replace(
							'/([^A-Za-z0-9_()])/', 
							'_', 
							$basename
						)
					);
					
					// We return the beautiful SEO thumbnail identifier
					// consisting of the original filename followed by
					// 2 md5 hashes. Those two hashes are:
					// - a hash of the uri so we don't conflict with 
					//   identical filenames in different upload directories
					// - a hash of the thumbnail parameters so we 
					//   don't conflict with different thumbnails of the same image
					// Both base 16 hashes get converted to base 36 (10 digits + 26 alphanumeric)
					// so the filename gets a little bit shorter
					
					return $basename . '.' . base_convert(md5($this->uri), 16, 36) . '.' . base_convert(md5(implode('', $unifiedParameters)), 16, 36) . '.th';
					
				}

				/**
				 * This method returns an unique thumbnail filename (incl. file sufix)
				 * 
				 * @param array $parameters
				 * @return string the unique thumbnail filename 
				 **/
				function _getUniqueThumbnailName(&$parameters) {
					$extension = get_settings('yapb_phpthumb_output_format');
					return $this->_getUniqueThumbnailIdentifier($parameters) . '.' . $extension;
				}

				/**
				 * This method returns the absolute system path to the 
				 * unique thumbnail defined through the
				 * given phpThumb parameters
				 * 
				 * @param array $parameters
				 * @return string the absolute system path to the thumbnail
				 **/
				function _getUniqueThumbnailPath(&$parameters) {
					return YAPB_CACHE_ROOT_DIR . $this->_getUniqueThumbnailName($parameters);
				}

				/**
				 * This method returns the HREF to the
				 * unique thumbnail defined throught the
				 * given phpThumb parameters
				 * 
				 * @param array $parameters
				 * @return string the href to the thumbnail
				 **/
				function _getUniqueThumbnailHref(&$parameters) {
					return YAPB_PLUGIN_PATH . 'cache/' . $this->_getUniqueThumbnailName($parameters);
				}


		//
		//	The following methods allow to get the thumbnails
		//	width and height even if it wasn't generated yet.
		//	In this case, YAPB roughly estimates the dimensions
		//	that may be awaited: You will get those values only
		//	on the first call to a thumbnail. After that you will
		//	get the actual dimensions out of the generated cachefile.
		//

		/**
		 * This method returns the width of the thumbnail
		 * defined through the given phpThumb parameters
		 * 
		 * @param array $parameters 
		 * @return number >= 0
		 */
		function getThumbnailWidth($parameters = array()) {
			$dimension = $this->_getThumbnailDimensions($parameters);
			return $dimension['width'];
		}

		/**
		 * This method returns the height of the thumbnail
		 * defined through the given phpThumb parameters
		 *
		 * @param array $parameters
		 * @return number >= 0
		 */
		function getThumbnailHeight($parameters = array()) {
			$dimension = $this->_getThumbnailDimensions($parameters);
			return $dimension['height'];
		}


				/**
				 * Internal method fetches thumbnail dimensions either from previously
				 * generated cache file or let phpThumb calculate the dimensions with the 
				 * help of the given parameters
				 * 
				 * @param array $parameters
				 * @return assoziative_array containing keys [width, height] mapped to numbers
				 **/
				function _getThumbnailDimensions($parameters=array()) {

					$result = array();

					// First of all some minor performance tweaking
					// with a little help of an internal info cache

					$cacheKey = $this->_getUniqueThumbnailIdentifier($parameters);
					if (isset($this->_thumbInfoCache[$cacheKey])) {

						// We have a cachehit
						// That means we calculated the dimensions
						// before and just redeliver the result
						$result = $this->_thumbInfoCache[$cacheKey];

					} else {

						// No cachehit - Let's do some fetching or calculation
						// to get that thumbnail dimensions

						if (file_exists($this->_getUniqueThumbnailPath($parameters))) {

							// Hehe, we have a file - Let's get the dimensions directly
							$size = getimagesize($this->_getUniqueThumbnailPath($parameters));
							$result['width'] = $size[0];
							$result['height'] = $size[1];

						} else {

							// Hmmm. no file generated yet. We have to do an calculation
							// based on the given parameters

							// Since i don't want to reverse engeneer phpThumb
							// (and i hope that future versions won't change that much)
							// i'll use internal methods of the phpThumb class for that

							require_once realpath(dirname(__file__) . '/' . YAPB_PHPTHUMB_DIR . '/phpthumb.class.php');
							$phpThumb = new phpthumb();

							// First fetch and parse the parameters we need,
							// and give them to the phpThumb instance

							$parametersWeSearchFor = array('w', 'h', 'wp', 'hp', 'wl', 'hl', 'ws', 'hs', 'zc');
							for ($i=0, $len=count($parameters); $i<$len; $i++) {
								preg_match('/([^=]+)=(.*)/', $parameters[$i], $matches);
								$key = $matches[1];
								$value = $matches[2];
								if (in_array($key, $parametersWeSearchFor)) {
									$phpThumb->$key = $value;
								}
							}

							// For the calculation phpThumb needs the size of the original image
							$phpThumb->source_width = $this->width;
							$phpThumb->source_height = $this->height;

							// Now let phpThumb calculate the size
							$phpThumb->CalculateThumbnailDimensions();

							// TODO: fetch the desired dimensions and return them
							$result['width'] = $phpThumb->thumbnail_width;
							$result['height'] = $phpThumb->thumbnail_height;

						}

					}

					return $result;

				}

				/**
				 * This method fetches the image size of an image
				 * file already placed on the disk
				 */
				function _fetchImagesize() {
					$size = getimagesize($this->systemFilePath());
					$this->width = $size[0];
					$this->height = $size[1];
				}


		/**
		 * This method persists the image data to the database
		 * If no id was given before, an insert will be preformed
		 * Else this method does an update
		 **/
		function persist() {

			global $wpdb;

			// If this image hasn't got an id yet
			if (is_null($this->id)) {
				// Image not persisted yet - We do an insert
				$wpdb->query('INSERT INTO ' . YAPB_TABLE_NAME . ' (post_id, uri) values (' . $wpdb->escape($this->post_id) . ', \'' . $wpdb->escape($this->uri) . '\')');
				// Additionally we want the primary id in case we need it afterwards
				$this->id = $wpdb->get_var('SELECT LAST_INSERT_ID() as id');
			} else {
				// We have this image already - We do an update
				$wpdb->query('UPDATE ' . YAPB_TABLE_NAME . ' uri=\'' . $wpdb->escape($this->uri) . '\' WHERE id = ' . $this->id);
			}

		}

		/**
		 * This method deletes this image inclusively thumbnails from db and hd
		 * You won't want to use this YapbImage instance after calling
		 * this method - there's no real data representing this instance any more.
		 **/
		function delete() {

			global $wpdb;

			// Require glob support for older php versions (version < 4.3)

			require_once realpath(dirname(__file__) . '/../includes/GlobExtension.script.php');

			// we want to erase all traces of this previously uploaded image:
			// first, we delete all generated thumbnails

			$emptyArray = array('');
			$exampleThumb = $this->_getUniqueThumbnailName($emptyArray);
			$thumbGlob = preg_replace('/(.*\..*\.).*(\.th\..*)/','$1*$2',$exampleThumb);
			$globPattern = YAPB_CACHE_ROOT_DIR . $thumbGlob;

			$allThumbnails = glob($globPattern);
			for ($i=0, $len=count($allThumbnails); $i<$len; $i++) {
				unlink($allThumbnails[$i]);
			}

			// now we delete the original image

			unlink($this->systemFilePath());

			// at last we clean up the database entries belonging to that image

			$wpdb->query('DELETE FROM ' . YAPB_TABLE_NAME . ' where id = ' . $this->id);

		}


		/**
		 * This method generates a transformed image via phpThumb
		 * Please be aware that this method needs it's time - So try to
		 * avoid multiple usages in one thread - That could cause timeouts.
		 * 
		 * @param array $phpThumbConfig provide the array defining the phpThumb operations
		 * @param YapbLogger $log provide a logging instance for internal logging
		 * @param boolean $replaceOriginal original image will be replaced with transformed one if set to true
		 * @return the URI to the new image or false in case of an error
		 **/
		function transform($phpThumbConfig, &$log, $replaceOriginal=false) {

			require_once realpath(dirname(__file__) . '/' . YAPB_PHPTHUMB_DIR . '/phpthumb.class.php');
			
			$result = false;

			// For this operation, we define an internal log 
			// Additionally, we define a little log appender and
			// a little log output function
			$phpthumb = new phpthumb();
			
			// Set the source filename
			$phpthumb->setSourceFilename($this->uri);							
			
			// Wild hack taken from phpThumb.config.php.default:
			// If we have a wrong DOCUMENT_ROOT setting, we try to
			// build it on our own. Should work on most configurations,
			// wouldn't be necessary on correct configured hosts

			$phpthumb->config_document_root = realpath((@$_SERVER['DOCUMENT_ROOT'] && file_exists(@$_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF'])) ? $_SERVER['DOCUMENT_ROOT'] : str_replace(dirname(@$_SERVER['PHP_SELF']), '', str_replace(DIRECTORY_SEPARATOR, '/', realpath('.'))));			
			
			// Get some phpThumb configurations

			$phpthumb->config_output_format = 
				(is_null(get_settings('yapb_phpthumb_output_format')) ||
				get_settings('yapb_phpthumb_output_format') == '')
					? null
					: get_settings('yapb_phpthumb_output_format');
			$phpthumb->config_output_interlace = 
				is_null(get_settings('yapb_phpthumb_output_interlace')) || 
				(get_settings('yapb_phpthumb_output_interlace') == '');
			$phpthumb->config_imagemagick_path = 
				(is_null(get_settings('yapb_phpthumb_imagemagick_path')) ||
				get_settings('yapb_phpthumb_imagemagick_path') == '')
					? null
					: get_settings('yapb_phpthumb_imagemagick_path');

			for ($i=0, $len=count($phpThumbConfig); $i<$len; $i++) {

				$token = $phpThumbConfig[$i];
				$explodedToken = explode('=', $token);

				$key = $explodedToken[0];
				$value = $explodedToken[1];

				// Direct mapping of the value to the 
				// according attribute of the phpThumb instance
				// This is potentially very unsafe and shouldn't
				// be made available directly to public 

				if (strpos($key, '[]') == false) {
					$phpthumb->$key = $value;
				} else {
					$keyName = substr($key, 0, -2);
					array_push($phpthumb->$keyName, $value);
				}
					

			}
			
			// Now let's have a look at the destination path

			if ($replaceOriginal) {

				// Replace the original
				$thumbPath = $this->systemFilePath();

			} else {

				// Generate a new cached thumbnail
				$thumbPath = $this->_getUniqueThumbnailPath($phpThumbConfig);

			}

			if ($phpthumb->GenerateThumbnail()) {
				
				if ($phpthumb->RenderToFile($thumbPath)) {

					if ($replaceOriginal) {
						$result = $this->getFullHref();
					} else {
						$result = $this->_getUniqueThumbnailHref($phpThumbConfig);
					}

				} else {

					$log->error('Couldn\'t save ' . $thumbPath . ' - Please check cache directory permissions');

				}

			} else {

				if (!is_null($log)) $log->error('phpThumb couldn\'t generate thumbnail.');

				// OK - It wasn't possible to generate the thumbnail
				// Let's have an indepth look into the phpThumb debug messages

				$log->info('Writing out phpThumb internal debug messages:');
				foreach ($phpthumb->debugmessages as $message) {
					$log->debug('phpThumb: ' . $message);
				}

			}

			return $result;

		}

		/**
		 * Method preparing parameters of the html tag
		 * Additionally it creates a default fill if given an empty parameter
		 *
		 * @static
		 * @param array $parameters
		 * @return array
		 */
		function prepareInnerHtmlParameters($parameters) {

			$result = array();
			
			if (empty($parameters)) {
			
				// If no parameters where given, i define
				// a basic set of them

				$result = array(
					'alt' => get_the_title(),
					'border' => '0',
					'class' => 'yapb_thumbnail'
				);

			} else {

				// We have some parameters - Let's bring them to
				// a normalized lowercase form

				foreach ($parameters as $key => $value) {
					$result[strtolower($key)] = $value;
				}
				
			}

			return $result;

		}

	}

?>