<?php

	/*	Class ExifUtil
		This class provides some higher level methods around phpExifWR
		which are needed by YAPB.
	*/

	class ExifUtils {

		var $info = 'Class ExifUtils (C) 2006 by J.P.Jarolim';

		/** constructor **/
		function ExifUtils() {}

		/**
		 * Method provides the exif-data to an image
		 * $yapbImage: provide the YapbImage Object which should be parsed for EXIF data
		 * $flagUnfiltered: if true, no filtering will occur whatever the user has chosen
		 * 
		 * @param YapbImage $yapbImage
		 * @param boolean $flagUnfiltered
		 * @return array
		 **/
		function getExifData($yapbImage, $flagUnfiltered=false) {
			
			$result = null;
			
			require_once realpath(dirname(__file__) . '/phpExifRW-1.1/exifReader.inc');
			$phpExifReader = new phpExifReader($yapbImage->systemFilePath());
			$phpExifReader->ImageReadMode = 1; // This should turn off EXIF thumbnail caching too
			$result = $phpExifReader->getImageInfo();

			// If the user wants his EXIF data filtered, we do that
			if (get_option('yapb_filter_exif_data') && ($flagUnfiltered == false)) {
				$result = ExifUtils::filterExifData($result);
			}
			
			if (count($result) == 0) return null;
			else return $result;
		}
		
		/**
		 * Method currently under developement
		 **/
		function setExifData($yapbImage, $exifData) {
			
			require_once realpath(dirname(__file__) . '/phpExifRW-1.1/exifWriter.inc');
			$phpExifWriter = new phpExifWriter($yapbImage->systemFilePath());

		}

		/**
		 * This method reduces the load of exif fields by filtering
		 * 
		 * @param array $exifData
		 * @return array
		 **/
		function filterExifData($exifData) {
			$tagnamesToBeShown = ExifUtils::getTagnamesFilter();
			$result = array();
			foreach ($exifData as $key => $value) {
				if (in_array($key, $tagnamesToBeShown)) {
					$result[$key] = $value;
				}
			}
			return $result;
		}

		/**
		 * This method gets all learned exif-tagnames out of the WordPress options
		 * 
		 * @return array
		 */
		function getLearnedTagnames() {
			$commaSeparatedList = get_option('yapb_learned_exif_tagnames');
			if ($commaSeparatedList == '') return array();
			else {
				$result = explode(',', $commaSeparatedList);
				sort($result, SORT_STRING);
				return $result;
			}
		}

		/**
		 * This method takes an array of exif tags and compares it with the already learned tagnames
		 * The learned tagnames will be extended if needed
		 *
		 * @param array $exifArray
		 */
		function learnTagnames($exifArray) {
			if (!is_null($exifArray)) {
				$learnedTagnames = ExifUtils::getLearnedTagnames();
				foreach ($exifArray as $key => $value) {
					if (!in_array($key, $learnedTagnames)) {
						array_push($learnedTagnames, $key);
					}
				}
				if (count($learnedTagnames) > 0) update_option('yapb_learned_exif_tagnames', implode(',', $learnedTagnames));
				else update_option('yapb_learned_exif_tagnames', 'none');
			}
		}

		/**
		 * This method updates the tagnames filter WordPress option
		 * 
		 * @param array $tagnamesArray 
		 */
		function updateTagnamesFilter($tagnamesArray) {
			if (!is_null($tagnamesArray)) {
				if (count($tagnamesArray) > 0) {
					update_option('yapb_view_exif_tagnames', implode(',', $tagnamesArray));
				} else update_option('yapb_view_exif_tagnames', 'none');
			} else update_option('yapb_view_exif_tagnames', 'none');
		}

		/**
		 * This method fetches the tagnames filter WordPress option
		 *
		 * @return array
		 */
		function getTagnamesFilter() {
			$commaSeparatedList = get_option('yapb_view_exif_tagnames');
			if ($commaSeparatedList == 'none') return array();
			else return explode(',', $commaSeparatedList);
		}

	}

?>