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
 * Class to parse RPC HTTP body responses.
 * @author Telefonica R&D
 *
 */
class RPC_Parser extends Json_Parser{
	
	/**
	 * @param String $byteArray The string to be parsed.
	 * @return stdClass standard PHP class with the parsed information of the $byteArray param 
	 */
	public function parse($byteArray){
		$newEntity=parent::parse($byteArray);
		if (isset($newEntity->methodResponse->result)){
			return $newEntity->methodResponse->result;
		}
		else{
			if (isset($newEntity->methodResponse->error->message)){
				$extraData='';
				if (isset($newEntity->methodResponse->error->data)){
					$extraData=$newEntity->methodResponse->error->data;
				}
				return $newEntity->methodResponse->error->message.'. '.$extraData;
			}
			return '';
		}
	}
}