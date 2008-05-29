<?php

/**
 * Class Params
 * Simple util class to allow http parameter 
 * fetching with default values
 * May be used statically
 **/

class Params {

	function Params() {}

	/**
	 * Method returns if the given parametername was provided in the request
	 * You may call this method statically
	 *
	 * @param string $name
	 * @return boolean
	 */
	function exists($name) {
		$result = false;
		$result = array_key_exists($name, $_GET);
		$result = $result | array_key_exists($name, $_POST);
		return $result;
	}

	/**
	 * Method tries to fetch value for given parametername out of request
	 * return defaultvalue if no parameter was found in request 
	 * You may call this method statically
	 *
	 * @param string $name
	 * @param string $defaultValue
	 * @return string
	 */
	function get($name, $defaultValue=NULL) {
		$result = $defaultValue;
		if ($this->exists($name)) {
			if (array_key_exists($name, $_GET)) $result = $_GET[$name];
			else $result = $_POST[$name];
		}
		return $result;
	}

}

?>