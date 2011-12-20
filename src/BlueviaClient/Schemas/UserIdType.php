<?php
/**
 * Helper class for UserId type
 */
class BlueviaClient_Schemas_UserIdType {

    const PHONE = 'phoneNumber';
    const ALIAS = 'alias';
    const ANYURI= 'anyUri';
    const IP    = 'ipAddress';
    const OTHER = 'otherId';    

    /**
     * Constructor
     * @param string $participant
     * @param string $type (phoneNumber, alias, anyUri, ipAddress, otherId)
     */
    public function __construct($participant, $type = self::PHONE) {
        $this->$type = $participant;
        return $this;
    }

    public function __isset($type) {
        return isset($this->_userid[$type]);
    }

    public function  __get($type) {
        $this->_validate_type($type);

        return $this->_userid[$type];
    }

    public function  __set($type, $participant) {
        $this->_validate_type($type);
        $this->_userid = array($type => $participant);
        return $this;
    }

    public function  __sleep() {
        reset($this->_userid);
        return array(key($this->_userid));
    }

    public function toArray() {
        return $this->_userid;
    }
    
    public function toUrl($field = 'phoneNumber') {
        return $field. ':' .$this->_userid[$field];
    }



    protected function _validate_type($type) {
        if (!in_array($type, $this->_allowed_types)) {
            throw new Unica_Exception_Parameters('Please use only one of ' .
                        implode(', ', $this->_allowed_types));
        }
    }

    protected $_userid = array();
    protected $_allowed_types = array(
        self::PHONE,
        self::ALIAS,
        self::ANYURI,
        self::IP,
        self::OTHER
    );
}
