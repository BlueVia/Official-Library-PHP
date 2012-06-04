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
 * Implementation of HttpConnector to sign requests using OAuth
 * @author Telefonica R&D
 *
 */
class OAuth_Http_Connector extends HTTP_Connector implements IOAuth{

	// internal variables
	/**
	* array $_consumer Consumer key and secret. array(consumer_key=>XXXXXX, consumer_secret=>YYYYYY)
	*/
	protected $_consumer;

	/**
	 * array $_token Token access and secret. array('token_access'=>XXXXX, 'token_secret'=>YYYYYYY)
	 */
	protected $_token;
	/**
	 * array $_parameters Oauth parameters
	 */
	protected $_parameters;

	/**
	 * @var string VERSION OAuth version
	 */
	const VERSION = '1.0';

	// for debug purposes

	/**
	 * string $base_string Oauth signature base string
	 */
	protected $_base_string;
	/**
	 * string $_key_string Oauth key string
	 */
	protected $_key_string;

	/**
	 * Constructor. Set's consumer key and token
	 * @param String $consumer The consumer key identifiying the app.
	 * @param String $consumerSecret The consumer secret
	 * @param String|null $token The token identifiying the user
	 * @param String|null $tokenSecret The token secret
	 * @param String|null $certificate Certificate's path
	 * @param String|null $fileCertPass Certificate's password
	 */
	public function __construct($consumer, $consumerSecret,$token=null,$tokenSecret=null, $certificate=null,$fileCertPass=null){
		Utils::checkParameter(array('$consumer'=>$consumer,'$consumerSecret'=>$consumerSecret));
		$this->_consumer=array('consumer_key'=>$consumer,'consumer_secret'=>$consumerSecret);
		if (!(is_null($token) && is_null($tokenSecret)))
		$this->_token=array('token_access'=>$token,'token_secret'=>$tokenSecret);
		parent::__construct($certificate,$fileCertPass);
	}

	/**
	 * Set oauth extra parameters method
	 * @param array() $parameters
	 */
	public function setExtraParameters($parameters){
		if (empty($this->_parameters)){
			$this->_parameters=$parameters;
		}else{
			$this->_parameters=array_merge($this->_parameters,$parameters);
		}
	}

	/**
	 * Create's an authorization header
	 * @return string Authorization header
	 */
	public function authenticate(){
		$this->_setDefaultParameters();
		$this->_sign_request();
		return $this->_to_header();
	}

	/**
	 * Gets the access token
	 * @return String The access token
	 */
	public function getToken(){
		$token =$this->_token;
		return $token['token_access'];
	}

	/**
	 * Set tokens
	 * @param String $token the access token
	 * @param String $tokenSecret The token secret
	 */
	public function setTokens($token,$tokenSecret){
		Utils::checkParameter(array('token'=>$token,'tokenSecret'=>$tokenSecret));
		$this->_token=array('token_access'=>$token,'token_secret'=>$tokenSecret);
	}

	/**
	 * Checks if it is a two-legged OAuth
	 */
	public function isTwoLegged(){
		return is_null($this->_token);
	}


	//////////////////////////////////////////////////////////////////////////////////////////////////
	//
	//  PRIVATE FUNCTIONS
	//
	//////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Helper function, set's oauth standard parameters
	 */
	protected function _setDefaultParameters() {
		$parameters = array();
		$this->_base_string=null;
		$this->_key_string=null;
		$defaults = array(
					"oauth_version" => self::VERSION,
                    "oauth_nonce" => Utils::generateRandomIdentifier(),
                    "oauth_consumer_key" => $this->_consumer['consumer_key']);
		$parameters['oauth_timestamp'] = (isset($parameters['oauth_timestamp']))
		? $parameters['oauth_timestamp'] : $this->_generate_timestamp();
		if(isset($this->_token)) $parameters['oauth_token']=$this->_token['token_access'];
		$parameters = array_merge($defaults, $parameters);

		$parameters = array_merge(Utils::parse_parameters
		(parse_url($this->_address, PHP_URL_QUERY)), $parameters);
		$this->setExtraParameters($parameters);
	}

	/**
	 * Helper function, set's an oauth parameter
	 * @param string $name Parameter name
	 * @param string $value Value to set up to
	 * @param boolean $allow_duplicates Allow two parameters having  with the same name
	 */
	protected function _set_parameter($name, $value, $allow_duplicates = true) {
		if ($allow_duplicates && isset($this->_parameters[$name])) {
			// We have already added parameter(s) with this name, so add to the list
			if (is_scalar($this->_parameters[$name])) {
				// This is the first duplicate, so transform scalar (string)
				// into an array so we can add the duplicates
				$this->_parameters[$name] = array($this->_parameters[$name]);
			}
			$this->_parameters[$name][] = $value;
		} else {
			$this->_parameters[$name] = $value;
		}
	}

	/**
	 * Unset the parameter with name $name
	 * @param string $name Parameter's name to be unset
	 */
	private function _unset_parameter($name) {
		unset($this->_parameters[$name]);
	}

	/**
	 * The request parameters, sorted and concatenated into a normalized string.
	 * @return string
	 */
	private function _get_signable_parameters() {
		// Grab all parameters
		$urlEnc=false;
		foreach($this->_headers as $header){
			if (strpos($header,BV_Constants::URL_ENCODED)){
				$urlEnc=true;
			}
		}
		if($urlEnc){
			$bodyParams=Utils::parse_parameters($this->_content);
			$params = array_merge($this->_parameters,$bodyParams);
		}
		else $params=$this->_parameters;
		// Remove oauth_signature if present
		// Ref: Spec: 9.1.1 ("The oauth_signature parameter MUST be excluded.")
		if (isset($params['oauth_signature'])) {
			unset($params['oauth_signature']);
		}

		return Utils::build_URLEncoded_query($params);
	}

	/**
	 * The base string defined as the method, the url
	 * and the parameters (normalized), each urlencoded
	 * and the concated with &.
	 *
	 * @return the base string of this request
	 */
	private function _get_signature_base_string() {
		$parts = array(
		$this->_method,
		$this->_get_normalized_http_url(),
		$this->_get_signable_parameters(),
		);

		$parts = Utils::urlencode_rfc3986($parts);

		return implode('&', $parts);
	}


	/**
	 * Parses the url and rebuilds it to be scheme://host/path
	 * @return String  scheme://host/path
	 */
	private function _get_normalized_http_url() {
		$parts = parse_url($this->_address);

		$scheme = (isset($parts['scheme'])) ? $parts['scheme'] : 'http';
		$port = (isset($parts['port'])) ? $parts['port'] : (($scheme == 'https') ? '443' : '80');
		$host = (isset($parts['host'])) ? strtolower($parts['host']) : '';
		$path = (isset($parts['path'])) ? $parts['path'] : '';

		if (($scheme == 'https' && $port != '443')
		|| ($scheme == 'http' && $port != '80')) {
			$host = "$host:$port";
		}
		return "$scheme://$host$path";
	}

	/**
	 * Builds the Authorization: header
	 * @return string Authorization: header
	 */
	private function _to_header($realm=null) {
		$first = true;
		if($realm) {
			$out = 'Authorization: OAuth realm="' . Utils::urlencode_rfc3986($realm) . '"';
			//$out = 'OAuth realm="' . Utils::urlencode_rfc3986($realm) . '"';
			$first = false;
		} else
		$out = 'Authorization: OAuth';
		//$out = 'OAuth';

		$total = array();
		foreach ($this->_parameters as $k => $v) {
			if (substr($k, 0, 5) == "oauth" || substr($k, 0, 6) == "xoauth") {
				if (is_array($v)) {
					throw new Bluevia_Exception('-101',null,$k);
				}
				$out .= ($first) ? ' ' : ',';
				$out .= Utils::urlencode_rfc3986($k) .
              '="' .
				Utils::urlencode_rfc3986($v) .
              '"';
				$first = false;
			}
		}
		$this->_parameters=null;
		return $out;
	}

	/**
	 * Helper function to sign a request
	 */
	private function _sign_request() {
		$this->_set_parameter(
      "oauth_signature_method",
      'HMAC-SHA1',
		false
		);
		$this->_build_signature();
		$signature = $this->_encode_signature();
		$this->_set_parameter("oauth_signature", $signature, false);
	}

	/**
	 * Helper function for encoding signature base string with hash_hmac
	 */
	private function _encode_signature(){
		$key_parts = Utils::urlencode_rfc3986($this->_key_string);
		$key = implode('&', $key_parts);
		return base64_encode(hash_hmac('sha1', $this->_base_string, $key, true));
	}

	/**
	 * Helper function for setting The $_base_string and the $_key_string
	 */
	protected function _build_signature() {
		$base_string = $this->_get_signature_base_string();
		$this->_base_string = $base_string;
		$key_parts = array(
		$this->_consumer['consumer_secret']);
		$this->_key_string=$key_parts;
		if (isset($this->_token)) $this->_key_string[]=$this->_token['token_secret'];
		else $this->_key_string[]='';

	}

	/**
	 * Helper function: current timestamp
	 */
	private function _generate_timestamp() {
		return time();
	}


}



?>
