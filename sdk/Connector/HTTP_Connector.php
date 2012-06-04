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
 * HttpConnector that allows to send requests via HTTP REST
 * @author Telefonica R&D
 *
 */
abstract class HTTP_Connector implements IConnector,IAuth {

	/**
	 * Represent HTTP POST method.
	 */
	const POST = 'POST';
	/**
	 * Represent HTTP GET method.
	 */
	const GET = 'GET';
	/**
	 * Represent HTTP DELETE method.
	 */
	const DELETE = 'DELETE';
	/**
	 * Connection timeout in seconds
	 */
	const TIMEOUT = 20;
	/**
	 * Whether to verify peer's SSL certificate
	 */
	const SSL_VERIFY_PEER = true;
	/**
	 * Whether to check that Common Name in SSL certificate matches host name
	 */
	const SSL_VERIFY_HOST = true;
	/**
	 *  String $_method Current HTTP method
	 */
	protected $_method;
	/**
	 * String $_address The URI to which the request will be made.
	 */
	protected $_address;
	/**
	 * String $_content The request body.
	 */
	protected $_content;
	/**
	 *  Array $_headers The request headers.
	 */
	protected $_headers;
	/**
	 * String $_certificate Path to the cert file
	 */
	protected $_certificate;
	/**
	 *
	 * String $_fileCertPass Certificate's password
	 */
	protected $_fileCertPass;

	/**
	 * Constructor
	 * @param String|null $certificate (Optional) Path to the cert file
	 * @param String|null $fileCertPass (Optional Certificate's password
	 */
	public function __construct($certificate=null,$fileCertPass=null){
		$this->_certificate=$certificate;
		$this->_fileCertPass= $fileCertPass;
	}

	/**
	 * Method to make a POST request
	 * @param string $address Request URL
	 * @param string $headers Request headers
	 * @param array $parameters Query parameters
	 * @param string $content Request body
	 * @return Generic_Response the HTTP response from server.
	 * @throws Connector_Exception
	 */
	public function create ($address,$parameters=array(),$content=null,$headers=null){


		$this->_setParameters(self::POST,$address,$headers,$content);
		$request=$this->_newHTTPRequest($parameters);
		$request=$this->_setBody($request);
		$response=$this->_sendRequest($request);
		return $response;
	}

	/**
	 * Method to make a GET request
	 * @param string $address Request URL
	 * @param array $parameters Query parameters
	 * @return Generic_Response the HTTP response from server.
	 * @throws Connector_Exception
	 */
	public function retrieve ($address,$parameters=array()){

		$this->_setParameters(self::GET,$address);
		$request=$this->_newHTTPRequest($parameters);
		$response=$this->_sendRequest($request);
		return $response;

	}

	/**
	 * Method to make an HTTP request.
	 * @param string $address Request URL
	 * @param string $headers Request headers
	 * @param array $parameters Query parameters
	 * @param string $content Request body
	 * @throws Bluevia_Exception
	 */
	public function update ($address,$parameters=array(),$content=null,$headers=null){
		throw new Bluevia_Exception('-3');
	}

	/**
	 * Method to make a DELETE request
	 * @param string $address Request URL
	 * @param array $parameters Query parameters
	 * @return Generic_Response the HTTP response from server.
	 * @throws Bluevia_Connector_Exception
	 */
	public function delete ($address,$parameters=array()){

		$this->_setParameters(self::DELETE,$address);
		$request=$this->_newHTTPRequest($parameters);
		$response=$this->_sendRequest($request);
		return $response;

	}

	/**
	 * Method to check if the HTTP_Connector has a certificate.
	 */
	public function isTrusted(){
		return (!is_null($this->_certificate));
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////
	//
	//  PRIVATE FUNCTIONS
	//
	//////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Helper function for setting HTTP_Connector properties
	 * @param String $method Request method
	 * @param string $address Request URL
	 * @param string $headers Body Content-Type
	 * @param string $content Request content
	 */
	private function _setParameters ($method,$address,$headers=null,$content=null){
		Utils::checkParameter($method,$address);
		$this->_method = $method;
		$this->_address=$address;
		$this->_content=$content;
		$this->_headers=$headers;
	}

	/**
	 * Helper function to create an authenticated instance of HTTP_Request2
	 * @param array() $parameters Query params
	 * @return HTTP_Request2
	 */
	private function _newHTTPRequest($parameters){
		$query_params =Utils::build_URLEncoded_query($parameters);
		if (!empty($query_params)) $this->_address .='?'.$query_params;
		$sslOptions=array ('ssl_verify_peer'   => false,
               'ssl_verify_host'   => true
		,
				'ssl_cafile'=> dirname(__FILE__) . DIRECTORY_SEPARATOR. "certificate.crt");
		if ($this->_certificate){
			$sslOptions['ssl_local_cert']=$this->_certificate;
			$sslOptions['ssl_passphrase']=$this->_fileCertPass;
		}
		$request = new HTTP_Request2($this->_address,$this->_method,$sslOptions);
		return $this->_setHeaders($request);
	}

	/**
	 * Helper function for setting the headers in the HTTP_Request2 object
	 * @param HTTP_Request2 $request The request to be set
	 * @return HTTP_Request2 with headers
	 */
	private function _setHeaders($request){
		if (!is_array($this->_headers)){
			$this->_headers=array($this->_headers);
		}
		$this->_headers[]=$this->authenticate();
		foreach($this->_headers as $header){
			$request->setHeader($header);
		}
		return $request;
	}

	/**
	 * Helper function to set the request content.
	 * @param [array|string] $request
	 * @return HTTP_Request2 with the content set up
	 */
	private function _setBody($request){
		if (is_array($this->_content)) {
			foreach ($this->_content as $key => $value){
				$request->addUpload($key,$value[0],$value[1],$value[2]);
			}
		} else {

			if (!empty($this->_content)){
				$request->setBody($this->_content);
			}
		}
		return $request;
	}

	/**
	 * Helper function for sending the request
	 * @param HTTP_Request2 $request The request to be sent
	 * @return Generic_Response
	 * @throws Bluevia_Exception, Connector_Exception
	 */
	private function _sendRequest($request){
		try{
			$response=$request->send();
		}
		catch (HTTP_Request2_ConnectionException $e){
			throw new Bluevia_Exception('-2');
		}
		if (isset($response)){
			if ($response->getStatus()<300){
				return new Generic_Response($response->getStatus(),
				$response->getReasonPhrase(),
				array('content'=>$response->getBody(),
						'headers'=>$response->getHeader()));
			}
			else if ($response->getStatus()==301 || $response->getStatus()==302 || $response->getStatus()==303){
				$request->setUrl($response->getHeader('Location'));
				$this->_sendRequest($request);
			}
			else if ($response->getStatus()<500){
				throw new Connector_Exception($response->getReasonPhrase(),
				$response->getStatus(),
				array('content'=>$response->getBody(),
						'headers'=>$response->getHeader())
				);
			}
			else{
				throw new Connector_Exception($response->getReasonPhrase(),
				$response->getStatus(),
				array('content'=>$response->getBody(),
						'headers'=>$response->getHeader()));
			}
		}

	}


}