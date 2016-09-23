<?php

    require_once('ConnectionHandler.php');
    require_once('EncryptionHandler.php');

    class LoginHandler{

        private $dbconn_obj;
        private $dbconn;

        private $email;
        private $password;

        private $MAX_ATTEMPTS = 5;

        public function __construct() {

            $this->dbconn_obj = new ConnectionHandler();
            $this->dbconn = $this->dbconn_obj->getConnection();

            $this->encryption_obj = new EncryptionHandler();
        }
        
        public function __destruct() {
            unset($this->encryption_obj);
            unset($this->dbconn_obj);
        }

        public function attemptLogin($email, $password){

            $this->email = $email;
            $this->password = $password;

            if (!isset($_SESSION['loginattempts']))
                $_SESSION['loginattempts'] = 0;

            if(!$this->anyAttemptsLeft()){
                echo json_encode(array('status' => false, 'message' => "Sorry. Try again later. No login attempts left"));
                exit();
            }

            $stmt = $this->dbconn->prepare("SELECT user_id, email, password FROM users WHERE email=?");
            $stmt->bind_param('s', $useremail);
            $useremail = $email;
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($user_id, $dbemail, $dbpassword);
            $stmt->fetch();

            if($stmt->num_rows == 0) {
                $this->addAttempt();

                $_SESSION['logged_in'] = false;

                echo json_encode(array('status' => false, 'message' => "Incorrect Login", 'attemptsleft' => $this->attemptsLeft() ));
                exit();
            }
            elseif(!$this->encryption_obj->isHashSame($dbpassword, $this->password)){
                $this->addAttempt();

                $_SESSION['logged_in'] = false;

                echo json_encode(array('status' => false, 'message' => "Incorrect Login", 'attemptsleft' => $this->attemptsLeft() ));
                exit();
            }
            elseif($this->encryption_obj->isHashSame($dbpassword, $this->password)){
                $this->updateHash();

                $_SESSION['user_id'] = $this->getUserID();
                $_SESSION['logged_in'] = true;
                $_SESSION['loginattempts'] = 0;
                $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
                $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

                echo json_encode(array('status' => true, 'message' => "Logged In", 'UserID' => $user_id));
                exit();
            }
            else{
                echo json_encode(array('status' => false, 'message' => "Fatal Error"));
                exit();
            }

        }

        private function getUserID():int{

            $stmt = $this->dbconn->prepare("SELECT user_id FROM users WHERE (email=? AND password=?)");
            $stmt->bind_param("ss", $useremail, $userpassword);
            $useremail = $this->email;
            $userpassword = $this->encryption_obj->getHash();
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($user_id);
            if($stmt->fetch()) {
                return $user_id;
            }
            else {
                return 0;
            }

        }

        private function updateHash(){

            $this->encryption_obj->encryptPassword($this->password);
            $hash = $this->encryption_obj->getHash();
            $stmt = $this->dbconn->prepare("UPDATE users SET password=? WHERE email=?");
            $stmt->bind_param("ss", $userpassword, $useremail);
            $useremail = $this->email;
            $userpassword = $hash;
            $stmt->execute();
            $stmt->close();

        }

        private function addAttempt(){
            $_SESSION['loginattempts'] = $this->getAttempts() + 1;
        }

        private function attemptsLeft():int{
            return ($this->MAX_ATTEMPTS - $this->getAttempts());
        }

        private function getAttempts():int{
            return $_SESSION['loginattempts'];
        }

        private function anyAttemptsLeft():bool{
            if($this->getAttempts() > $this->MAX_ATTEMPTS)
                return false;
            else
                return true;
        }

    }

?>