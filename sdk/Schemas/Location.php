<?php
/**
 *
 * @category    opentel
 * @package     com.bluevia
 * @copyright   Copyright (c) 2010-2011 Telefónica I+D (http://www.tid.es)
 * @author      Bluevia <support@bluevia.com>
 * @version     1.0
 *
 * BlueVia is a global iniciative of Telefonica delivered by Movistar and O2.
 * Please, check out http://www.bluevia.com and if you need more information
 * contact us at support@bluevia.com
 */

/**
 * Class representing the location info.
 *
 * @author Telefonica R&D
 */
class Location_Info{
	/**
	 *  String $reportStatus Element indicating whether the response contains valid location data, or an error has occurred.
	 */
	public $reportStatus;
	/**
	 *  String $coordinatesLatitude the latitude
	 */
	public $coordinatesLatitude;
	/**
	 *  String $coordinatesLongitude the longitude.
	 */
	public $coordinatesLongitude;
	/**
	 *  String accuracy Accuracy of location provided in meters
	 */
	public $accuracy;
	/**
	 *  String Date and time that location was collected
	 */
	public $timestamp;
}
