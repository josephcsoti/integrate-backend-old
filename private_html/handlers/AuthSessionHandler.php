<?php

    class AuthSessionHandler{

        public function __construct() {
            session_start();
        }

        public function __destruct() {
            session_write_close();
        }

        public function destroySession(){
            session_unset();
            session_destroy();
        }

        public function checkAll(){

            if(!$this->isUserLoggedIn()){
                echo json_encode(array('status' => false, 'message'=> "You are not logged in"));
                exit();
            }

            if(!$this->isUserAgentSame()){
                echo json_encode(array('status' => false, 'message'=> "User Agent has changed. Exiting for security reasons"));
                exit();
            }

            if(!$this->isUserIPSame()){
                echo json_encode(array('status' => false, 'message'=> "User IP has changed. Exiting for security reasons"));
                exit();
            }

        }

        private function isUserLoggedIn():bool{

            if(!isset($_SESSION['logged_in']))
                return false;
            else
                return $_SESSION['logged_in'];
        }

        private function isUserIPSame():bool{

            if(!isset($_SESSION['user_ip']))
                return false;
            else
                return $_SESSION['user_ip'] == $_SERVER['REMOTE_ADDR'];

        }

        private function isUserAgentSame():bool{

            if(!isset($_SESSION['user_agent']))
                return false;
            else
                return $_SESSION['user_agent'] == $_SERVER['HTTP_USER_AGENT'];
            
        }

    }

?>