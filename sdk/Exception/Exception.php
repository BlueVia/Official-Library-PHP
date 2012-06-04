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
 * Base class for Exceptions
 */
class Bluevia_Exception extends Exception
{
	/**
	 * Constructor
	 * @param String $code Exception status
	 * @param String $message Exception message
	 * @param String $extra_param Specific message
	 * @param String $extra_message Specific message
	 */
	public function __construct($code,$message=null,$extra_param=null,$extra_message=null) {
		if (is_null($message)){
			$errorCodes= BV_Constants::$error_codes;
			$message=$errorCodes[$code][0];
			$message=str_replace('%EXC_PARAM%',$extra_param,$message);
			if ($extra_message) $message=$message.' '.$extra_message;
			if ($code < -100 && $code!=-115)
			$code=$errorCodes[$code][1];
		}
		parent::__construct($message,$code);
			
	}


}