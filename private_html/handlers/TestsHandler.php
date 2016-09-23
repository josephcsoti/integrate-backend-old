<?php

    require_once('ConnectionHandler.php');

    class TestsHandler{

        private $dbconn_obj;
        private $dbconn;
        private $AllTests;

        private $MIN_TEST_NAME_LENGTH = 5;
        private $MAX_TEST_NAME_LENGTH = 50;

        private $TEST_CREATION_COOLDOWN = 60;//(1 * 24 * 60 * 60); //1 days; 24 hours; 60 mins; 60secs

        public function __construct() {
            $this->dbconn_obj = new ConnectionHandler();
            $this->dbconn = $this->dbconn_obj->getConnection();
        }

        public function __destruct() {
            unset($this->dbconn_obj);
        }

        public function getTestsForClassID($classid){

            $this->AllTests = Array();
            
            $stmt = $this->dbconn->prepare("SELECT test_id, test_name, rate_of_1, rate_of_15, rate_of_2, rate_of_25, rate_of_3, rate_of_35, rate_of_4, rate_of_45, rate_of_5, reports, created_on FROM tests WHERE class_id=?");
            $stmt->bind_param('i', $class_id);
            $class_id = $classid;
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($t_id, $t_name, $rate_of_1, $rate_of_15, $rate_of_2, $rate_of_25, $rate_of_3, $rate_of_35, $rate_of_4, $rate_of_45, $rate_of_5, $reports, $created_on);
            if($stmt->num_rows >= 1) {

                while ($stmt->fetch()) {
                    $uid = $_SESSION['user_id'];
                    $hasVoted = $this->checkIfVoted($uid, $t_id);
                    $this->AllTests[] = array('TestID' => $t_id, 'TestName' => $t_name, 'RateOf1' => $rate_of_1, 'RateOf15' => $rate_of_15, 'RateOf2' => $rate_of_2, 'RateOf25' => $rate_of_25, 'RateOf3' => $rate_of_3, 'RateOf35' => $rate_of_35, 'RateOf4' => $rate_of_4, 'RateOf45' => $rate_of_45, 'RateOf5' => $rate_of_5, 'hasVoted' => $hasVoted, 'Reports' => $reports, 'CreatedOn' => $created_on);
                }
            }
            else {
                echo json_encode(array('status' => false, 'message'=> "No tests for class"));
                exit();
            }

            echo json_encode(array('status' => true, 'alltests' =>$this->AllTests));
        }
        
        public function addNewTest($uid, $testname, $classid){

            if($this->checkTestCooldown($uid)){
                echo json_encode(array('status' => false, 'message' => "You are making too many tests. Please wait 1 day"));
                exit();
            }

            $this->testNameRequirements($testname);

            $testname = $this->cleanTestName($testname);

            $stmt = $this->dbconn->prepare("INSERT INTO tests(class_id, test_name, created_on, added_by) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isii", $class_id, $test_name, $created_on, $added_by);
            $class_id = intval($classid);
            $test_name = $testname;
            $created_on = time();
            $added_by = $uid;
            $stmt->execute();

            echo json_encode(array('status' => true, 'message' => "\"".$testname."\" was added"));

        }

        public function getRecentTestData($classid){
            $stmt = $this->dbconn->prepare("SELECT test_id, test_name, created_on FROM tests WHERE class_id=? ORDER BY created_on DESC LIMIT 1;");
            $stmt->bind_param('i', $class_id);
            $class_id = $classid;
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($test_id, $test_name, $created_on);
            if ($stmt->fetch())
                return array('testid' => $test_id , 'testname' => $test_name, 'testtime' => $created_on);
            else
                return array('testid' => 0, 'testname' => "No recent tests", 'testtime' => 0);

        }

        private function testNameRequirements($testname){

            if(!$this->isTestNameClean($testname)){
                echo json_encode(array('status' => false, 'message' => "Test Name is not clean"));
                exit();
            }

            if(strlen($testname) > $this->MAX_TEST_NAME_LENGTH){
                echo json_encode(array('status' => false, 'message' => "Test Name is too big. Max ".$this->MAX_TEST_NAME_LENGTH." characters"));
                exit();
            }

            if(strlen($testname) < $this->MIN_TEST_NAME_LENGTH){
                echo json_encode(array('status' => false, 'message' => "Test Name is too small. Minimum ".$this->MIN_TEST_NAME_LENGTH." characters"));
                exit();
            }

        }

        private function cleanTestName($testname){

            $testname = trim($testname);
            $testname = preg_replace('/\s+/', ' ', $testname);
            $testname = strtolower($testname);
            $testname = ucwords($testname);

            return $testname;
        }

        private function isTestNameClean($testname){

            return true;
        }

        private function checkTestCooldown($uid){

            $stmt = $this->dbconn->prepare("SELECT created_on FROM tests WHERE added_by=? ORDER BY created_on DESC LIMIT 1;");
            $stmt->bind_param('i', $added_by);
            $added_by = $uid;
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($created_on);
            if ($stmt->fetch()) {
                $created = $created_on;
                $now = time();
                if($now - $created < $this->TEST_CREATION_COOLDOWN)
                    return true;
                else
                    return false;
            }
            else
                return false;

        }

        private function checkIfVoted($uid, $testid):bool{

            $stmt = $this->dbconn->prepare("SELECT test_id, user_id, has_voted FROM votes WHERE (test_id=? AND user_id=?)");
            $stmt->bind_param("ii", $test_id, $user_id);
            $test_id = $testid;
            $user_id = $uid;
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
                return true;

        }
    }

?>
