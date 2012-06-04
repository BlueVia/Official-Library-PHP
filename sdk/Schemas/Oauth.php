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
 * Class representing the oauth tokens.
 * 
 * @author Telefonica R&D
 *
 */
class Token{
	/**
	 * String $key The token key
	 */
	public $key;
	/**
	 * String $secret The token secret
	 */
	public $secret;
}

/**
 * Class containing the authentication URL where the user's aplication must be authorized.
 * 
 * @author Telefonica R&D
 *
 */
final class Request_Token extends Token {
	/**
	 * String $authUrlauthentication URL where the user's aplication must be authorized.
	 */
	public $authUrl;
}
