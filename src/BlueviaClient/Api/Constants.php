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
 */
class BlueviaClient_Api_Constants {    
    const ENVIRONMENT_SANDBOX    = '_Sandbox';
    const ENVIRONMENT_COMMERCIAL = '';

    /** @var string stores base service URL  */
    public static $base_url     = "https://api.bluevia.com/services";
        
    // Important: wrong application environment could provide 'Invalid consumer
    //  key error' or App [X] can not use Api [Y]
    
    /** @var enum ENVIRONMENT_SANDBOX or ENVIRONMENT_COMMERCIAL */
    public static $environment  = self::ENVIRONMENT_COMMERCIAL;
    
    
    // stores oAuth portal 
    public static $oauth_url    = "https://connect.bluevia.com/en/authorise";
    
    //encoding
   	const URL_ENCODED 	= "application/x-www-form-urlencoded";
   	const JSON 			= "application/json";
   	const XML			= "application/xml";
   
}

