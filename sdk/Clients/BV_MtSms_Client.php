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
 * Abstract client for the REST binding of the Bluevia Sms Mt Service.
 *
 * @author Telefonica R&D
 *
 */
abstract class BV_MtSms_Client extends BV_Mt_Client{

	/**
	 * Enables your application to send an MMS on behalf of the user.  It returns a String containing the mmsId of the MMS sent
	 * @param String $destination the address of the recipient of the message
	 * @param String $text the text of the message
	 *  @param Strng|null $endpoint (Optional) the endpoint to receive notifications of sent SMSs.
	 * @param String|null $correlator (Optional) An application generated identifier for the SMS. Mandatory if $endpoint is included.
	 * This identifier will be included in the delivery status notifications for your application to correlate notifications with SMS.
	 * @param String|null $userId (Optional) Telefonica's customer phone number who will appear as the sms sent origin address. (Only for partner's SDK)
	 * @param String|null $senderName (Optional). Human-readable text for mms remitent. (Only for partner's SDK)
	 * @param String|null $apiPayer (Optional). Indicates who will pay for the sms. If not indicated, the mms payer will be the
	 * sender of mms (Only for partner's SDK)
	 * @return String containing the sms sent ID.
	 * @throws Bluevia_Exception
	 */
	protected function send($destination,$text,$endpoint=null,$correlator=null,
	$userId=null,$senderName=null,$apiPayer=null){
		Utils::checkParameter(array('$text'=>$text));
		$body['smsText']=$this->_send($destination,$endpoint,$correlator,$userId,$senderName);
		$body['smsText']['message'] = $text;
		$headers=$this->_createHeaders($apiPayer,BV_Constants::JSON);
		$this->baseCreate(null,null,$body,$headers);
		return $this->_getDeliveryStatusId();
	}

	/**
	 * Helper function for setting the Client properties
	 */
	protected function _setParameters (){
		$this->_rewriteUrl(BV_Constants::REST_URL.BV_Constants::SMS_URL.BV_Constants::MT_URL);
		$this->_parser= new Json_Parser();
		$this->_serializer= new Json_Serializer();
	}

	/**
	 * Simplify the server's response to the BV_Mt_Client::getDeliveryStatus method in sms into a more useful object
	 * @param stdClass $content a standard object with all the infomation returned from the server.
	 * @return Delivery_Info Object containing the useful information from the server
	 */
	protected function _simplifyDelResponse($response) {
		$status=$response->smsDeliveryStatus->smsDeliveryStatus;
		$info=$this->_createDeliveryInfo($status);
		return $info;
	}

}
