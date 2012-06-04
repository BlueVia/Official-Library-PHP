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
 * Client interface for the REST binding of the Bluevia Location Service.
 *
 * @author Telefonica R&D
 * 
 */
class BV_Location extends BV_Location_Client{


	/**
	 * Location API constructor
	 * @param BV_Mode $mode BlueVia provides three modes to support the different
	 * 						development stages of your app. [LIVE,TEST,SANDBOX]
	 * @param String $consumer The string identifying the application- you obtained when you registered your application within the provisioning portal.
	 * @param String $consumerSecret A secret -a string- used by the consumer to establish ownership of the consumer key.
	 * @param String $token The token -a string- used by the client for granting access permissions to the server.
	 * @param String $tokenSecret The secret of the access token.
	 * @throws Bluevia_Exception
	 */
	public function __construct($mode,$consumer,$consumerSecret,$token,$tokenSecret){
		$this->checkThreeLeggedCredentials($consumer,$consumerSecret,$token,$tokenSecret);
		parent::initUntrusted($mode,$consumer,$consumerSecret,$token,$tokenSecret);
		$this->setParameters();

	}
	
	/**
	 * Retrieves the user's location.
	 * 
	 * @param String|null $accuracy (optional) Accuracy in meters, that is acceptable for a response.
	 * @return Location_Info The user's location information.
	 * @throws Bluevia_Exception 
	 */
	public function getLocation($accuracy=null){
		return parent::getLocation($accuracy);
	}
}
