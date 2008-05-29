<?php

	/*	Class YapbCache
		Description: This class bundles all maintainance/general information functionality
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

	require_once realpath(dirname(__file__) . '/../includes/GlobExtension.script.php');
	require_once realpath(dirname(__file__) . '/YapbImage.class.php');

	class YapbMaintainance {

		var $imagefileList = null;
		var $cachefileList = null;

		/**
		 * Constructor
		 *
		 * @return YapbMaintainance
		 */
		function YapbMaintainance() {
			$this->fetchImagefileList();
			$this->fetchCachefileList();
		}

		
		/**
		 * Method fetches a list of all cached image files
		 */
		function fetchCachefileList() {
			$this->cachefileList = glob(YAPB_CACHE_ROOT_DIR . '*.th.*');
		}

		/**
		 * Method returns the count of all cached image files
		 *
		 * @return number of images
		 */
		function getCachefileCount() {
			return count($this->cachefileList);
		}

		/**
		 * Method returns the size of all cached image files
		 *
		 * @return number of bytes
		 */
		function getCachefileSizeBytes() {
			$result = 0;
			for ($i=0, $len=$this->getCachefileCount(); $i<$len; $i++) {
				$result += filesize($this->cachefileList[$i]);
			}
			return $result;
		}

		/**
		 * Method deletes all cached image files
		 */
		function clearCache() {
			for ($i=0, $len=$this->getCachefileCount(); $i<$len; $i++) {
				unlink($this->cachefileList[$i]);
			}
			$this->cachefileList = array();
		}

		/**
		 * Method fetches the list of all posted images
		 */
		function fetchImagefileList() {
			global $wpdb;
			$this->imagefileList = array();
			$imageRows = $wpdb->get_results('SELECT id,uri FROM ' . YAPB_TABLE_NAME . ' ORDER BY id');
			for ($i=0, $len=count($imageRows); $i<$len; $i++) {
				$temp = $imageRows[$i];
				array_push($this->imagefileList, YapbImage::systemFilePath($temp->uri));
			}
		}

		/**
		 * Method returns the count of all uploaded images
		 *
		 * @return number of all images uploaded
		 */
		function getImagefileCount() {
			return count($this->imagefileList);
		}

		/**
		 * Method returns the bytesize of all uploaded images
		 *
		 * @return number of bytes
		 */
		function getImagefileSizeBytes() {
			$result = 0;
			for ($i=0, $len=$this->getImagefileCount(); $i<$len; $i++) {
				$result += filesize($this->imagefileList[$i]);
			}
			return $result;
		}

	}

?>