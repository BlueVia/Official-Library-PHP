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
 * Client interface for the REST binding of the Bluevia Trusted SMS MT Service.
 *
 * @author Telefonica R&D
 *
 */
class BV_MtSms extends BV_MtSms_Client{


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
		$this->_setParameters();

	}

	/**
	 * Enables your application to send an MMS on behalf of the user.  It returns a String containing the mmsId of the MMS sent
	 * The SMSID of the sent SMS is returned in order to ask later for the status of the message as well.
	 * The max length of the message must be less than 160 characters.
	 * @param String $destination the address of the recipient of the message
	 * @param String $text the text of the message
	 * @param Strng|null $endpoint (Optional) the endpoint to receive notifications of sent SMSs. 
	 * @param String|null $correlator (Optional) An application generated identifier for the SMS. Mandatory if $endpoint is included.
	 * This identifier will be included in the delivery status notifications for your application to correlate notifications with SMS.
	 *
	 * @return String. the sent SMS ID
	 * @throws Bluevia_Exception
	 */
	public function send($destination,$text,$endpoint=null,$correlator=null){
		return parent::send($destination,$text,$endpoint,$correlator);
	}


}
