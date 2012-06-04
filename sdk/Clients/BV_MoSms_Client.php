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
 * Abstract client for the REST binding of the Bluevia Sms Mo Service.
 *
 * @author Telefonica R&D
 *
 */
abstract class BV_MoSms_Client extends BV_Mo_Client{

	/**
	 * Receive notifications about SMS sent to your applications.
	 * @param String $phoneNumber Short number corresponding to the user's country, including the country code.
	 * @param String $endpoint URI where your application is expecting to receive the SMS notifications. (Only https)
	 * @param String $criteria The keyword you chose for your application when you requested the API key.
	 * @param String|null $correlator (Optional) Identifier for the subscription. If not provided this method will generate a random identifier.
	 * @return String the provided $correlator or the one generated.
	 * @throws Bluevia_Exception
	 */
	public function startNotification($phoneNumber,
	$endpoint,
	$criteria,
	$correlator=null){
		return parent::startNotification($phoneNumber,$endpoint,$criteria,$correlator);
	}

	/**
	 *
	 * Allow to request for the list of the received SMSs for the app provisioned and authorized
	 *
	 * @param String $registrationId The Bluevia service number for your country, including the country code without the + symbol
	 * @return array of Sms_Message the list of the received messages
	 * @throws Bluevia_Exception
	 */
	public function getAllMessages($registrationId){
		$response=parent::getReceivedMessages($registrationId);
		return $response;
	}
	
	/**
	 * Simplify the server's response to the BV_MoSms_Client::getAllMessages method into a more useful object
	 * @param stdClass $content a standard object with all the infomation returned from the server.
	 * @return Mms_Message_Info Object containing the useful information from the server
	 */
	protected function simplifyGetAllMessages($response){
		try{
			$response = parent::simplifyGetAllMessages($response,'receivedSMS');
		}catch(Exception $e){
			return array();
		}
		foreach($response as $key => $value){
			$receivedSMS[$key]= $this->_setBVMessage($value,new Sms_Message());
			$receivedSMS[$key]->message=$value->message;
		}
		return $receivedSMS;
	}

	/**
	 * Set the wrapper for the body sent in the BV_MoSms_Client::startNotification method.
	 * @param String $name Value for the sms notification wrapper.
	 * @return array with the body included the Sms special wrapper
	 */
	protected function setNotificationName($name){
		return array('smsNotification'=>$name);
	}

	/**
	 * Helper function for setting the Client properties
	 */
	protected function setParameters (){
		$this->_parser = new Json_Parser();
		$this->_serializer = new Json_Serializer();
		$this->_rewriteUrl(BV_Constants::REST_URL.BV_Constants::SMS_URL.BV_Constants::MO_URL);
	}

}