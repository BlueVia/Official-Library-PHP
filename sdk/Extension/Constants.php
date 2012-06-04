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
 * API Constants
 * @author Telefonica R&D
 *
 */
class BV_Constants {


	const ALT = 'json';

	const VERSION = 'v1';

	/**
	 * Url-encoded Conten-type
	 */
	const URL_ENCODED 	= "application/x-www-form-urlencoded";
	/**
	 * Json Content-type
	 */
	const JSON 			= "application/json;charset=UTF-8";
	/**
	 * XML Content-type
	 */
	const XML			= "application/xml";

	const URL_MODE='';
//	/**
//	* Base URL for all services
//	*/
	const BASE_URL = 'https://api.bluevia.com/services';
	/**
	 * Rest Url for REST APIs (Ad, Sms, Mms, Directory, Location, OAuth)
	 */
	const REST_URL = '/REST';
	/**
	 * RPC url for RPC APIs (Payment)
	 */
	const RPC_URL = '/RPC';
	
	// ADVERTISING API
	
	/**
	 * Advertising base url
	 * ADV_URL
	 */
	const ADV_URL = '/%TRUS%Advertising%ENV%/simple/requests';
	
	//LOCATION API
	
	/**
	 * Location base url
	 * LOC_URL
	 */
	const LOC_URL = '/%TRUS%Location%ENV%/TerminalLocation';
	
	// MESSAGING
	
	/**
	 * MT base url
	 * MT_URL
	 */
	const MT_URL = '/outbound/requests';
		/**
	 * Get Deliery status url
	 * STATUS_URL
	 */
	const STATUS_URL = '/deliverystatus';
	/**
	 * Mo base Url
	 */
	
	const MO_URL = '/inbound';
		/**
	 * Notifications url
	 */
	const NOT_URL= '/subscriptions';
	/**
	 * Mms base url
	 */
	
	const MMS_URL = '/%TRUS%MMS%ENV%';
	/**
	 * Get Messages and Get attachments url
	 */
	const MSGS_URL = '/messages';
	/**
	 * Get Attachment Url
	 */

	const ATT_URL = '/attachments';
	/**
	 * Sms base Url
	 */
	const SMS_URL = '/%TRUS%SMS%ENV%';
	/**
	 * Oauth base Url
	 */
	
	// OAUTH API
	
	const OAUTH_URL = '/Oauth';

	/**
	 * Get Request Token url
	 */
	const REQ_URL = '/getRequestToken';

	/**
	 * Get Access Token url
	 */
	const ACC_URL = '/getAccessToken';

	// DIRECTORY API

	/**
	 * Directory base url
	 */
	const DIR_URL = '/%TRUS%Directory%ENV%/%ID%/UserInfo';
	/**
	 * Payment base Url
	 */
	
	// PAYMENT API
	
	const PAY_URL = '/%TRUS%Payment%ENV%';
	/**
	 * Make a payment Url
	 */
	const PAY_PAY_URL = '/payment';
	/**
	 * Get payment Status url
	 */
	const PAY_STA_URL = '/getPaymentStatus';
	/**
	 * Cancel payment Authorization url
	 */
	const PAY_CAN_URL = '/cancelAuthorization';
	/**
	 * Refund Payment url
	 */
	const PAY_REF = '/refund';




	/**
	 * Authentication URL for the OAuth process. For authorize Test and Sandbox tokens.
	 */
	const AUTH_URL_TEST = "https://bluevia.com/test-apps/authorise";
	/**
	 * Authentication URL for the OAuth process. For authorize Live tokens.
	 */
	const AUTH_URL_LIVE = "https://connect.bluevia.com/en/authorise";

	/**
	 * Error codes and messages.
	 */
	public static $error_codes = array(
			'-101'=>array('Client error: The parameter %EXC_PARAM% cannot be null nor empty.','-1'),
			'-102'=>array('Client error: The parameter %EXC_PARAM% cannot be an array.','-1'),
   			'-103'=>array('Client error: The parameter %EXC_PARAM% cannot be a phone number.','-1'),
   			'-104'=>array('Client error: Wrong mode. You must choose between Live, Sandbox or Test.','-1'),
			'-105'=>array('Client error: Invalid parameter: %EXC_PARAM%.','-1'),
			'-106'=>array('Client error: The parameter %EXC_PARAM% must be ','-1'),
			'-107'=>array('Client error: Duplicate values in %EXC_PARAM%.','-1'),
			'-108'=>array('Client error: %EXC_PARAM% is mandatory.','-1'),
			'-109'=>array('Client error: Invalid %EXC_PARAM% length. Length should be less than ','-1'),
			'-110'=>array('Client error: Use %EXC_PARAM% function for requesting ','-1'),
			'-111'=>array('Client error: %EXC_PARAM% must be a numeric value.','-1'),
			'-112'=>array('Client error: %EXC_PARAM% is required if you want to
            	check the delivery status following a notification strategy','-1'),
			'-113'=>array('Client error: %EXC_PARAM% not supported mimetype.','-1'),
			'-114'=>array('Client error: Parameter %EXC_PARAM% must be an instance of ','-1'),
			'-115'=>array('%EXC_PARAM%'),
			'-2'=>array('Connection error: Unable to connect with endpoint.'),
			'-3'=>array('Not implemented: Function not implemented in current version.'),
			'-4'=>array('Bad encoding: Incoming data must be UTF-8 encoded.'),
			'-5'=>array('Invalid resource path: The path given %EXC_PARAM% is not correct'),
			'-6'=>array('Client error: Parser is mandatory.'),
			'-7'=>array('Invalid mode: %EXC_PARAM% only available on ',
			'-9'=>array("An ISO-3166 country is required in two legged authorization")));

}
/**
 * This class represent the three modes to support the different development environments of your app.
 * @author Telefonica R&D
 *
 */
class BV_Mode extends Enumerated{
	/**
	 * In the Live environment your application uses the real network. 
	 * You will be able to send real transactions to real Movistar, O2 and Vivo customers in the applicable country.
	 */
	const LIVE = 'Live';
	/**
	 * No traffic Generated. 
	 * The Sandbox environment offers you the exact same experience as the Live environment except that no traffic is generated on the live network, meaning you can experiment and play until your heart’s content.
	 */
	const SANDBOX = 'Sandbox';
	/**
	 * In the Test environment your application uses the real network.
	 * The Test mode behave exactly like the Live mode, but the API calls are free of chargue, using a credits system. You are required to have a Movistar, O2 or Vivo mobile number to get this monthly credits.
	 */
	const TEST = 'Test';


}
