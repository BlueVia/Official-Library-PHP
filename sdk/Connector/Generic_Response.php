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
 *  Class that represents the response of the IConnector methods.
 * @author Telefonica R&D
 *
 */
class Generic_Response {
	

	// internal variables
	/** String $_status Response status */
	private $_code;

	/**  String $_body Response body */
	private $_message;

	/**  array $_additionalData Response headers */
	private $_additionalData;
	
	/**
	 * Constructor. Sets $_status, $_body and $_additionalData
	 */
	/**
	 * Constructor.
	 * @param String $code Response status
	 * @param String $status Response body
	 * @param array $additionalData Response headers
	 */
	public function __construct($code,$message,$additionalData){
		$this->_code = $code;
		$this->_message= $message;
		$this->_additionalData = $additionalData;
	}
	
	/**
	 * Returns the status code
	 */
	public function getCode(){
		return $this->_code;
	}

	/**
	 * Returns the body of the response
	 */
	public function getMessage(){
		return $this->_message;
	}
	
	
	 /**
	  * Returns all response headers
	  */
	public function getAdditionalData(){
		return $this->_additionalData;
	}
	
}