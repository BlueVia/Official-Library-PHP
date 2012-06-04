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
 * Abstract client for the REST binding of the Bluevia Mt Service.
 *
 * @author Telefonica R&D
 *
 */
abstract class BV_Mt_Client extends BV_Base_Client{


	/**
	 *
	 * Allows to know the delivery status of a previous sent message using Bluevia API
	 *
	 * @param String $messageId the id of the message previously sent using this API
	 * @return array of Delivery_Info for each destination address from the sent message.
	 * @throws Bluevia_Exception
	 *
	 */
	public function getDeliveryStatus($messageId){
		Utils::checkParameter(array('$messageId'=>$messageId));
		try{
			$response = $this->baseRetrieve('/'.$messageId.BV_Constants::STATUS_URL);
			return $this->_simplifyDelResponse($response);
		}
		catch (Exception $e){
			throw $e;
		}
	}

	/**
	 * Get's the message Id from the location header in the send sms response or in send mms response. This Id is required in the BV_Mt_Client::getDeliveryStatus method
	 */
	protected function _getDeliveryStatusId (){
		$location=$this->_getLocationHeader();
		$id = preg_replace(
                '@^.+.'.BV_Constants::MT_URL.'/([^/]+)'.BV_Constants::STATUS_URL.'$@',
                '$1',
		$location
		);
		return $id;
	}

	/**
	 * Simplify the server's response to the BV_Mt_Client::getDeliveryStatus method into a more useful object
	 * @param stdClass $status Standard object with all the infomation returned from the server.
	 * @return Delivery_Info Object containing the useful information from the server
	 */
	protected function _createDeliveryInfo($status){
		$info=array();
		foreach($status as $key => $value){
			$info[$key] = new Delivery_Info();
			$info[$key]->destination=$value->address->phoneNumber;
			$info[$key]->status=$value->deliveryStatus;
			if (isset($value->description)){
				$info[$key]->statusDescription=$value->description;
			}else{
				if (Status::isValid($value->deliveryStatus)){
					$info[$key]->statusDescription=Status::$descriptions[$value->deliveryStatus];
				}
			}
		}
		return $info;
	}

	/**
	 * Helper function with common functionality of the sending methods (Sms and Mms send)
	 * @param String $destination Phone number where the message will be sent.
	 * @param Strng|null $endpoint (Optional) the endpoint to receive notifications of sent SMSs.
	 * @param String|null $correlator (Optional) An application generated identifier for the SMS. Mandatory if $endpoint is included.
	 * @param String|null $userId Telefonica's customer phone number who will appear as the sms sent origin address. (Only for partner's SDK)
	 * @param String|null $senderName (Optional). Human-readable text for mms remitent. (Only for partner's SDK)
	 * @return array the body to be sent in the BV_MtSms::send and the BV_MtMms::send request.
	 */
	protected function _send($destination,$endpoint=null,$correlator=null,$userId=null, $senderName=null){
		Utils::checkParameter(array('$destination'=>$destination));
		$to=$this->_addRecipient($destination);
		// fills JSON message body
		if (!empty($userId)){
			$originAddress=array('phoneNumber'=>$userId);
		}
		else{
			$originAddress=array( 'alias' => $this->_connector->getToken());
		}
		$body = array(
                'originAddress' => $originAddress,
                'address' => $to,                
		);
		if (!empty($senderName)){
			$body['senderName']=$senderName;
		}
		$endpoint=$this->_checkEndpoint($endpoint,$correlator);
		if (!empty($endpoint)){
			$body['receiptRequest']=$endpoint;
		}
		return $body;
	}

	/**
	 * Helper function to create the BV_MtSmsClient::send and BV_MtMmsClient::send method headers.
	 * @param String|null $apiPayer (Optional). Indicates who will pay for the sms. If not indicated, the mms payer will be the
	 * sender of mms. (Only for Partner's SDK)
	 * @param String|null $contentType The HTTP's body content-type.
	 * @return array containing the headers to be sent in the BV_MtSms::send and the BV_MtMms::send request.
	 */
	protected function _createHeaders($apiPayer=null,$contentType=null){
		$headers=array();
		if (!empty($contentType)){
			$headers[]='Content-type: '.$contentType;
		}
		if (!empty($apiPayer)){
			$headers[]='X-ChargedId: '.$apiPayer;
		}
		return $headers;
	}
	
	/**
	 * Abstract function to simplify the server's response to the BV_Mt_Client::getDeliveryStatus method into a more useful object
	 * @param stdClass $content a standard object with all the infomation returned from the server.
	 */
	abstract protected function _simplifyDelResponse($response);

	//////////////////////////////////////////////////////////////////////////////////////////////////
	//
	//  PRIVATE FUNCTIONS
	//
	//////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Creates an array of destinations with the necessary format for sending sms and mms.
	 * @param array $destination  Array of String. This strings are the destination phone numbers
	 */
	private function _addRecipient($destination)
	{
		if (!is_array($destination)){
			$destination=array($destination);
		}
		$to=array();
		foreach ($destination as $key => $value){
			Utils::isPhoneNumber($value);
			$to[] = array('phoneNumber' => $value);
		}
		return $to;
	}
}
