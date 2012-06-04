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
 * Class to hold the information requested in the BV_Directory_Client::getUserInfo method
 *
 *
 */

final class User_Info {
	/**
	 *  Access_Info $accessInfo User Access Information block
	 */
	public $accessInfo;
	/**
	 *  Terminal_Info $terminalInfo User Terminal Information block
	 */
	public $terminalInfo;
	/**
	 *  Personal_Info $personalInfo User Personal Information block
	 */
	public $personalInfo;
	/**
	 *  Profile $profile User %Profile block
	 */
	public $profile;
}

/**
 * Class to hold the information requested in the BV_Directory_Client::getAccessInfo method
 */
final class Access_Info {

	/**
	 *  string $accessType it indicates the access network used to get connected. Possible values are:
	 *  GSM, GPRS, UMTS, HSPA, LTE, WIMAX, etc.
	 */
	public $accessType;
	/**
	 *  string $apn Access Point Name.
	 */
	public $apn;
	/**
	 *  string $roaming. It indicates if the user is attached to an access network different from its home network.
	 */
	public $roaming;
}

/**
 * Class to hold the information requested in the BV_Directory_Client::getTerminalInfo method
 *
 * @author Telefonica R&D
 *
 */

final class Terminal_Info{

	/**
	 *  string $brand  vendor of the device
	 */
	public $brand;
	/**
	 *  string $modelS  model s name
	 */
	public $modelS;
	/**
	 *  string $version  model s version number
	 */
	public $version;
	/**
	 *  string $mms  yes/no field that indicates if the device supports MMS client or not.
	 */
	public $mms;
	/**
	 *  string $ems  yes/no field that indicates if the device supports EMS or not.
	 */
	public $ems;
	/**
	 *  string $smartMessaging  yes/no field that indicates if the device supports smart messaging or not.
	 */
	public $smartMessaging;
	/**
	 *  string $wap  yes/no field that indicates if the device supports WAP or not.
	 */
	public $wap;
	/**
	 *  string $ussdPhase  it indicates if the device supports USSD Phase 1 (only permits
	 * reception of USSDs), Phase 2 (permits both reception and response to USSDs)
	 * or it does not support USSD at all.
	 */
	public $ussdPhase;
	/**
	 *  string $emsMaxNumber  maximum number of consecutive SMSs.
	 */
	public $emsMaxNumber;
	/**
	 *  string $wapPush  It indicates whether the user's handset supports the WAP Push service.
	 */
	public $wapPush;
	/**
	 *  string $mmsVideo  It indicates whether the user's handset is able to play video received over MMS.
	 */
	public $mmsVideo;
	/**
	 *  string $videoStreaming  It indicates whether the user's handset supports video streaming.
	 */
	public $videoStreaming;
	/**
	 *  string $screenResolution  screen resolution in pixels
	 */
	public $screenResolution;

}

/**
 *
 * Class to hold the information  requested in the BV_Directory_Client::getPersonalInfo method
 *
 * @author Telefonica R&D
 *
 */

final class Personal_Info{

	/**
	 *  string $gender the gender of the user
	 */
	public $gender;
}

/**
 *
 * Class to hold the information requested in the BV_Directory_Client::getProfileInfo method
 *
 * @author Telefonica R&D
 *
 */
final class Profile {

	/**
	 *  string $userType it indicates the billing conditions of the user (pre-paid, post-paid, corporate,
	 * etc.)
	 */
	public $userType;
	/**
	 *  string $icb Incoming Communication Barring
	 */
	public $icb;
	/**
	 *  string $ocb Outgoing Communication Barring
	 */
	public $ocb;
	/**
	 *  string $language language, provisioned in the HLR (Home Location Register)
	 */
	public $language;
	/**
	 *  string $parentalControl  it indicates if the parental control is activated and the
	 * associated control level. If it is activated, it will be necessary to check the age (e.g. using
	 * the information from user profile or through other mean), but it is out of scope for this
	 * API interface.
	 */
	public $parentalControl;
	/**
	 *  string $operatorId It indicates the operator the user belongs to. Allowed values are: 'O2' and 'MOVISTAR'
	 */
	public $operatorId;
	/**
	 *  string $mmsStatus  it indicates if the reception of MMS messages is activated or not.
	 */
	public $mmsStatus;
	/**
	 *  string $segment  Class the user belongs to in a social/age/geographical classification.
	 */
	public $segment;

}
/**
 * Class representing the categories of the returned information by the Directory API.
 * @author Telefonica R&D
 *
 */
final class Directory_Data_Sets extends Enumerated{

	/**
	 *  USER_PROFILE valid data set for getting user profile
	 */
	const USER_PROFILE = 'UserProfile';
	/**
	 *  USER_PROFILE valid data set for getting user access information
	 */
	const USER_ACCESS_INFO = 'UserAccessInfo';
	/**
	 *  USER_PROFILE valid data set for getting user terminal information
	 */
	const USER_TERMINAL_INFO = 'UserTerminalInfo';
	/**
	 *  USER_PERSONAL valid data set for getting user personal information
	 */
	const USER_PERSONAL_INFO = 'UserPersonalInfo';
}
/**
 * Enumerated class representing the AccessInfo fields supported by Bluevia
 */
final class Access_Fields extends Enumerated{

	/**
	 *  it indicates the access network used to get connected. Possible values are:
	 *  GSM, GPRS, UMTS, HSPA, LTE, WIMAX, etc.
	 */
	const ACCESS_TYPE = 'accessType';
	/**
	 *  Access Point Name.
	 */
	const APN = 'apn';
	/**
	 *  It indicates if the user is attached to an access network different from its home network.
	 */
	const ROAMING = 'roaming';
}
/**
 * Enumerated class representing the TerminalInfo fields supported by Bluevia
 */
final class Terminal_Fields extends Enumerated{

	/**
	 *  vendor of the device
	 */
	const BRAND = 'brand';
	/**
	 *  model s name
	 */
	const MODEL = 'model';
	/**
	 *  model s version number
	 */
	const VERSION = 'version';
	/**
	 *  yes/no field that indicates if the device supports MMS client or not.
	 */
	const MMS = 'mms';
	/**
	 *  yes/no field that indicates if the device supports EMS or not.
	 */
	const EMS ='ems';
	/**
	 *  yes/no field that indicates if the device supports smart messaging or not.
	 */
	const SMART_MESSGING ='smartMessaging';
	/**
	 *  yes/no field that indicates if the device supports WAP or not.
	 */
	const WAP = 'wap';
	/**
	 *  it indicates if the device supports USSD Phase 1 (only permits
	 * reception of USSDs), Phase 2 (permits both reception and response to USSDs)
	 * or it does not support USSD at all.
	 */
	const USSD_PHASE ='ussdPhase';
	/**
	 *  maximum number of consecutive SMSs.
	 */
	const EMS_MAX_NUMBER ='emsMaxNumber';
	/**
	 *  It indicates whether the user's handset supports the WAP Push service.
	 */
	const WAP_PUSH ='wapPush';
	/**
	 * It indicates whether the user's handset is able to play video received over MMS.
	 */
	const MMS_VIDEO='mmsVideo';
	/**
	 *  It indicates whether the user's handset supports video streaming.
	 */
	const VIDEO_STREAMING ='videoStreaming';
	/**
	 *  screen resolution in pixels
	 */
	const SCREEN_RESOLUTION ='screenResolution';

}
/**
 * Enumerated class representing the PersonalInfo fields supported by Bluevia
 */
final class Personal_Fields extends Enumerated{

	/**
	 *  the gender of the user
	 */
	const GENDER ='gender';
}
/**
 * Enumerated class representing the Profile fields supported by Bluevia
 */
final class Profile_Fields extends Enumerated{

	/**
	 *  it indicates the billing conditions of the user (pre-paid, post-paid, corporate,
	 * etc.)
	 */
	const USER_TYPE = 'userType';
		/**
	 * Incoming Communication Barring
	 */
	const ICB = 'icb';
		/**
	 *  Outgoing Communication Barring
	 */
	const OCB ='ocb';
		/**
	 *  language, provisioned in the HLR (Home Location Register)
	 */
	const LANGUAGE ='language';
		/**
	 *  it indicates if the parental control is activated and the
	 * associated control level. If it is activated, it will be necessary to check the age (e.g. using
	 * the information from user profile or through other mean), but it is out of scope for this
	 * API interface.
	 */
	const PARENTAL_CONTROL = 'parentalControl';
	/**
	 * It indicates the operator the user belongs to. Allowed values are: 'O2' and 'MOVISTAR'
	 */
	const OPERATOR_ID = 'operatorId';
		/**
	 *  it indicates if the reception of MMS messages is activated or not.
	 */
	const MMS_STATUS = 'mmsStatus';
	/**
	 * Class the user belongs to in a social/age/geographical classification.
	 */
	const SEGMENT = 'segment';

}

