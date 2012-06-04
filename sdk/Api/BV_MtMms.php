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
 *
 * Client interface for the REST binding of the Bluevia MMS MT Service.
 *
 * @author Telefonica R&D
 *
 */
class BV_MtMms extends BV_MtMms_Client{


	/**
	 * Sms MT API constructor
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
	 * Enables your application to send an MMS on behalf of the user.  It returns a String containing the mmsId of the MMS sent
	 * @param String $destination the address of the recipient of the message 
	 * @param String $subject the subject of the mms to send
	 * @param String|null $message (Optional) Text message of the MMS 
	 * @param array|null $attachments (Optional) array of Attachment. The attachments your application will send
	 * @param Strng|null $endpoint (Optional) the endpoint to receive notifications of sent MMSs. 
	 * @param String|null $correlator (Optional) An application generated identifier for the MMS. Mandatory if $endpoint is included.
	 * This identifier will be included in the delivery status notifications for your application to correlate notifications with MMS.
	 *
	 * @return the sent MMS ID
	 * @throws Bluevia_Exception
	 */
	public function send($destination,$subject,$message=null,$attachments=null,$endpoint=null,$correlator=null){
		return parent::send($destination,$subject,$message,$attachments,$endpoint,$correlator);
	}

}