<?php
/**
 *
 * @category    opentel
 * @package     com.bluevia
 * @copyright   Copyright (c) 2010-2011 Telefónica I+D (http://www.tid.es)
 * @author      Bluevia <support@bluevia.com>
 * @version     1.0
 *
 * BlueVia is a global iniciative of Telefonica delivered by Movistar and O2.
 * Please, check out http://www.bluevia.com and if you need more information
 * contact us at support@bluevia.com
 */


/**
 * Abstract client for the REST binding of the Bluevia Location Service.
 *
 * @author Telefonica R&D
 *
 */
abstract class BV_Location_Client extends BV_Base_Client {


	/**
	 * The Application invokes getLocation to Retrieve the terminal location
	 *
	 * @param String $accuracy Accuracy that is acceptable for a response. Accuracy is expressed in meters.
	 * @param String|null $phoneNumber (Optional) Telefonica's customer phone number. Get's the location if this customer. Only for partner's sdk version.
	 */
	protected function getLocation($accuracy = null,$phoneNumber=null) {

		if (!empty($phoneNumber)){
			$locatedParty='phoneNumber:'.$phoneNumber;
		}
		else{
			$locatedParty='alias:'.$this->_connector->getToken();
		}
		$params = array('locatedParty'=>$locatedParty);

		if(!is_null($accuracy)){
			if($accuracy != ''){
				Utils::isInteger($accuracy,'$accuracy');
				$params['acceptableAccuracy'] = $accuracy;
			}
		}
		$response=$this->baseRetrieve(null,$params);
		return $this->_simplifyResponse($response);

	}

	/**
	 * Helper function for setting the Client properties
	 */
	protected function setParameters (){
		$this->_rewriteUrl(BV_Constants::REST_URL.BV_Constants::LOC_URL);
		$this->_parser = new Json_Parser();
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////
	//
	//  PRIVATE FUNCTIONS
	//
	//////////////////////////////////////////////////////////////////////////////////////////////////
	
	/**
	 * Simplifies the server's response into a more useful object.
	 * @param stdClass $response The server's response, with all information.
	 * @return Location_Info object with only the useful information returned from Bluevia.
	 */
	private function _simplifyResponse($response){
		$locResponse=new Location_Info();
		$locResponse->reportStatus = $response->terminalLocation->reportStatus;
		$locResponse->coordinatesLatitude = $response->terminalLocation->currentLocation->coordinates->latitude;
		$locResponse->coordinatesLongitude = $response->terminalLocation->currentLocation->coordinates->longitude;
		$locResponse->accuracy=$response->terminalLocation->currentLocation->accuracy;
		$locResponse->timestamp=$response->terminalLocation->currentLocation->timestamp;
		return $locResponse;
	}



}
