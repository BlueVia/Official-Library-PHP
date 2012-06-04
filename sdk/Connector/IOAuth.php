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
 * Interface to include the functions to implement the complete OAuth workflow
 * 
 * @author Telefonica R&D
 */
interface IOAuth extends IAuth{

	/**
	 * Set the OAuth special parameters (api_name, callback, timestamp)
	 * @param array $parameters Parameters to be set
	 */
	public function setExtraParameters($parameters);
	
	/**
	 * Retrieves the acces token
	 * @return String Access token
	 */
	public function getToken();
	
	/**
	 * Checks if it is a two-legged OAuth
	 */
	public function isTwoLegged();

}