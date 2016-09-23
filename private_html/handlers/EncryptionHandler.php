<?php

    class EncryptionHandler{

        private $hash;
        private $cost = 10;

        public function __construct(){
        }

        public function __destruct() {
        }

        public function encryptPassword($password){

            $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
            $salt = sprintf("$2a$%02d$", $this->cost) . $salt;
            $this->hash = crypt($password, $salt);

        }

        public function getHash() {
            return $this->hash;
        }

        public function isHashSame($dbhash, $userpassword):bool{

            if(hash_equals($dbhash, crypt($userpassword, $dbhash)))
                return true;
            else
                return false;

        }

    }

?>