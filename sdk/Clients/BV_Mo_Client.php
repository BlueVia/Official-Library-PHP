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
 * Abstract client for the REST binding of the Bluevia Mo Service.
 *
 * @author Telefonica R&D
 *
 */
abstract class BV_Mo_Client extends BV_Base_Client{

	/**
	 * Unsuscribe your application, to stop recieving notifications.
	 * @param String $correlator The identifier returned by the startNotification method.
	 * @return boolean. True if the application was unsuscribed successfully.
	 * @throws Bluevia_Exception
	 */
	public function stopNotification($correlator){
		Utils::checkParameter(array('$correlator'=>$correlator));
		try{
			$response=$this->baseDelete(BV_Constants::NOT_URL.'/'.$correlator);
			return true;
		}
		catch (Exception $e){
			var_dump($e);
		}
	}

	/**
	 * Receive notifications about messages sent to your applications.
	 * @param String $phoneNumber Short number corresponding to the user's country, including the country code.
	 * @param String $endpoint URI where your application is expecting to receive the message notifications. (Only https)
	 * @param String $criteria The keyword you chose for your application when you requested the API key.
	 * @param String|null $correlator (Optional) Identifier for the subscription. If not provided this method will generate a random identifier.
	 * @return String the provided $correlator or the one generated.
	 * @throws Bluevia_Exception
	 */
	protected function startNotification($phoneNumber,
	$endpoint,
	$criteria,
	$correlator=null){
		// check required paramters
		Utils::checkParameter(
		array('$phoneNumber'=>$phoneNumber,
                	'$endpoint'=>$endpoint, 
                	'$criteria'=>$criteria) );

		if (strlen($correlator)>20)
		throw new Bluevia_Exception('-109',null,'$correlator','20 characters')	;
		if (!isset($correlator))
		$correlator=Utils::generateRandomIdentifier(15);

		$structure['reference']['endpoint'] =  $endpoint;
		$structure['reference']['correlator'] = $correlator;
		$structure['destinationAddress'] = array(array('phoneNumber'=>$phoneNumber));
		$structure['criteria'] = $criteria;


			
		$structure=$this->setNotificationName($structure);
		$response=$this->baseCreate(BV_Constants::NOT_URL,null,$structure,'Content-type: '.BV_Constants::JSON);
		return $this->_getCorrelatorID();
	}
	
	/**
	 * Common functionality in sms's and mms's getAllMessages function. Makes the request.
	 * @param String $registrationId Short number corresponding to the user's country (including the country code without the + symbol).
	 * @param String $params Query params ($attachUrl)
	 * @return Mms_Message_Info or Sms_Message depending on the requested message sms or mms.
	 */
	protected function getReceivedMessages($registrationId,$params=null){
		$url=$this->composeGetMessagesURL($registrationId);
		$response=$this->baseRetrieve($url,$params);
		return $this->simplifyGetAllMessages($response);

	}


	/**
	 * Helper function to compose the URL for the BV_MoMms_Client::getAllMessages method. 
	 * @param String $registrationId Short number corresponding to the user's country (including the country code without the + symbol).
	 * @return String Partial url for the getMessages URL. 
	 */
	protected function composeGetMessagesURL($registrationId){
		Utils::checkParameter(array('$registrationId'=>$registrationId));
		return '/'.$registrationId.BV_Constants::MSGS_URL;
	}

	/**
	 * Simplifies server's response of the BV_MoMms_Client::getAllMessages method into a more useful object
	 * @param stdClass $response Parsed Bluevia's response.
	 * @param String $keyword Name of the internal structure the $response will have depending on wich message you requested (sms or mms)
	 * @return Mms_Message_Info or Sms_Message depending on the requested message sms or mms.
	 */
	protected function simplifyGetAllMessages($response,$keyword){
		if (is_string($response)){
			throw new Exception();
		}
		$response=$response->$keyword->$keyword;
		if (!is_array($response)){
			$response=array($response);
		}
		return $response;
	}

	/**
	 * Abstract function for setting the notification's body wrapper depending on wich notification you are requesting (sms or mms)
	 * @param String $name Sms's or Mms's notification wrapper.
	 */
	abstract protected function setNotificationName($name);

	/**
	 * Setter for the BV_Message information.
	 * @param stdClass $response Parsed response from Bluevia's server.
	 * @param Mms_Message_Info|Sms_Message $message Object where the information will be set.
	 * @return Mms_Message_Info|Sms_Message with all information set.
	 */
	protected function _setBVMessage($response,$message){
		foreach($response->originAddress as $key => $value){
			$message->originAddress=$value;
		}
		if (isset($response->destinationAddress)){
			$message->destination[]=$response->destinationAddress->phoneNumber;
		}else{
			$destination=array();
			foreach ($response->address as $value){
				$destination[]=$value->phoneNumber;
			}
			$message->destination=$destination;
		}
		if (isset($response->dateTime)){
			$message->dateTime=$response->dateTime;
		}
		return $message;
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////
	//
	//  PRIVATE FUNCTIONS
	//
	//////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Get the correlator Id from location header.
	 */
	private function _getCorrelatorID(){
		$location=$this->_getLocationHeader();
		$id = preg_replace(
                "@^.+".BV_Constants::NOT_URL."/([^/]+)$@",
                '$1',
		$location
		);
		return $id;
	}

}