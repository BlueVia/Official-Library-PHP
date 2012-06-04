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
 * Client interface for the REST binding of the Bluevia MMS MO Service.
 *
 * @author Telefonica R&D
 *
 */
class BV_MoMms extends BV_MoMms_Client{

		/**
	 * Mms MO API Constructor
	 * @param BV_Mode $mode BlueVia provides three modes to support the different
	 * 						development stages of your app. [LIVE,TEST,SANDBOX]
	 * @param String $consumer The string identifying the application- you obtained when you registered your application within the provisioning portal.
	 * @param String $consumerSecret A secret -a string- used by the consumer to establish ownership of the consumer key.
	 * @throws Bluevia_Exception
	 */
	public function __construct($mode,$consumer,$consumerSecret){
		$this->checkTwoLeggedCredentials($consumer,$consumerSecret);
		parent::initUntrusted($mode,$consumer,$consumerSecret);
		$this->setParameters();
	}



}
