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
 * Interface that represents the parser. Object implementing this interface
 * will be able to parse HTTP body responses.
 *
 */
interface IParser{
	
	/**
	 * @param String $byteArray The string to be parsed.
	 * @return stdClass standard PHP class with the parsed information of the $byteArray param 
	 */
	public function parse($byteArray);
	
}
