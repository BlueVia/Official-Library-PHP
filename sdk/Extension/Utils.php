<?php
/**
 *
 * @category    opentel
 * @package     com.bluevia
 * @copyright   Copyright (c) 2010 Telefónica I+D (http://www.tid.es)
 * @author      Bluevia <support@bluevia.com>
 * @version     1.0
 *
 * BlueVia is a global iniciative of Telefonica delivered by Movistar and O2.
 * Please, check out http://www.bluevia.com and if you need more information
 * contact us at support@bluevia.com
 */

/**
 * Helper class with useful functions
 * @author Telefonica R&D
 *
 */
class Utils {

	/**
	 * Encodes $input in urlEncoded
	 * @param String $input
	 * @return String $input encoded in URL-encoded
	 */
	public static function urlencode_rfc3986($input) {
		if (is_array($input)) {
			return array_map(array('Utils', 'urlencode_rfc3986'), $input);
		} else if (is_scalar($input)) {
			return str_replace(
      '+',
      ' ',
			str_replace('%7E', '~', rawurlencode($input))
			);
		} else {
			return '';
		}
	}
 
	/**
	 * Utility function for turning the Authorization: header into
	 * parameters, has to do some unescaping
	 * Can filter out any non-oauth parameters if needed (default behaviour)
	 * @param String $header Authorization header
	 * @param Bool $only_allow_oauth_parameters If true only allowed oauth params are set.
	 * 
	 */
	public static function split_header($header, $only_allow_oauth_parameters = true) {
		$params = array();
		if (preg_match_all('/('.($only_allow_oauth_parameters ? 'oauth_' : '').'[a-z_-]*)=(:?"([^"]*)"|([^,]*))/', $header, $matches)) {
			foreach ($matches[1] as $i => $h) {
				$params[$h] = urldecode(empty($matches[3][$i]) ? $matches[4][$i] : $matches[3][$i]);
			}
			if (isset($params['realm'])) {
				unset($params['realm']);
			}
		}
		return $params;
	}

	/**
	 * Helper to try to sort out headers for people who aren't running apache
	 */
	public static function get_headers() {
		if (function_exists('apache_request_headers')) {
			// we need this to get the actual Authorization: header
			// because apache tends to tell us it doesn't exist
			$headers = apache_request_headers();

			// sanitize the output of apache_request_headers because
			// we always want the keys to be Cased-Like-This and arh()
			// returns the headers in the same case as they are in the
			// request
			$out = array();
			foreach ($headers AS $key => $value) {
				$key = str_replace(
            " ",
            "-",
				ucwords(strtolower(str_replace("-", " ", $key)))
				);
				$out[$key] = $value;
			}
		} else {
			// otherwise we don't have apache and are just going to have to hope
			// that $_SERVER actually contains what we need
			$out = array();
			if( isset($_SERVER['CONTENT_TYPE']) )
			$out['Content-Type'] = $_SERVER['CONTENT_TYPE'];
			if( isset($_ENV['CONTENT_TYPE']) )
			$out['Content-Type'] = $_ENV['CONTENT_TYPE'];

			foreach ($_SERVER as $key => $value) {
				if (substr($key, 0, 5) == "HTTP_") {
					// this is chaos, basically it is just there to capitalize the first
					// letter of every word that is not an initial HTTP and strip HTTP
					// code from przemek
					$key = str_replace(
            " ",
            "-",
					ucwords(strtolower(str_replace("_", " ", substr($key, 5))))
					);
					$out[$key] = $value;
				}
			}
		}
		return $out;
	}

	/**
	 * This function takes a input like a=b&a=c&d=e and returns the parsed
	 * parameters like this
	 * array('a' => array('b','c'), 'd' => 'e')
	 * @param String $input Query string
	 * @return array of parameters
	 */
	public static function parse_parameters( $input ) {
		if (!isset($input) || !$input) return array();

		$pairs = explode('&', $input);

		$parsed_parameters = array();
		foreach ($pairs as $pair) {
			$split = explode('=', $pair, 2);
			$parameter = urldecode($split[0]);
			$value = isset($split[1]) ? urldecode($split[1]) : '';

			if (isset($parsed_parameters[$parameter])) {
				// We have already recieved parameter(s) with this name, so add to the list
				// of parameters with this name

				if (is_scalar($parsed_parameters[$parameter])) {
					// This is the first duplicate, so transform scalar (string) into an array
					// so we can add the duplicates
					$parsed_parameters[$parameter] = array($parsed_parameters[$parameter]);
				}

				$parsed_parameters[$parameter][] = $value;
			} else {
				$parsed_parameters[$parameter] = $value;
			}
		}
		return $parsed_parameters;
	}

	/**
	 * Encode urlencoded query and sort it
	 * @param array $params Paramters array
	 * @return String query string
	 */
	public static function build_URLEncoded_query($params) {
		if (!$params) return '';

		// Urlencode both keys and values
		$keys = Utils::urlencode_rfc3986(array_keys($params));
		$values = Utils::urlencode_rfc3986(array_values($params));
		$params = array_combine($keys, $values);

		// Parameters are sorted by name, using lexicographical byte value ordering.
		// Ref: Spec: 9.1.1 (1)
		uksort($params, 'strcmp');

		$pairs = array();
		foreach ($params as $parameter => $value) {
			if (is_array($value)) {
				// If two or more parameters share the same name, they are sorted by their value
				// Ref: Spec: 9.1.1 (1)
				// June 12th, 2010 - changed to sort because of issue 164 by hidetaka
				sort($value, SORT_STRING);
				foreach ($value as $duplicate_value) {
					$pairs[] = $parameter . '=' . $duplicate_value;
				}
			} else {
				$pairs[] = $parameter . '=' . $value;
			}
		}
		// For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61)
		// Each name-value pair is separated by an '&' character (ASCII code 38)
		return implode('&', $pairs);
	}

	/**
	 * Helper to determine if required parameter is empty
	 * @param string|array $parameter_value. Parameter, or array of parameters.
	 * @param String $extra_message an extra message for the Bluevia_Exception 
	 * @throws Bluevia_exception if missing parameters
	 */
	public static function checkParameter($parameter_value,$extra_message=null) {
		if (!is_array($parameter_value)) {
			$parameter_value = array($parameter_value);
		}

		foreach($parameter_value as $key => $value) {
			if (empty($value) || $value === "") {
				throw new Bluevia_Exception('-101',null,$key,$extra_message);
			}
		}
	}

	/**
	 * Checks if the number is a positive integer
	 * @param $number integer to be checked
	 * @param $field The name of the parameter with the $number value
	 * @return Bool True if $number is a positive integer value
	 */
	public static function isInteger($number,$field){
		if (is_null($number) || $number === "") {
			throw new Bluevia_Exception('-101',null,$field);
		}
		if (!preg_match('/^[0-9]*$/', $number)){
			throw new Bluevia_Exception('-111',null,$field);
		}
	}

	/**
	 * Checks if the phoneNumber parameter could be a phoneNumber if the parameter is a non zero,
	 * positive integer 
	 * @param String $phoneNumber 
	 * @return Bool True if phoneNUmber is a non-zero, positive integer.
	 * @throws Bluevia_Exception if the phoneNumber is invalid
	 */
	public static function isPhoneNumber($phoneNumber){
		Utils::isInteger($phoneNumber,'$phoneNumber');
		if ($phoneNumber<=0){
			throw new Bluevia_Exception('-105',null,'$phoneNumber');
		}
	}

	/**
	 * Helper function to change timestamp format
	 * @param String $timestamp a timestamp
	 * @return formatted timestamp
	 */
	public static function createDateTime($timestamp){
		date_default_timezone_set('UTC');
		$datetime = date("Y-m-d H:m:s", $timestamp);
		$datetime = str_replace(" ","T",$datetime);
		$datetime .="Z";
		return $datetime;
	}

	/**
	 * Helper function to validate URLs
	 * @param String $url URL to vallidate
	 * @return bool True if is a valid URL
	 */
	public static function isValidURL($url)
	{
		return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
	}

	public static function checkPath($path){
		if (!file_exists($path)){
			throw new Bluevia_Exception('-5',null,$path);
		}
	}
	/**
	 * Helper function: random identifier
	 * @param Int|null $length If length is set the identifier will have max $length  
	 */
	public static function generateRandomIdentifier($length=null) {
		$mt = microtime();
		$rand = mt_rand();
		$id=md5($mt . $rand);
		if (isset($length) && $length!=0){
			$id = substr($id,0,$length);
		}
		return $id;
	}
	
	public static function convertToUTF8($content){
		if (!mb_detect_encoding($content,'UTF-8',true)){
			$content=mb_convert_encoding($content,'UTF-8');
		}
		return $content;
	}

}
