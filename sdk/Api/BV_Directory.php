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
 * Client interface for the REST binding of the Bluevia Directory Service.
 *
 * @author Telefonica R&D
 * 
 */
class BV_Directory extends BV_Directory_Client{

	/**
	 * Directory API constructor
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
	 * Allows an application to get all the user context information. Applications
	 * will only be able to retrieve directory information on themselves.
	 * Information blocks can be filtered using the data set.
	 *
	 * @param array|null $dataSet (Optional) array of Directory_Data_Sets constants (the blocks to be retrieved). 
	 *
	 * @return User_Info object containing the blocks of user context information you've selected.
	 * @throws Bluevia_Exception
	 */


	public function getUserInfo($dataSet=null){
		return parent::getUserInfo($dataSet);
	}

	/**
	 * Retrieves a subset of the User Personal Information resource block from the directory. Applications
	 * will only be able to retrieve directory information on themselves.
	 *
	 * @param array|null $filter (Optional) array of Personal_Fields constants. A filter object to specify which information fields are required.
	 * 					If not included this function will return all fields.
	 * @return Personal_Info object containing the user terminal information
	 * @throws Bluevia_Exception
	 */
	public function getPersonalInfo($fields=null){
		return parent::getPersonalInfo($fields);
	}

	/**
	 * Retrieves User Profile resource block from the directory. Applications
	 * will only be able to retrieve directory information on themselves.
	 *
	 * @param array|null $filter (Optional) array of Profile_Fields constants. A filter object to specify which information fields are required.
	 * 					If not included this function will return all fields.
	 * @return Profile_Info object containing the user terminal information
	 * @throws Bluevia_Exception
	 */
	public function getProfileInfo($fields=null){
		return parent::getProfileInfo($fields);

	}
	
	
	/**
	 * Retrieves User Access Information resource block from the directory. Applications
	 * will only be able to retrieve directory information on themselves.
	 * 
	 * @param array|null $filter (Optional) array of Access_Fields constants. A filter object to specify which information fields are required.
	 * 					If not included this function will return all fields. 
	 * @return Access_Info object containing the user terminal information
	 * @throws Bluevia_Exception
	 */
	public function getAccessInfo($fields=null){
		return parent::getAccessInfo($fields);
			
	}
	
	/**
	 * Retrieves User Terminal Information resource block from the directory. Applications
	 * will only be able to retrieve directory information on themselves.
	 * 
	 * @param array|null $filter (Optional) array of Terminal_Fields constants. A filter object to specify which information fields are required.
	 * 					If not provided this function will return all fields. 
	 * @return Terminal_Info object containing the user terminal information
	 * @throws Bluevia_Exception
	 */
	public function getTerminalInfo($fields=null){
		return parent::getTerminalInfo($fields);

	}
	
	/**
	 * Helper function for instantiate the Client parameters
	 */
	protected function setParameters(){
		$this->_rewriteUrl(BV_Constants::REST_URL.BV_Constants::DIR_URL);
		$this->_url=str_replace('%ID%','alias:'.$this->_connector->getToken(),$this->_url);
		parent::setParameters();
	}



}
