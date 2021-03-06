<?php

    require_once('ConnectionHandler.php');

    class RateHandler {

        private $dbconn_obj;
        private $dbconn;
        private $testid;
        private $userid;

        private $ratesfor1;
        private $ratesfor15;
        private $ratesfor2;
        private $ratesfor25;
        private $ratesfor3;
        private $ratesfor35;
        private $ratesfor4;
        private $ratesfor45;
        private $ratesfor5;

        private $RATE_EXPIRATION = (7 * 24 * 60 * 60); //7 days; 24 hours; 60 mins; 60secs

        public function __construct(){
            $this->dbconn_obj = new ConnectionHandler();
            $this->dbconn = $this->dbconn_obj->getConnection();
        }

        public function __destruct() {
            unset($this->dbconn_obj);
        }

        public function rateTest($userid, $testid, $score){

            $this->testid = $testid;
            $this->userid = $userid;
/*
            if($this->checkIfOld()){
                echo json_encode(array('status' => false, 'message'=> "Test is too old to vote on"));
                exit();
            }

            if($this->checkIfVoted()){
                echo json_encode(array('status' => false, 'message'=> "You already rated"));
                exit();
            }
*/
            $this->getTestRates();

            if($score == "1")
                $this->updateTestRate(1, $this->ratesfor1);
            else if($score == "15")
                $this->updateTestRate(15, $this->ratesfor15);
            else if($score == "2")
                $this->updateTestRate(2, $this->ratesfor2);
            else if($score == "25")
                $this->updateTestRate(25, $this->ratesfor25);
            else if($score == "3")
                $this->updateTestRate(3, $this->ratesfor3);
            else if($score == "35")
                $this->updateTestRate(35, $this->ratesfor35);
            else if($score == "4")
                $this->updateTestRate(4, $this->ratesfor4);
            else if($score == "45")
                $this->updateTestRate(45, $this->ratesfor45);
            else if($score == "5")
                $this->updateTestRate(5, $this->ratesfor5);
            else{
                echo json_encode(array('status' => false, 'message'=> "Rate unsuccessful"));
                exit();
            }

            $this->addUserVoted();
        }
        
        private function updateTestRate($rateid, $ratesforx){

            $stmt = $this->dbconn->prepare("UPDATE tests SET rate_of_".$rateid."=? WHERE test_id=?");
            $stmt->bind_param("ii", $rate, $test_id);
            $rate = $ratesforx + 1;
            $test_id = $this->testid;
            $stmt->execute();

            $newrate = $rate;
            echo json_encode(array('status' => true, 'message'=> "Rate of ".$rateid." added. Total: ".$newrate));

        }

        private function getTestRates(){

            $stmt = $this->dbconn->prepare("SELECT test_id, rate_of_1, rate_of_15, rate_of_2, rate_of_25, rate_of_3, rate_of_35, rate_of_4, rate_of_45, rate_of_5 FROM tests WHERE test_id=?");
            $stmt->bind_param('i', $test_id);
            $test_id = $this->testid;
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($t_id, $rate_of_1, $rate_of_15, $rate_of_2, $rate_of_25, $rate_of_3, $rate_of_35, $rate_of_4, $rate_of_45, $rate_of_5);

            if($stmt->fetch()) {
                $this->ratesfor1 = $rate_of_1;
                $this->ratesfor15 = $rate_of_15;
                $this->ratesfor2 = $rate_of_2;
                $this->ratesfor25 = $rate_of_25;
                $this->ratesfor3 = $rate_of_3;
                $this->ratesfor35 = $rate_of_35;
                $this->ratesfor4 = $rate_of_4;
                $this->ratesfor45 = $rate_of_45;
                $this->ratesfor5 = $rate_of_5;
            }

        }

        private function checkIfOld(){

/*            $stmt = $this->dbconn->prepare("SELECT created_on FROM tests WHERE test_id=?");
            $stmt->bind_param('i', $test_id);
            $test_id = $this->testid;
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($created_on);
            $stmt->fetch();

            $created = $created_on;
            $now = time();

            if($now > $created + $this->RATE_EXPIRATION)
                return true;
            else
                return false;*/

            return false;

        }
    
        private function checkIfVoted():bool{

/*            $stmt = $this->dbconn->prepare("SELECT test_id, user_id, has_voted FROM votes WHERE (test_id=? AND user_id=?)");
            $stmt->bind_param("ii", $test_id, $user_id);
            $test_id = $this->testid;
            $user_id = $this->userid;
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($t_id, $u_id, $has_voted);

            if($stmt->fetch()) {
                if ($has_voted == 1)
                    return true;
                else
                    return false;

            }
            elseif($stmt->num_rows == 0)
                return false;
            else
                return true;*/

            return false;

        }

        private function addUserVoted(){

            $stmt = $this->dbconn->prepare("INSERT INTO votes(user_id, test_id, has_voted) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $user_id, $test_id, $has_voted);
            $has_voted = 1;
            $test_id = $this->testid;
            $user_id = $this->userid;
            $stmt->execute();

        }
    }

?>                                                                                   
