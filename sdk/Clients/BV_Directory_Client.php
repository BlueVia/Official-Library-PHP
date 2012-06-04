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
 * Abstract client for the REST binding of the Bluevia Directory Service.
 *
 * @author Telefonica R&D
 *
 */
abstract class BV_Directory_Client extends BV_Base_Client {


	/**
	 * Allows an application to get all the user context information. Applications
	 * will only be able to retrieve directory information on themselves.
	 * Information blocks can be filtered using the data set.
	 *
	 * @param array|null $dataSet (Optional) array of Directory_Data_Sets constants (the blocks to be retrieved).
	 *
	 * @return User_Info object containing the blocks of user context information you've selected.
	 * @throws Bluevia_Exception
	 */
	protected function getUserInfo($dataSet=null){
		if (!is_null($dataSet))
		$dataSet=$this->_verifyDataSet($dataSet);
		$params=null;
		if (!empty($dataSet)) {
			$params=array('dataSets' => str_replace( "User", "", $dataSet ));
		}
		$response=$this->baseRetrieve(null,$params);
		return $this->_createUserInfo($response);
	}

	/**
	 * Retrieves a subset of the User Personal Information resource block from the directory. Applications
	 * will only be able to retrieve directory information on themselves.
	 *
	 * @param array|null $filter (Optional) array of Personal_Fields constants. A filter object to specify which information fields are required.
	 * 					If not included this function will return all fields.
	 * @return Personal_Info object containing the user terminal information
	 * @throws Bluevia_Exception
	 */
	protected function getPersonalInfo($fields=null){
		if (!empty($fields)){
			$fields=$this->_verifyFields($fields,'Personal_Fields');
		}
		$response=$this->baseRetrieve('/'.Directory_Data_Sets::USER_PERSONAL_INFO,$fields);
		return $this->_createInfo($response->userPersonalInfo, new Personal_Info());
	}

	/**
	 * Retrieves User Profile resource block from the directory. Applications
	 * will only be able to retrieve directory information on themselves.
	 *
	 * @param array|null $filter (Optional) array of Profile_Fields constants. A filter object to specify which information fields are required.
	 * 					If not included this function will return all fields.
	 * @return Profile_Info object containing the user terminal information
	 * @throws Bluevia_Exception
	 */
	protected function getProfileInfo($fields=null){
		if (!empty($fields)){
			$fields=$this->_verifyFields($fields,'Profile_Fields');
		}
		$response=$this->baseRetrieve('/'.Directory_Data_Sets::USER_PROFILE,$fields);
		return $this->_createInfo($response->userProfile, new Profile());
	}

	/**
	 * Retrieves User Access Information resource block from the directory. Applications
	 * will only be able to retrieve directory information on themselves.
	 *
	 * @param array|null $filter (Optional) array of Access_Fields constants. A filter object to specify which information fields are required.
	 * 					If not included this function will return all fields.
	 * @return Access_Info object containing the user terminal information
	 * @throws Bluevia_Exception
	 */
	protected function getAccessInfo($fields=null){
		if (!empty($fields)){
			$fields=$this->_verifyFields($fields,'Access_Fields');
		}
		$response=$this->baseRetrieve('/'.Directory_Data_Sets::USER_ACCESS_INFO,$fields);
		return $this->_createInfo($response->userAccessInfo, new Access_Info());
	}

	/**
	 * Retrieves User Terminal Information resource block from the directory. Applications
	 * will only be able to retrieve directory information on themselves.
	 *
	 * @param array|null $filter (Optional) array of Terminal_Fields constants. A filter object to specify which information fields are required.
	 * 					If not provided this function will return all fields.
	 * @return Terminal_Info object containing the user terminal information
	 * @throws Bluevia_Exception
	 */
	protected function getTerminalInfo($fields=null){
		if (!empty($fields)){
			$fields=$this->_verifyFields($fields,'Terminal_Fields');
		}
		$response= $this->baseRetrieve('/'.Directory_Data_Sets::USER_TERMINAL_INFO,$fields);
		return $this->_createInfo($response->userTerminalInfo, new Terminal_Info());
	}

	/**
	 * Helper function for setting the Client properties
	 */
	protected function setParameters(){
		$this->_parser= new Json_Parser();
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////
	//
	//  PRIVATE FUNCTIONS
	//
	//////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Helper function to simplify the server's response to the BV_Directory::getUserInfo method into a more useful object.
	 * @param stdClass $response Generic object containing the server's response
	 * @return User_Info object containing the useful information from a getUserInfo request.
	 */
	private function _createUserInfo($response){
		$userInfo = new User_Info();
		$response=$response->userInfo;
		foreach($response as $key => $value){
			$property=lcfirst(str_replace('user','',$key));
			$class=str_replace('Info','_Info',$property);
			$userInfo->$property=$this->_createInfo($value, new $class());
		}
		return $userInfo;
	}
	
	/**
	 * Helper function to verify if the $dataSet parameter has allowed values.
	 * @param array|string $dataSet String with one directory method or an array with multiple directory methods
	 * @throws Bluevia_Exception if $dataSet has incorrect values.
	 */
	private function _verifyDataSet($dataSet) {
		if(!is_array($dataSet)) {
			throw new Bluevia_Exception('-106',null,'$dataSet',' an array.');
		}
		// check if it is on the types allowed
		foreach($dataSet as $key => $currentType) {
			$dataSet[$key]=Directory_Data_Sets::getValue($currentType);
		}
		if (count(array_unique($dataSet)) < count($dataSet))
		throw new Bluevia_Exception('-107',null,'$dataSet');
		if (count($dataSet)===4) return null;
		if (count($dataSet)===1) {
			$function='get'.str_replace('User','',$dataSet[0]);
			throw new Bluevia_Exception('-110',null,$function,$dataSet[0]);
		}
		return implode(',', $dataSet);
	}

	/**
	 * Helper function to verify if the $fields parameter has allowed values. Also if all the $fields correspond to tha $dataSet.
	 * @param array $fields Array with fields.
	 * @param String $dataSet A value in Directory_Data_Sets.
	 * @return an array with the format specified by Bluevia for the fields.
	 */
	private function _verifyFields($fields,$dataSet) {

		if(!is_array($fields)) {
			$fields = array($fields);
		}
		// Check duplicate values
		if (count(array_unique($fields)) < count($fields))
		throw new Bluevia_Exception('-107',null,'$fields');

		foreach ($fields as $key => $currentField){
				
			// Check if the field value is correct
			$fields[$key]=call_user_func($dataSet.'::getValue',$currentField);

		}
		return array('fields'=>"'".implode(',', $fields)."'");
	}

}