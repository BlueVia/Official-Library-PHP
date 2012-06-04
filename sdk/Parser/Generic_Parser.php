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
 * Base class for parsers
 * @author Telefonica R&D
 *
 */
abstract class Generic_Parser implements IParser{

	/**
	 * @param String $byteArray The string to be parsed.
	 */
	public function parse($byteArray){
		if (is_null($byteArray))
		return null;
	}
}	