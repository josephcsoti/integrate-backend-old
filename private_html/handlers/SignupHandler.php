<?php

    require_once('ConnectionHandler.php');
    require_once('EncryptionHandler.php');

    class SignupHandler{

        private $dbconn;
        private $dbconn_obj;
        private $encryption_obj;

        private $MIN_PASSWORD_LENG = 6;
        private $MAX_PASSWORD_LENG = 64;

        public function __construct(){
            $this->dbconn_obj = new ConnectionHandler();
            $this->dbconn = $this->dbconn_obj->getConnection();
            $this->encryption_obj = new EncryptionHandler();
        }

        public function __destruct() {
            unset($this->dbconn_obj);
        }

        public function createAccount($useremail, $userpassword){

            if($this->isEmailTaken($useremail)){
                echo json_encode(array('status' => false, 'message' => "An account with that email has already been created"));
                exit();
            }

            if(!filter_var($useremail, FILTER_VALIDATE_EMAIL)){
                echo json_encode(array('status' => false, 'message' => "Invalid Email"));
                exit();
            }

            if(!$this->passwordLengthVerify($userpassword)){
                echo json_encode(array('status' => false, 'message' => "Password is not the right length. Your password must be between ".$this->MIN_PASSWORD_LENG." and ".$this->MAX_PASSWORD_LENG." characters. Yours is ".strlen($userpassword)." characters"));
                exit();
            }

            $this->encryption_obj->encryptPassword($userpassword);
            $hash = $this->encryption_obj->getHash();

            $stmt = $this->dbconn->prepare("INSERT INTO users(email, password) VALUES(?, ?)");
            $stmt->bind_param("ss", $email, $password);
            $email = $useremail;
            $password = $hash;
            $stmt->execute();
            $stmt->close();

            echo json_encode(array('status' => true, 'message' => "Account has been created"));
        }

        private function passwordLengthVerify($password):bool {

            if(strlen($password) >= $this->MIN_PASSWORD_LENG && strlen($password) <= $this->MAX_PASSWORD_LENG)
                return true;
            else
                return false;

        }

        private function isEmailTaken($useremail):bool{

            $stmt = $this->dbconn->prepare("SELECT email FROM users WHERE email=?");
            $stmt->bind_param('s', $email);
            $email = $useremail;
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->fetch())
                return true;
            else
                return false;

        }
        
    }

?>