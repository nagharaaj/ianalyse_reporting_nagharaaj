<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CLdapLogin
 *
 * @author somnaths
 */

define('LDAP_OPT_DIAGNOSTIC_MESSAGE', 0x0032);

class CLdapLogin {

        private $user_name;
        private $password;
        private $server;
        private $port;
        private $domain;
        private $base_dn;
        private $ldap;
        private $bind;

        public function __construct($server = null, $port = null, $domain = null, $user_name = null, $password = null, $baseDN = null) {
                $this->server     = $server;
                $this->port       = $port;
                $this->domain     = $domain;
                $this->base_dn    = $baseDN;
                $this->user_name  = $user_name;
                $this->password   = $password;
        }

        /*
        * If function return the false then verify the commit on the return false statement.
        */

        public function login() {
                $this->ldap = ldap_connect($this->server, $this->port);

                if (false == $this->ldap) {
                        return false; // Failed to connect ldap server.
                }

                ldap_set_option($this->ldap, LDAP_OPT_REFERRALS, 0);
                ldap_set_option($this->ldap, LDAP_OPT_PROTOCOL_VERSION, 3);

                $this->bind = @ldap_bind($this->ldap, $this->user_name, $this->password);

                if ($this->bind) {

                        $filter   = "(&(objectCategory=*) (userPrincipalName=$this->user_name))";

                        $fields   = array("samaccountname", "mail", "memberof", "member", "department", "displayname", "telephonenumber", "primarygroupid", "objectsid", "physicalDeliveryOfficeName", "title", "l", "userPrincipalName");

                        $search = ldap_search($this->ldap, $this->base_dn, $filter, $fields);

                        if (!$search) {
                                return false;
                        }

                        $info = ldap_get_entries($this->ldap, $search);

                        if (false == is_array($info)) {
                                return false;
                        }

                        if ($info['count'] >= 1) {
                                return true;
                        } else {
                                return false;
                        }
                }

                return false;
        }


        public function getGroupName() {
                if ($this->bind) {
                        $filter       = "(userPrincipalName=" . $this->user_name . ")";
                        $fields       = array("memberof");
                        $group_list   = array();

                        $search   = ldap_search($this->ldap, $this->base_dn, $filter, $fields);

                        if (!$search) {
                                display( 'Error Code :: ' . ldap_errno($this->ldap) . ' Error Description :: ' . ldap_err2str(ldap_errno($this->ldap)));
                                return false;
                        }

                        $info     = ldap_get_entries($this->ldap, $search);

                        if (true == is_array($info) && true == isset($info[0]['memberof'])) {
                                foreach ($info[0]['memberof'] as $groups) {
                                        if (true == is_array($groups)) {
                                                for ($j = 0; $j < $info[0]["memberof"]["count"]; $j++)
                                                        array_push($group_list, $info[0]["memberof"][$j]);
                                        }
                                }
                        }

                        if (true == is_array($group_list)) {
                                $group_data = null;
                                foreach ($group_list as $group) {
                                        $group = substr($group, 0, strpos($group, ','));
                                        $group_data .= str_replace( 'CN=', '', $group ) . '<br/>';
                                }
                                return $group_data;
                        } else {
                                return false;
                        }
                } else {
                        echo 'Failed to bind to LDAP Server.';
                }

                //Return false if not bind with LDAP server.
                return false;
        }

        public function getAllUserInfo($strSearch = null) {

                if ($this->bind) {
                        $filter   = "(&(objectCategory=*) (|(displayName=$strSearch*) (givenName=$strSearch*) (sn=$strSearch*)))";

                        $fields   = array("samaccountname", "mail", "memberof", "member", "department", "displayname", "telephonenumber", "primarygroupid", "objectsid", "physicalDeliveryOfficeName", "title", "l", "st", "co", "mailNickname", "userPrincipalName"); 

                        $search = ldap_search($this->ldap, $this->base_dn, $filter, $fields, 0, 5, 0);

                        if (!$search) {
                                display('Bad search parameters please check the CN, OU, DC, DC, DC values.');
                                display( 'Error Code :: ' . ldap_errno($this->ldap) . ' Error Description :: ' . ldap_err2str(ldap_errno($this->ldap)));
                                return false;
                        }

                        $info = ldap_get_entries($this->ldap, $search);
                        //echo '<pre>'; print_r($info);
                        if (false == is_array($info)) {
                                display('Failed to load data from LDAP server.');
                                return false;
                        }

                        $arrResult = array();

                        $cnt = 0;
                        for ($i = 0; $i < $info["count"]; $i++) {
                                if(isset($info[$i]['userprincipalname'])) {
                                        $group_list = array();

                                        if (true == isset($info[$i]["memberof"])) {
                                                for($j = $i; $j < $info[$i]["memberof"]["count"]; $j++)
                                                        array_push($group_list, $info[$i]["memberof"][$j]);
                                        }

                                        $arrResult[$cnt]['Name'] = ucwords(strtolower($info[$i]['displayname'][0]));
                                        $arrResult[$cnt]['Title'] = (!empty($info[$i]['title']) ? ucwords(strtolower($info[$i]['title'][0])) : '');

                                        if(!empty($info[$i]['l'])) {
                                                $arrResult[$cnt]['Location'] = ucwords(strtolower($info[$i]['l'][0]));
                                        } elseif(!empty($info[$i]['co'])) {
                                                $arrResult[$cnt]['Location'] = ucwords(strtolower($info[$i]['co'][0]));
                                        } elseif(!empty($info[$i]['physicaldeliveryofficename'])) {
                                                $arrResult[$cnt]['Location'] = ucwords(strtolower($info[$i]['physicaldeliveryofficename'][0]));
                                        } else {
                                                $arrResult[$cnt]['Location'] = '';
                                        }
                                        $arrResult[$cnt]['Email'] = (!empty($info[$i]['userprincipalname']) ? strtolower($info[$i]['userprincipalname'][0]) : '');
                                        $arrResult[$cnt]['UserName'] = (!empty($info[$i]['userprincipalname']) ? strtolower($info[$i]['userprincipalname'][0]) : '');

                                        $group_name = '----';
                                        foreach ($group_list as $group) {
                                                $group = substr($group, 0, strpos($group, ','));
                                                $group_name .= str_replace( 'CN=', '', $group ) . '<br/>';

                                        }
                                        $cnt++;
                                }
                        }

                        echo json_encode($arrResult);

                        @ldap_close($this->ldap);
                        return true;
                } else {
                        display('Failed to fecth User data from server.');
                }

                return false;
        }

        public function setUserName($user_name) {
                $this->user_name = $user_name;
        }

        public function setPassword($password) {
                $this->password = $password;
        }

        public function setServer($server) {
                $this->server = $server;
        }

        public function setPort($port) {
                $this->port = $port;
        }

        public function setDomain($domain) {
                $this->domain = $domain;
        }

        public function getUserName($mail_address = false) {
                if (true == $mail_address) return $this->user_name . '@' . $this->domain;

                return $this->user_name;
        }

        public function getPassword() {
                return $this->password;
        }

        public function getServer() {
                return $this->server;
        }

        public function getPort() {
                return $this->port;
        }

        public function getDomain() {
                return $this->domain;
        }
}

function display( $arrmixData ) {
        print_r( '<pre>' );
        print_r( $arrmixData );
        print_r( '</pre>' );
}
?>
