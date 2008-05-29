<?php

	/**
	 * Lightweight Logmessage DAO for YAPB logging
	 **/
	class YapbLoggerMessage {
		
		var $level;
		var $message;
		var $timestamp;

		function YapbLoggerMessage($level, $message) {
			$this->level = $level;
			$this->message = $message;
			$this->timestamp = mktime();
		}

		function toString() {
			return date('', $this->timestamp) . ' ' . $this->level . ' ' . $this->message;
		}

	}

	/**
	 * Lightweight YAPB Logger
	 **/
	class YapbLogger {

		var $messages;

		function YapbLogger() {
			$this->messages = array();
		}

		function toString($separator) {
			$result = '';
			$messageStrings = array();
			foreach ($this->messages as $message) {
				$messageStrings[] = $message->toString();
			}
			return implode($separator, $messageStrings);
		}

		function debug($message) { 
			$this->messages[] = new YapbLoggerMessage('DEBUG', $message); 
		}
		
		function info($message) { 
			$this->messages[] = new YapbLoggerMessage('INFO', $message); 
		}
		
		function warn($message) { 
			$this->messages[] = new YapbLoggerMessage('WARN', $message); 
		}
		
		function error($message) { 
			$this->messages[] = new YapbLoggerMessage('ERROR', $message); 
		}
		
		function fatal($message) { 
			$this->messages[] = new YapbLoggerMessage('FATAL', $message); 
		}


	}

?>