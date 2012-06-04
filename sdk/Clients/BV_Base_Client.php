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
 * Abstract representation of an HTTP REST Client for the Bluevia API
 * @author Telefonica R&D
 *
 */
abstract class BV_Base_Client{

	/**
	 * %Parser for Bluevia's HTTP Response.
	 * Parser $_parser
	 */
	protected $_parser;
	/**
	 * %Serializer for Bluevia's HTTP Request.
	 * Serializer $_serializer
	 */
	protected $_serializer;
	/**
	 * Object used to make the HTTP Request
	 * Oauth_HTTP_Connector $_connector
	 */
	protected $_connector;
	/**
	 * The URI to which the request will be made.
	 * String $_url
	 */
	protected $_url;
	/**
	 * The working mode for the client.
	 * Bv_Mode $_mode
	 */
	protected $_mode;
	/**
	 * Response information from server
	 * Generic_Response $_genericResponse
	 */
	protected $_genericResponse;

	/**
	 * Helper function to instantiate the common properties in Trusted Clients.
	 * @param BV_Mode $mode The working mode for the client. Represents the development stage of your application.
	 * @param String $consumerkey The string identifying the application- you obtained when you registered your application within the provisioning portal.
	 * @param String $consumerSecret A secret -a string- used by the consumer to establish ownership of the consumer key.
	 * @param String $cert Path to the client's certificate
	 * @param String $cerFilePass Certificate's password
	 */
	protected function initTrusted($mode, $consumerkey, $consumerSecret,
	$cert, $cerFilePass=null){
		Utils::checkParameter((array('$cert'=>$cert)));
		Utils::checkPath($cert);
		$this->checkTwoLeggedCredentials($consumerkey, $consumerSecret);
		$this->_connector=new Oauth_Http_Connector($consumerkey, $consumerSecret, null, null, $cert, $cerFilePass);

		$this->_checkMode($mode);
	}
	/**
	 * Helper function to instantiate the common properties in Clients.
	 * @param BV_Mode $mode The working mode for the client. Represents the development stage of your application.
	 * @param String $consumerkey The string identifying the application- you obtained when you registered your application within the provisioning portal.
	 * @param String $consumerSecret A secret -a string- used by the consumer to establish ownership of the consumer key.
	 * @param String $token The token -a string- used by the client for granting access permissions to the server.
	 * @param String $tokenSecret The secret of the access token.
	 */
	protected function initUntrusted($mode,$consumerkey, $consumerSecret, $token=null, $tokenSecret=null){
		$this->_connector=new Oauth_Http_Connector($consumerkey, $consumerSecret, $token, $tokenSecret);
		$this->_checkMode($mode);
	}

	/**
	 * Makes the HTTP POST Request
	 * @param String $address Url to wich the request will be made
	 * @param array|null $parameters Array of query parameters
	 * @param array|null $content (Optional) Array containing the body parameters that will be serialized before sending.
	 * @param array|null $headers (Optional) Array containing HTTP Request's headers.
	 * @return String containing the parsed body response from Bluevia
	 * @throws Bluevia_Exception
	 */
	protected function baseCreate($address,$parameters=null, $content=null, $headers=null){
		try{
			if (!is_null($this->_serializer)){
				$content=$this->_serializer->serialize($content);
			}
			$params=$this->_initDefaultParameters($parameters);
			$url=$this->_url.$address;
			$this->_genericResponse=$this->_connector->create($url, $params,
			$content, $headers);
			$body=$this->_parseResponse();
			return $body;
		}
		catch (Connector_Exception $e){
			$this->_parseError($e,$this->_parser);
		}

	}

	/**
	 * Makes the HTTP PUT Request. Not implemented in 1.6 version.
	 * @param String $address Url to wich the request will be made
	 * @param array|null $parameters (Optional) Array of query parameters
	 * @param array|null $content (Optional) Array containing the body parameters that will be serialized before sending.
	 * @param array|null $headers (Optional) Array containing HTTP Request's headers.
	 * @throws Bluevia_Exception Not implemented.
	 */
	protected function baseUpdate($address,$parameters=null, $content=null, $headers=null){
		throw new Bluevia_Exception('-3');
	}

	/**
	 * Makes the HTTP GET Request
	 * @param String $address Url to wich the request will be made
	 * @param array|null $parameters (Optional) Array of query parameters
	 * @return String containing the parsed body response from Bluevia
	 * @throws Bluevia_Exception
	 */
	protected function baseRetrieve($address,$parameters=null){
		try{
			$params=$this->_initDefaultParameters($parameters);
			$url=$this->_url.$address;
			$this->_genericResponse=$this->_connector->retrieve($url, $params);
			$body=$this->_parseResponse();
			return $body;
		}
		catch (Connector_Exception $e){
			$this->_parseError($e,$this->_parser);
		}

	}

	/**
	 * Makes the HTTP DELETE Request
	 * @param String $address Url to wich the request will be made
	 * @param array|null $parameters (Optional) Array of query parameters
	 * @return String containing the parsed body response from Bluevia
	 * @throws Bluevia_Exception
	 */
	protected function baseDelete($address,$parameters=null){
		try{
			$params=$this->_initDefaultParameters($parameters);
			$url=$this->_url.$address;
			$this->_genericResponse=$this->_connector->delete($url, $params);
			$body=$this->_parseResponse();
			return $body;
		}
		catch (Connector_Exception $e){
			$this->_parseError($e,$this->_parser);
		}
	}

	/**
	 * Get the Location Header from the headers in the $_genericResponse propertie (Generic_Response).
	 * @return String containing Locations's header value.
	 */
	protected function _getLocationHeader(){
		$response = $this->_genericResponse->getAdditionalData();
		return $response['headers']['location'];
	}

	/**
	 * Helper function to create the body needed in methods wich check the delivery status following a notification strategy
	 * @param String $endpoint The URI where your application is expecting to receive the delivery status notifications.
	 * @param String $correlator An application generated identifier for the request.
	 */
	protected function _checkEndpoint ($endpoint,$correlator){
		if (!empty($endpoint)) {
			if (!empty($correlator)) {
				$body['correlator'] = $correlator;
				$body['endpoint'] =  $endpoint;
				return $body;
			}
			else{
				throw new Bluevia_Exception('-112',null,'Correlator');
			}
		}
		else if (!empty($correlator)){
			throw new Bluevia_Exception('-112',null,'Endpoint');
		}
	}

	/**
	 * Helper function to parse the server's error
	 * @param Connector_Exception $e The server's error
	 * @param Parser $parser The parser that will be used to parse the error.
	 * @throws Bluevia_Exception, Connector_Exception
	 */
	protected function _parseError($e,$parser){
		$body=null;
		if ($e instanceof Connector_Exception ){
			$additionalData=$e->getAdditionalData();

			if (!is_null($parser)){
				$body= $parser->parse($additionalData['content']);
			}
		}
		if (!$body){
			throw $e;
		}
		if (isset($body->ClientException)){
			$text=$body->ClientException->text;
			$extraInfo='';
			if (isset($body->ClientException->variables)){
				foreach($body->ClientException->variables as $value){
					$extraInfo.='. '.$value;
				}
			}
			$body=$text.': '.$extraInfo;
		}else if (isset($body->ServerException)){
			$text=$body->ServerException->text;
			$extraInfo='';
			if (isset($body->ServerException->variables)){
				foreach($body->ServerException->variables as $value){
					$extraInfo.='. '.$value;
				}
			}
			$body=$text.': '.$extraInfo;
		}else if (isset($body->text)){
			$body=(string)$body->text;
		}
		if (is_string($body)){
			$message=$e->getMessage();
			throw new Bluevia_Exception($e->getCode(),$message.': '.$body);
		}else {
			throw $e;
		}

	}

	/**
	 * Helper function to create Response objects from standard classes (stdClass)
	 * @param stdClass $response Server's parsed response.
	 * @param Class $object An instance of the class wich properties have to be set
	 * @return Object with all the info set.
	 */
	protected function _createInfo($response,$object){
		if (!empty($response)){
			foreach ($response as $key => $value){
				if (property_exists(get_class($object),$key)){
					$object->$key=$value;
				}
			}
		}
		return $object;
	}

	/**
	 * Helper function to check if consumer key and secret are not null. (Two-legged)
	 * @param String $consumerkey The string identifying the application- you obtained when you registered your application within the provisioning portal.
	 * @param String $consumerSecret A secret -a string- used by the consumer to establish ownership of the consumer key.
	 * @throws Bluevia_Exception if $consumer or $cosumerSecret are null or empty.
	 */
	protected function checkTwoLeggedCredentials($consumer,$cosumerSecret){
		Utils::checkParameter(array('$consumer'=>$consumer,
											'$cosumerSecret'=>$cosumerSecret));
	}

	/**
	 * Helper function to check if consumer key, secret token and token secret are not null. (Two-legged)
	 * @param String $consumerkey The string identifying the application- you obtained when you registered your application within the provisioning portal.
	 * @param String $consumerSecret A secret -a string- used by the consumer to establish ownership of the consumer key.
	 * @param String $token The token -a string- used by the client for granting access permissions to the server.
	 * @param String $tokenSecret The secret of the access token.
	 * @throws Bluevia_Exception if $consumer, $cosumerSecret, $token or $tokenSecret are null or empty.
	 *
	 */
	protected function checkThreeLeggedCredentials($consumer,$cosumerSecret,$token,$tokenSecret){
		$this->checkTwoLeggedCredentials($consumer,$cosumerSecret);
		Utils::checkParameter(array('$token'=>$token,'$tokenSecret'=>$tokenSecret));
	}

	/**
	 * Replaces the %TRUS% for Trusted SDK
	 * @return String $url The URI to which the request will be made.
	 */
	protected function _rewriteUrl($url)
	{
		$this->_url = BV_Constants::BASE_URL;
		$this->_url .= $this->_replaceUrlMode($url);
		$this->_url = str_replace('%TRUS%',BV_Constants::URL_MODE,$this->_url);
	}

	/**
	 * Replaces the %ENV% (mode) by the selected (Sandbox, Commercial)
	 * @param String $url The URI to which the request will be made.
	 */
	protected function _replaceUrlMode($url){
		if ($this->_mode==BV_Mode::SANDBOX){
			$replacement='_'.BV_Mode::SANDBOX;
		} else {
			$replacement ='';
		}
		return str_replace('%ENV%', $replacement, $url);
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////
	//
	//  PRIVATE FUNCTIONS
	//
	//////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Helper function to parse the server's response
	 */
	private function _parseResponse(){
		$body=$this->_genericResponse->getAdditionalData();
		if (is_null($this->_parser)){
			return $body['content'];
		}
		else{
			return $this->_parser->parse($body['content']);
		}
	}

	/**
	 * Helper function to check if the mode has a valid value. In this case $_mode propertiw will be set.
	 * @param BV_Mode $mode The working mode for the client. Represents the development stage of your application.
	 */
	private function _checkMode($mode){
		Utils::checkParameter(array('$mode'=>$mode));
		try{
			$mode=BV_Mode::getValue($mode);
			$this->_mode=$mode;
		}catch (Exception $e){
			throw new Bluevia_Exception('-104');
		}
	}

	/**
	 * Initializes the default and specific parameters ($parameters)
	 * @param array $parameters Query parameters.
	 * @return array with all the query parametes (Default and non-default ones)
	 */
	private function _initDefaultParameters($parameters)
	{
		$defaultParameters=array('alt'=> BV_Constants::ALT, 'version'=>BV_Constants::VERSION);
		if (is_array($parameters))
		return array_merge($defaultParameters,$parameters);
		return $defaultParameters;
	}

}
