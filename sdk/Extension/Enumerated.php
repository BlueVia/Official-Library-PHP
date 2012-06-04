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
 * Abstract class with useful functionality for classes that simulates Enumerated. This classes only contains 
 * constants.
 * @author Telefonica R&D
 *
 */
abstract class Enumerated {

	/**
	 * Get all the contants in the class named $className
	 * @param String $className Class name
	 * @return array with class constants.
	 */
	public static function getValidValues($className){
		$class = new ReflectionClass($className);
		return $class->getConstants();
	}
	
	/**
	 * Check if the $enum param is a constant name or a constant value.
	 * @param String $enum constant name or value
	 * @return bool. True if $enum is a contant name or a constant value
	 */
	public static function isValid($enum) {
		$className= get_called_class();
		$consts=Enumerated::getValidValues($className);
		return  (in_array($enum,$consts) || isset($consts[$enum]));
	}

	/**
	 * Get's $enum value or return $enum if is a valid value
	 * @param String $enum Constant name or constant value
	 * @return String $enum value or $enum
	 * @throws Bluevia_Exception if $enum is not a valid value. 
	 */
	public static function getValue($enum){
		if (empty($enum)){
			return null;
		}
		$c=strtoupper($enum);
		$className= get_called_class();
		$consts=Enumerated::getValidValues($className);
		if (isset($consts[$c])){
			return ($consts[$c]);
		} else if (in_array($enum,$consts)){
			return $enum;
		} else {
			throw new Bluevia_Exception('-106',null,$enum,' a value in class '.$className);
		}
	}

	/**
	 * Checks if value of $constantName is equal to $constantValue
	 * @param String $constantName Constant name
	 * @param String $constantValue Constan value
	 * @return Bool True if value's $constantName is equal to $constantValue
	 */
	public static function equals ($constantName,$constantValue){
		$className= get_called_class();
		$constantName=strtoupper($constantName);
		$const=constant($className.'::'.$constantName);
		return $const==$constantValue;
	}
}