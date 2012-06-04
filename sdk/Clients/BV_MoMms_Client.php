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
 * Abstract client for the REST binding of the Bluevia Mms Mo Service.
 *
 * @author Telefonica R&D
 *
 */
abstract class BV_MoMms_Client extends BV_Mo_Client{

	/**
	 * Json_Parser $_jsonParser Json parser to parse the HTTP's response body
	 */
	private $_jsonParser;
	/**
	 * Multipart_Parser $_multipartParser Multipart parser to parse the HTTP's response body
	 */
	private $_multipartParser;

	/**
	 * Retrieves the list of received messages. Depending on the value of the useAttachmentsIds parameter, the response will
	 * include the IDs of the attachments or not.
	 * If the ids are retrieved, the function 'getAttachment' can be used; otherwise, the attachments must be obtained throught the getMessage function.
	 *
	 * @see getMessage(String $registrationId, String $messageId)
	 * @see getAttachment(String $registrationId, String $messageId, String $attachmentId)
	 *
	 * Note: the origin address of the received MMS will contain an alias, not a phone number.
	 *
	 * @param String $registrationId the registration id (short number) that receives the messages
	 * @param boolean $attachUrl the boolean parameter to retrieve the IDs of the attachments or not. Default to false
	 * @return arrayof Mms_Message_Info the list of Received MMSs (list will be empty if the are no messages)
	 */
	public function getAllMessages ($registrationId,$attachtUrl=false){
		$this->_parser=&$this->_jsonParser;
		$attachtUrl=($attachtUrl) ? 'true' : 'false';
		$response=parent::getReceivedMessages($registrationId,array('useAttachmentURLs'=>$attachtUrl));
		return $response;
	}

	/**
	 * Gets the content of a message with a 'messageId' sent to the 'registrationId'
	 *
	 * @param String $registrationId the registration id (short number) that receives the messages
	 * @param String $messageId the message id (obtained in getAllMessages function)
	 * @return MmsMessage the complete mms message (including attachments)
	 * @throws Bluevia_Exception
	 */
	public function getMessage($registrationId,$messageId){
		$url=$this->_composeGetMessageURL($registrationId,$messageId);
		$this->_parser=&$this->_multipartParser;
		try{
			$response=$this->baseRetrieve($url);
			$message= $this->_simplifyMessage($response);
			$message->mmsInfo->messageId=$messageId;
			return $message;
		}
		catch (Exception $e){
			$this->_parseError($e,$this->_jsonParser);
		}
	}

	/**
	 *
	 * Gets the attachment with the specified id of the received message.
	 *
	 * @param String $registrationId the registration id (short number) that receives the messages
	 * @param String $messageId the message id (obtained in getAllMessages function)
	 * @param String $attachmentId the attachment id (obtained in getAllMessages function)
	 * @return Mime_Content the attachment of the received MMS.
	 * @throws Bluevia_Exception
	 */
	public function getAttachment($registrationId,$messageId,$attachmentId){
		$url=$this->_composeGetMessageURL($registrationId,$messageId);
		Utils::checkParameter(array('$attachmentId'=>$attachmentId));
		$url.=BV_Constants::ATT_URL.'/'.$attachmentId;
		$this->_parser=null;
		try{
			$response=$this->baseRetrieve($url);
			return $this->_simplifyGetAttachmentResponse($response);
		}
		catch (Exception $e){
			$this->_parseError($e,$this->_jsonParser);
		}
	}

	/**
	 * Receive notifications about SMS sent to your applications.
	 * @param String $phoneNumber element containing the short number corresponding to the user's country, including the country code.
	 * @param String $endpoint element with the URI where your application is expecting to receive the SMS notifications. (Only https)
	 * @param String $criteria element containing the keyword you chose for your application when you requested the API key.
	 * @param String|null $correlator (Optional) element containing application generated identifier for the subscription. If not provided this method will generate a random identifier.
	 * @return String the provided $correlator or the one generated.
	 * @throws Bluevia_Exception
	 */
	public function startNotification($phoneNumber,
	$endpoint,
	$criteria,
	$correlator=null){
		$this->_parser=$this->_jsonParser;
		return parent::startNotification($phoneNumber,$endpoint,$criteria,$correlator);
	}

	/**
	 * Set the wrapper for the body sent in the BV_MoMms_Client::startNotification method.
	 * @param String $name Value for the sms notification wrapper.
	 * @return array with the body included the Mms special wrapper
	 */
	protected function setNotificationName($name){
		return array('messagesNotification'=>$name);
	}

	/**
	 * Helper function for setting the Client properties
	 */
	protected function setParameters (){
		$this->_rewriteUrl(BV_Constants::REST_URL.BV_Constants::MMS_URL.BV_Constants::MO_URL);
		$this->_jsonParser = new Json_Parser();
		$this->_multipartParser = new Multipart_Parser();
		$this->_serializer=new Json_Serializer();
	}

	/**
	 * Simplify the server's response to the BV_MoMms_Client::getAllMessages method into a more useful object
	 * @param stdClass $content a standard object with all the infomation returned from the server.
	 * @return Mms_Message_Info Object containing the useful information from the server
	 */
	protected function simplifyGetAllMessages($response){
		try{
			$response = parent::simplifyGetAllMessages($response,'receivedMessages');
		}catch(Exception $e){
			return array();
		}
		foreach($response as $key => $value){
			$receivedMMS[$key]= $this->_setBVMessage($value,new Mms_Message_Info());
			$receivedMMS[$key]->messageId=$value->messageIdentifier;
			$receivedMMS[$key]->subject=$value->subject;
			$attachmentInfo=array();
			if (isset($value->attachmentURL)){
				foreach($value->attachmentURL as $attKey => $attValue){
					$attachmentInfo[$attKey]=new Attachment_Info();
					$attachmentInfo[$attKey]->url=$attValue->href;
					$attachmentInfo[$attKey]->contentType=$attValue->contentType;
				}
			}
			$receivedMMS[$key]->attachmentInfo=$attachmentInfo;

		}
		return $receivedMMS;
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////
	//
	//  PRIVATE FUNCTIONS
	//
	//////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Simplify the server's response to the BV_MoMms_Client::getMessage method into a more useful object
	 * @param stdClass $content a standard object with all the infomation returned from the server.
	 * @return Mms_Message Object containing the useful information from the server
	 */
	private function _simplifyMessage($body){
		$message= new Mms_Message();
		$message->attachments=$body->attachments;
		$body=$body->message->message;
		$message->mmsInfo= $this->_setBVMessage($body,new Mms_Message_Info());
		$message->mmsInfo->subject=$body->subject;
		return $message;
	}

	/**
	 * Helper function to compose the URL for the BV_MoMms_Client::getMessage method.
	 * @param String $registrationId Short number corresponding to the user's country (including the country code without the + symbol).
	 * @return String Partial url for the getMessages URL.
	 */
	private function _composeGetMessageURL($registrationId,$messageId){
		$url=$this->composeGetMessagesURL($registrationId);
		Utils::checkParameter(array('$messageId'=>$messageId));
		return $url.'/'.$messageId;

	}
	
	/**
	 * Simplify the server's response to the BV_MoMms_Client::getAllAttachment method into a more useful object
	 * @param stdClass $content a standard object with all the infomation returned from the server.
	 * @return Mime_Content Object containing the useful information from the server
	 */
	private function _simplifyGetAttachmentResponse($content){
		$attachment= new Mime_Content();
		$attachment->content=$content;
		$response = $this->_genericResponse->getAdditionalData();
		$response=$response['headers']['content-type'];
		$response=explode(';',$response);
		$attachment->contentType=$response[0];
		$attachment->name=$response[1];
		$attachment->encoding=$response[2];
		return $attachment;
	}

}