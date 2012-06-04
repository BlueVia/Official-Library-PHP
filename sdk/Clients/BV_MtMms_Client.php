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
 * Abstract client for the REST binding of the Bluevia Mms Mt Service.
 *
 * @author Telefonica R&D
 *
 */
abstract class BV_MtMms_Client extends BV_Mt_Client
{

	/**
	 * Enables your application to send an MMS on behalf of the user.  It returns a String containing the mmsId of the MMS sent
	 * @param String $destination the address of the recipient of the message
	 * @param String $subject the subject of the mms to send
	 * @param String|null $message (Optional) Text message of the MMS
	 * @param array|null $attachments (Optional) array of Attachment. The attachments your application will send
	 * @param Strng|null $endpoint (Optional) the endpoint to receive notifications of sent MMSs.
	 * @param String|null $correlator (Optional) An application generated identifier for the MMS. Mandatory if $endpoint is included.
	 * This identifier will be included in the delivery status notifications for your application to correlate notifications with MMS.
	 * @param String|null $userId (Optional) Telefonica's customer phone number who will appear as the sms sent origin address. (Only for partner's SDK)
	 * @param String|null $senderName (Optional). Human-readable text for mms remitent. (Only for partner's SDK)
	 * @param String|null $apiPayer (Optional). Indicates who will pay for the sms. If not indicated, the mms payer will be the
	 * sender of mms (Only for partner's SDK)
	 *
	 *
	 * @return String the sent MMS ID
	 * @throws Bluevia_Exception
	 */
	protected function send($destination,$subject,$message=null,$attachments=null,$endpoint=null,$correlator=null,
	$userId=null,$senderName=null,$apiPayer=null){
		Utils::checkParameter(array('$subject'=>$subject));
		$content['message']=$this->_send($destination,$endpoint,$correlator,$userId,$senderName);
		// add optional paramters
		$content['message']['subject'] = $subject;

		$body['root_fields']=$content;
		if (!empty($message)){
			$body['message']=$message;
		}
		if (!empty($attachments)){
			$body['files']=$this->_addFiles($attachments);
		}
		$headers=$this->_createHeaders($apiPayer);
		$this->baseCreate(null,null,$body,$headers);
		return $this->_getDeliveryStatusId();
	}

	/**
	 * Helper function for setting the Client properties
	 */
	protected function setParameters (){
		$this->_rewriteUrl(BV_Constants::REST_URL.BV_Constants::MMS_URL.BV_Constants::MT_URL);
		$this->_serializer= new Multipart_Serializer();
		$this->_parser= new Json_Parser();
	}

	/**
	 * Simplify the server's response to the BV_Mt_Client::getDeliveryStatus method in mms into a more useful object
	 * @param stdClass $content a standard object with all the infomation returned from the server.
	 * @return Delivery_Info Object containing the useful information from the server
	 */
	protected function _simplifyDelResponse($response) {
		$status=$response->messageDeliveryStatus->messageDeliveryStatus;
		$info=$this->_createDeliveryInfo($status);
		return $info;
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////
	//
	//  PRIVATE FUNCTIONS
	//
	//////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Adds files to be attached in the Mms
	 * @param array|Attachment $files Array of Attachment the attachments to be added to the mms
	 * @return array containing the valid format for the parsing it with the multipart parser.
	 * @throws Bluevia_Exception if the array don't contain Attachment objects.
	 */
	private function _addFiles($files)
	{
		if (!is_array($files)){
			$files=array($files);
		}
		foreach($files as $key => $file){
			if (!$file instanceof Attachment){
				throw new Bluevia_Exception('-114',null, '$attachments['.$key.']','Attachment');
			}
			$checkedFiles[] = array('path' => $file->attachment, 'mimetype' => $file->type);
		}
		return $checkedFiles;
	}

}
