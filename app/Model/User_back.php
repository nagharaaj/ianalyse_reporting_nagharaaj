<?php

class User extends AppModel {
    var $name = 'User';
    var $useDbConfig = 'ldap';

    var $primaryKey = 'dn';
    var $useTable = '';

    /**
     * return true if the userName is a member of the groupName
     * Active Directory group
     */
    function isMemberOf($userName, $groupName) {
        // trivial check for valid names
        if (empty($userName) || empty($groupName))
            return false;

        // locate the user record
        $userData = $this->find('first',
                                array(
                                    'conditions' => array(
                                        'samaccountname' => $userName
                                    )
                                )
                            );
        // no user by that name exists
        if (empty($userData))
            return false;

        // check if the userin question belongs to any groups
        if (!isset($userData['User']['memberof']))
            return false;

        // search all groups that our user if a meber
        $groups = $userData['User']['memberof'];
        foreach( $groups as $index => $group)
            if (strpos( $group, $groupName) != false)
                return true;

        return false;
    }
}
