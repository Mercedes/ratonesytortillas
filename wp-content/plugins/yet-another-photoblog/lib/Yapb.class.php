<?php

	/*  Copyright 2007 J.P.Jarolim (email : yapb@johannes.jarolim.com)

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

	class Yapb {

		/**
		 * Current plugin version
		 * @var string
		 **/
		var $pluginVersion;

		/**
		 * Lowest required WordPress Version
		 * @var string
		 **/
		var $requiredWordPressVersion;

		/**
		 * Highest WordPress Version this plugin version was tested with
		 * @var string
		 **/
		var $highestTestedWordPressVersion;

		/**
		 * The templating engine instance
		 * @var Savant2
		 */
		var $tpl = null;
		
		/**
		 * @var string
		 */
		var $base_url = '';
		
		/**
		 * The directory separator of this os
		 * @var string
		 */
		var $separator;

		/**
		 * The array holding all configuration settings and defaults for YAPB
		 * @var array
		 */
		var $options;

		/**
		 * PHP4 Constructor
		 */
		function Yapb() {	
			$this->__construct(); 
		}

		/**
		 * PHP5 Constructor
		 */
		function __construct() {
		
			global $wpdb;

			// First of all, let's read some version information
			$this->readVersionInformation();

			// Now, let's define some globally available variables
			require_once realpath(dirname(__FILE__) . '/../includes/YapbConstants.script.php');

			// I18N support through GNU-Gettext files
			load_plugin_textdomain('yapb', 'wp-content/plugins/' . YAPB_PLUGINDIR_NAME . '/lang/');

			// Let's require some usefull stuff
			require_once realpath(dirname(__FILE__) . '/YapbImage.class.php');
			require_once realpath(dirname(__FILE__) . '/YapbUtils.class.php');
			require_once realpath(dirname(__FILE__) . '/ExifUtils.class.php');
			require_once realpath(dirname(__FILE__) . '/Savant2-2.4.3/Savant2.php');
			require_once realpath(dirname(__FILE__) . '/../includes/YapbTemplateFunctions.php');

			// Initialize the savant2 templating engine
			$this->tpl =& new Savant2();
			$this->tpl->addPath('template', YAPB_TPL_PATH);

			// Dashboard Activity Box
			add_action('activity_box_end', array(&$this, 'activity_box_end'));
			
			// wp-admin post.php hooks
			add_filter('edit_form_advanced', array(&$this, 'edit_form_advanced'));
			if (get_settings('yapb_form_on_page_form')) {
				add_filter('edit_page_form', array(&$this, 'edit_form_advanced'));
			}
			
			add_action('edit_post', array(&$this, 'edit_publish_save_post'));

			add_action('publish_post', array(&$this, 'edit_publish_save_post'));
			add_action('publish_post', array(&$this, 'publish_post'));
			add_action('save_post', array(&$this, 'edit_publish_save_post'));
			add_action('delete_post', array(&$this, 'delete_post'));
			
			// wp-admin edit.php hooks
			add_filter('manage_posts_columns', array(&$this, 'manage_posts_columns'));
			add_action('manage_posts_custom_column', array(&$this, 'manage_posts_custom_column'));
			add_filter('manage_pages_columns', array(&$this, 'manage_pages_columns'));
			add_action('manage_pages_custom_column', array(&$this, 'manage_pages_custom_column'));

			// the wp-loop
			add_filter('the_posts', array(&$this, 'the_posts'));

			// options
			add_action('admin_menu', array(&$this, 'add_options_panel'));

			// Activation, Deactivation

			register_activation_hook(YAPB_PLUGINDIR_NAME . '/Yapb.php', array(&$this, 'activate_pluginurl'));
			register_deactivation_hook(YAPB_PLUGINDIR_NAME . '/Yapb.php', array(&$this, 'deactivate_pluginurl'));

			// Feeds & automatic image insertion
			add_filter('the_content', array(@$this, 'the_content'));

			// YAPB Sidebar Widget Last x images
			require_once realpath(dirname(__file__) . '/../includes/YapbSidebarWidget.php');

			// YAPB Options Array
			require_once realpath(dirname(__file__) . '/../includes/YapbOptions.php');
			
		}

		/**
		 * This method sets the plugins version information with the 
		 * values fetched from the readme.txt file in the plugins root directory
		 **/
		function readVersionInformation() {

			// Get the readme.txt file contents
			$readmeContent = file_get_contents(realpath(dirname(__FILE__) . '/../readme.txt'));
			
			// Since we don't want to execute regular expressions against the whole file, we extract the header part
			$readmeHeader = substr($readmeContent, 0, strpos($readmeContent, '== Description =='));
			
			// Extract the defined version numbers
			$this->pluginVersion = $this->_parseReadmeValue($readmeHeader, 'Stable tag');
			$this->requiredWordPressVersion = $this->_parseReadmeValue($readmeHeader, 'Requires at least');
			$this->highestTestedWordPressVersion = $this->_parseReadmeValue($readmeHeader, 'Tested up to');
		
		}

			/** 
			 * Function extracts a value from the readme header
			 *
			 * @param string $readmeHeader
			 * @param string $key
			 * @return string
			 **/
			function _parseReadmeValue($readmeHeader, $key) {
				preg_match('#' . $key . '\s*:\s*([0-9.]+)#i', $readmeHeader, $match);
				return $match[1];
			}

		/**
		 * POST.PHP
		 **/

		/**
		 * post.php hook edit_form_advanced
		 * This method enhances the wp new/edit-form
		 * to an upload form
		 **/
		function edit_form_advanced() {
			
			global $post;

			// Let's have a look if this post has an image attached
			// If yes: Assign the image to the template
			if (isset($post->ID)) {
				if (!is_null($image = YapbImage::getInstanceFromDb($post->ID))) {
					$this->tpl->assign('image', $image);
				}
			}

			$this->tpl->assign('content', $this->tpl->fetch('edit_form_advanced_field_fileupload.tpl.php'));
			$this->tpl->display('edit_form_advanced_javascript_injection.tpl.php');

		}

		/**
		 * This internal method returns if the given mime type gets
		 * accepted by YAPB upload
		 * @param string $type 
		 */
		function _isAllowedMimeType($type) {
			return in_array($type, array('image/jpeg', 'image/jpg', 'image/png', 'image/gif'));
		}

		/**
		 * This method hooks into the default upload workflow and intercepts 
		 * eventually existing file uploads
		 * 
		 * hook edit_post
		 * hook publish_post
		 * hook save_post
		 * @param number $post_id
		 **/
		function edit_publish_save_post($post_id) {

			global $wpdb;

			// if we have an yapb-imageupload here
			if (array_key_exists('yapb_imageupload', $_FILES)) {

				// wp_handle_upload: admin_functions.php
				$uploadedFileInfo = wp_handle_upload($_FILES['yapb_imageupload'], array('action' => $_POST['action']));

				// if we didn't have errors while upload
				if (!isset($uploadedFileInfo['error'])) {

					$url = $uploadedFileInfo['url'];
					$type = $uploadedFileInfo['type'];

					// We want to save the relative URI seen from the webhost-root of the image
					// We now have an url like 
					// http://my.server.tld/blog/wp-content/uploads/.../bla.jpg or
					// http://my.server.tld/wp-content/uploads/.../bla.jpg
					// We want to save instead: 
					// /blog/wp-content/uploads/.../bla.jpg or respectivly
					// /wp-content/uploads/.../bla.jpg

					$siteUrl = get_option('siteurl');
					if (substr($siteUrl, -1) != '/') $siteUrl .= '/';
					$uri = substr($url, strpos($siteUrl, '/', strpos($url, '//')+2));

					if ($this->_isAllowedMimeType($type)) {

						// First we take a look if we already have an image attached to this post
						// In this case we delete it, because we replace images

						if ($post_id) {
							if (!is_null($image = YapbImage::getInstanceFromDb($post_id))) {
								$image->delete();
							}
						}

						$image = new YapbImage(null, $post_id, $uri);

						// We persist the image to the database
						$image->persist();

						// Since we want to learn from every single posted image
						// Use YapbImage for Exif-Extraction
						$yapbImage = new YapbImage($id, $post_id, $uri); 
						$exifData = ExifUtils::getExifData($yapbImage, true);
						ExifUtils::learnTagnames($exifData);

						// If the user wants the postdate to be overwritten by the exif datetime stamp
						if (array_key_exists('exifdate', $_POST)) {

							if (!is_null($exifData)) {
								if (array_key_exists('DateTime', $exifData)) {

									// The exif date is formated this way: yyyy:mm:dd hh:mm:ss
									// strtotime needs it this way: yyyy-mm-dd hh:mm:ss
									// so we change that with a little regex since sprintf
									// isn't available on every platform

									$datetime = preg_replace('#([0-9]{4}):([0-9]{2}):([0-9]{2})#', '$1-$2-$3', $exifData['DateTime']);
									$date = strtotime($datetime);
									$dateGMT = $date - (get_option('gmt_offset') * 60 * 60);
									
									// we update the post if the datetime parsing was successful

									if ($date != -1) {

										// now we update post_date and post_date_gmt
										$wpdb->query('UPDATE ' . $wpdb->posts . ' set post_date = \'' . strftime('%Y-%m-%d %H:%M:%S', $date) . '\', post_date_gmt = \'' . strftime('%Y-%m-%d %H:%M:%S', $dateGMT) . '\' WHERE ID = ' . $post_id);

									} 

								} 
							}
							
						}

						
						/*

						// TODOs: 
						// - EXIF migration from source to target
						// - Testing

						// Optional image resize on upload
						// Feature disabled

						if (get_option('yapb_resize_on_upload_activate')) {

							// Lightweight logger
							require_once realpath(dirname(__file__) . '/lib/YapbLogger.class.php');
							$log = new YapbLogger();

							$maxSideLength = get_option('yapb_resize_on_upload_max_dimension');
							$width = $yapbImage->width;
							$height = $yapbImage->height;

							$log = 

							if ($width > $maxSideLength) {

								if ($height > $width) $transformResult = $yapbImage->transform(array('h=' . $maxSideLength), $log);
								else $transformResult = $yapbImage->transform(array('w=' . $maxSideLength));
								$yapbImage->_fetchImagesize();

							} else 

							if ($height > $maxSideLength) {

								$transformResult = $yapbImage->transform(array('h=' . $maxSideLength), $log);
								$yapbImage->_fetchImagesize();

							}

						}

						// TODO: Give the user feedback if the transformation fails
						// = Have a look how to manipulate the wp-post-success-message over a hook
						// = Catch other errors and give feedback too

						*/

					} else {

						// This ain't an image - let's delete it right away
						unlink(realpath($uri));

					}

				} else {
					
					// Some error occured while uploading the file
					// TODO: Some kind of error message on the admin interface

				}

			}

			if (array_key_exists('yapb_remove_image', $_POST)) {
				if (!is_null($image = YapbImage::getInstanceFromDb($post_id))) {
					$image->delete();
				}
			}

		}

		/**
		 * This hook reacts on a publish and pings all additionally 
		 * defined sites IF an image was attached
		 * @param number $post_id
		 */
		function publish_post($post_id) {
			
			// Since we registered this action after edit_publish_save_post($post_id),
			// we should eventually have an image 
			if (!is_null($image = YapbImage::getInstanceFromDb($post_id))) {
				// If this is a photoblog post, we additionally ping all sites defined by the user
				$this->_generic_yapb_ping($post_id);
			}

		}

		/**
		 * hook delete_post (wp-admin/post.php)
		 * This method gets called every time we delete a post
		 * @param number $post_id
		 **/
		function delete_post($post_id) {
			if (!is_null($image = YapbImage::getInstanceFromDb($post_id))) {
				$image->delete();
			}
		}

		/**
		 * EDIT.PHP
		 **/

		/**
		 * hook manage_posts_columns
		 * i want to insert another column after the date column
		 * since this is an associative array i've to rebuild it
		 * @param array $post_columns
		 **/
		function manage_posts_columns($posts_columns) {
			$result = array();
			foreach ($posts_columns as $key => $value) {
				if ($key == 'date') {
					$result[$key] = $value;
					$result['thumb'] = __('Image', 'yapb');
				} else $result[$key] = $value;
			}
			return $result;
		}

		/**
		 * this filter acts on the previously inserted post column 'thumb'
		 * see method manage_posts_columns
		 * @param string $column_name
		 **/
		function manage_posts_custom_column($column_name) {
			if ($column_name == 'thumb') {
				global $post;
				if (!is_null($image = YapbImage::getInstanceFromDb($post->ID))) {
					$this->tpl->assign('image', $image);
				} else {
					$this->tpl->clear('image');
				}
				$this->tpl->display('manage_posts_custom_column.tpl.php');
			}
		}

		/**
		 * edit-pages.php
		 **/

		/**
		 * hook manage_pages_columns
		 * i want to insert another column after the date column
		 * since this is an associative array i've to rebuild it
		 * @param array $post_columns
		 **/
		function manage_pages_columns($posts_columns) {
			$result = array();
			foreach ($posts_columns as $key => $value) {
				if ($key == 'date') {
					$result[$key] = $value;
					$result['thumb'] = __('Image', 'yapb');
				} else $result[$key] = $value;
			}
			return $result;
		}

		/**
		 * this filter acts on the previously inserted post column 'thumb'
		 * see method manage_pages_columns
		 * @param string $column_name
		 **/
		function manage_pages_custom_column($column_name) {
			if ($column_name == 'thumb') {
				global $post;
				if (!is_null($image = YapbImage::getInstanceFromDb($post->ID))) {
					$this->tpl->assign('image', $image);
				} else {
					$this->tpl->clear('image');
				}
				$this->tpl->display('manage_posts_custom_column.tpl.php');
			}
		}

		//
		// The wordpress-loop
		// Goal: Insert an image-object into every post having attached one
		//

		/**
		 * filter for hook: the_posts
		 * We cycle through all posts, look for attached images
		 * and assign found images to the particular posts
		 * @param array $posts
		 */
		function the_posts(&$posts) {
			for ($i=0, $len=count($posts); $i<$len; $i++) {
				$post = &$posts[$i];
				if (!is_null($image = YapbImage::getInstanceFromDb($post->ID))) {
					$post->image = $image;
				}
			}
			return $posts;
		}

		/**
		 * Options page
		 * We insert an options page for yapb
		 **/
		function add_options_panel() {
			if (function_exists('add_options_page')) {
				// We need some libraries on this page
				wp_enqueue_script('prototype');
				wp_enqueue_script('dbx');
				add_options_page('YAPB', 'YAPB', 8, basename(__FILE__), array(&$this, 'render_options_panel_content'));
		    }
		}

		function render_options_panel_content() {
			$this->tpl->assign('options', $this->options);
			$this->tpl->assign('yapbVersion', $this->pluginVersion);
			$this->tpl->display('admin_panel_yapb_options_page.tpl.php');
		}

		//
		// Installation/Activation
		//

		/**
		 * Action for hook: activate_pluginurl
		 * Creates the needed yabp-databaes-table upon activation of the plugin
		 */
		function activate_pluginurl() {

			global $wpdb;

			if($wpdb->get_var('show tables like "' . YAPB_TABLE_NAME . '"') != YAPB_TABLE_NAME) {

				$sql = 'CREATE TABLE ' . YAPB_TABLE_NAME . ' ( ' .
					'id BIGINT NOT NULL AUTO_INCREMENT, ' .
					'post_id BIGINT NOT NULL, ' .
					'URI VARCHAR(255) NOT NULL, ' .
					'PRIMARY KEY (id), ' .
					'INDEX idx_01(post_id) ' .
				');';

				require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
				dbDelta($sql);

			}

			// YAPB Options

			$this->_create_options($this->options);

		}

		/**
		 * This hook gets called on deactivation of YAPB
		 */
		function deactivate_pluginurl() {
			
			// Reviewed 08.02.2007: 
			// Don't think it's neccessary but i'll leave this 
			// code fragment in the class so i have it if i need it...
			// Hey: I'm over 30 and tend to forget hook names from time to time ;-)
			
		}

		/**
		 * this hook gets fired at the bottom of the "Latest Activity" box on the WordPress
		 **/
		function activity_box_end() {

			require_once realpath(dirname(__file__) . '/YapbMaintainance.class.php');
			$this->tpl->assign('yapbMaintainanceInstance', new YapbMaintainance());
			$this->tpl->assign('pluginVersion', $this->pluginVersion);
			$this->tpl->display('dashboard_activity_div.tpl.php');

		}

		/** 
		 * This method enhances the various feeds for an image tag if available
		 * 
		 * @param string $content the content of the post
		 **/ 
		function the_content($content) {
			
			global $post;
			$result = $content;
			
			// We only have to alter the content if we have an image
			if ($post->image) {
			
				// Was this hook called out of a feed generation?
				if (is_feed()) {

					// Does the user want to display images in feeds?
					if (get_option('yapb_display_images_xml')) {
						
						// Please notice: WordPress feeds embed content in CDATA fields
						// So i definitly don't want to use xhtml in these content fields
						// Example: VFXY.com doesn't like &amp; in these URL's

						// Build the image tag
						
						$embed = get_option('yapb_display_images_xml_html_before') . '<img src="';
						
						if (get_option('yapb_display_images_xml_thumbnail_activate')) {
							
							$maxWidth = get_option('yapb_display_images_xml_thumbnail');
							$maxHeight = get_option('yapb_display_images_xml_thumbnail_height');
							$crop = get_option('yapb_display_images_xml_thumbnail_crop');
							
							// phpThumb thumbnailing options

							$options = array();

							// Width and/or Height

							if (!empty($maxWidth)) $options[] = 'w=' . $maxWidth;
							if (!empty($maxHeight)) $options[] = 'h=' . $maxHeight;

							// The crop option only makes sense if both width and height were defined

							if (
								!empty($maxWidth) && 
								!empty($maxHeight) && 
								!empty($crop)
							) {
								$options[] = 'zc=1';
							}

							$embed .= 
								$post->image->getThumbnailHref(
									$options, 
									false // I manually override the users xhtml setting for WordPress feeds: Have a look at the description above
								);
							
							$embed .= '" ';

							$imageWidth = $post->image->getThumbnailWidth($options);
							$imageHeight = $post->image->getThumbnailHeight($options);

						} else {
							
							$embed .= $post->image->getFullHref() . '" ';
							$imageWidth = $post->image->width;
							$imageHeight = $post->image->height; 

						}
						
						$embed .= 'width="' . $imageWidth . '" ';
						$embed .= 'height="' . $imageHeight . '" ';
						
						$style = get_option('yapb_display_images_xml_inline_style');
						if ((!is_null($style)) && ($style != '')) {
							$embed .= 'style="' . $style . '" ';
						}
						$embed .= '>' . get_option('yapb_display_images_xml_html_after');
						
						// Surround the image tag with a link
						// Thanks to fsimo for the idea

						$embed = '<a href="' . get_permalink() . '">' . $embed . '</a>';
						
						// Directly print out the image tag into 
						// the feed
						
						print $embed;
						
					}
					
				} else
				
				// If automatic image insertion is activated in general
				if (get_option('yapb_display_images_activate')) {
					print $this->_getImageTag($post->image);
				}
				
			} 
			
			return $result;
			
		}

			/**
			 * Internal method prints an image tag eventually surrounded
			 * by some html at the beginning of a posts content part
			 * 
			 * @param YapbImage $image
			 * @return string the complete image tag
			 */
			function _getImageTag($image) {
				
				global $post;
				
				$result = '<!-- no image -->';
				
				// Accepted areas of the blog
				$areas = array(
					array('home', is_home()),			// homepage
					array('single', is_single()),		// single page
					array('archive', is_archive()),		// archive page
					array('page', is_page())			// page
				);
				
				// print image into the post content according to the area-setting
				foreach ($areas as $area) {
					
					// If automatic image insertion is activated for this area
					if (get_option('yapb_display_images_' . $area[0]) && $area[1]) {
						
						// Does the user wants straight xhtml-href's?
						$xhtml = get_option('yapb_display_images_xhtml');
						
						// Does the user wants thumbnails?
						if (get_option('yapb_display_images_' . $area[0] . '_thumbnail_activate')) {
							$options = array('w=' . get_option('yapb_display_images_' . $area[0] . '_thumbnail'));
							$imageURI = $image->getThumbnailHref($options);
							$imageWidth = $image->getThumbnailWidth($options);
							$imageHeight = $image->getThumbnailHeight($options);
						} else {
							$imageURI = $image->uri;
							$imageWidth = $image->width;
							$imageHeight = $image->height; 
						}
						
						// Did the user define an inline style definition?
						$style = get_option('yapb_display_images_' . $area[0] . '_inline_style');
						if ((!is_null($style)) && ($style != '')) {
							$style = ' style="' . $style . '"';
						}
						
						$result = get_option('yapb_display_images_' . $area[0] . '_html_before');
						$result .= '<img' . $style . ' width="' . $imageWidth . '" height="' . $imageHeight . '" src="' . $imageURI . '" title="' . $post->post_title . '" alt="' . $post->post_title . '"' . ($xhtml ? ' />' : '>');
						$result .= get_option('yapb_display_images_' . $area[0] . '_html_after');
						
					}
					
				}
				
				return $result;				
				
			}
			
		
		/**
		 * This method additionally pings all sites provided by the user if he
		 * publishes a photoblog article.
		 * It's a 1:1 copy of the wp-function found in wp-includes/functions.php
		 * The only modification: it uses the option yapb_ping_sites instead of 
		 * ping_sites defined in options/write
		 * 
		 * @param number $post_id
		 **/
		function _generic_yapb_ping($post_id = 0) {
			$services = get_settings('yapb_ping_sites');
			$services = preg_replace("|(\s)+|", '$1', $services); // Kill dupe lines
			$services = trim($services);
			if ( '' != $services ) {
				$services = explode("\n", $services);
				foreach ($services as $service) {
					weblog_ping($service);
				}
			}
			return $post_id;
		}

		/**
		 * This method is a small adaption of the function wp_dropdown_cats(...) 
		 * to be found in /wp-admin/admin-functions.php
		 * 
		 * 
		 * @param number $currentcat
		 * @param number $currentparent
		 * @param number $parent
		 * @param number $level
		 * @param array $caegories
		 */
		function _options_categories_array($currentcat = 0, $currentparent = 0, $parent = 0, $level = 0, $categories = 0) {

			global $wpdb;
			$result = array();

			if (!$categories) {
				$categories = get_categories( 'hide_empty=0' );
			}

			if ($categories) {
				foreach ($categories as $category) {
					if ($currentcat != $category->term_id && $parent == $category->parent) {
						$pad = str_repeat('– ', $level);
						$category->name = wp_specialchars($category->name);
						$result[$pad . $category->name] = $category->term_id;
						$result = $result + $this->_options_categories_array($currentcat, $currentparent, $category->term_id, $level+1, $categories);
					}
				}
			}

			return $result;

		}

		/**
		 * This method creates all options needed by YAPB and fills them with their default values
		 * @param array $optionsArray the YAPB options array 
		 **/
		function _create_options($optionsArray) {
			foreach ($optionsArray as $optionGroup) {
				foreach($optionGroup[2] as $optionSubGroup) {
					foreach ($optionSubGroup[2] as $optionName => $optionArray) {
						
						$optionType = $optionArray[0];
						$optionText = $optionArray[1];
						$optionDefaultValue = $optionArray[2];

						switch ($optionType) {

							case 'CHECKBOX_INPUT' :

								add_option($optionName . '_activate', $optionDefaultValue[0]);
								add_option($optionName, $optionDefaultValue[1]);
								break;

							case 'CHECKBOX_SELECT' :

								$optionDefaultValue = $optionArray[3];
								add_option($optionName . '_activate', $optionDefaultValue[0]);
								add_option($optionName, $optionDefaultValue[1]);
								break;

							case 'SELECT' :

								$optionDefaultValue = $optionArray[3];
								add_option($optionName, $optionDefaultValue);
								break;

							case 'CHECKBOX' :
							case 'INPUT' :
							case 'TEXTAREA' :

								add_option($optionName, $optionDefaultValue); 
								break;
							
							case 'CUSTOM_VIEW_EXIF_TAGNAMES' :
								
								add_option($optionName, '');
								break;

						}

					}

				}

			}

		}

	}

?>
