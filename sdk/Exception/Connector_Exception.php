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
 * This class encapsulates errors returned by the REST server
 *
 */
class Connector_Exception extends Exception
{
/**
 * 
 *  array $_additionalData response headers
 */
	protected $_additionalData;
	
	/**
	 * Constructor
	 * @param String $message Exception message
	 * @param String $code Exception status
	 * @param array $additionalData Exception headers
	 */
	public function __construct($message, $code, $additionalData){
		$this->_additionalData=$additionalData;
		parent::__construct($message,$code);
	}
	
	/**
	 * Get the additionalData parameter.
	 * @return array of headers.
	 */
	public function getAdditionalData(){
		return $this->_additionalData;
	}
	
	
}
