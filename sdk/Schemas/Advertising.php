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
 * Class to indicate the adult control policy for your advertising.
 *
 */
class Protection_Policy extends Enumerated{
	/**
	 * Low, moderately explicit content (I am youth; you can show me moderately explicit content).
	 */
	const LOW = '1';
	/**
	 * Safe, not rated content (I am a kid, please, show me only safe content).
	 */
	const SAFE = '2';
	/**
	 * High, explicit content (I am an adult; I am over 18 so you can show me any content including very explicit content).
	 */
	const HIGHT = '3';
}

/**
 * Class representing the advertising format you are requesting.
 *
 */
class Ad_Presentation extends Enumerated{
	
	/**
	 * Text advertising.
	 */
	const TEXT = '0104';
	/** 
	 * Image advertising.
	 */
	const IMAGE = '0101';
}

class Creative_Element {

	/**
	 *  string $type. The advertising type, you have requested. (Text | Image)
	 */
	public $type;
	/**
	 *  string $value It's the part of the ad that the user sees.
	 */
	public $value;
	
	/**
	 *  string $interaction It's the URL the ad is related to (the advertiser site). 
	 */
	public $interaction;
}

class Ad_Response {
	/**
	 *  Creative_Element $creativeElement. The advertising information
	 */
	public $creativeElement;
	/**
	 *  string $id. The advertising ID.
	 */
	public $id;
	
	/**
	 * Constructor
	 * @param String $id The advertising Id
	 * @param Creative Element $creativeElement. The advertising information
	 */
	public function __construct($id,$creativeElement){
		Utils::checkParameter(array('$id'=>$id,'$creativeElement'=>$creativeElement));
		if (!($creativeElement instanceof Creative_Element)){
			throw new Bluevia_Exception('-106',null,'$creativeElement','Creative_Element');
		}
		$this->id=$id;
		$this->creativeElement=$creativeElement;
		
	}
}
