<?php

    require_once('ConnectionHandler.php');

    class ReportHandler{

        private $dbconn;

        public function __construct(){
            $this->dbconn_obj = new ConnectionHandler();
            $this->dbconn = $this->dbconn_obj->getConnection();
        }

        public function __destruct(){
            unset($dbconn);
        }

        public function reportTest($uid, $testid, $choice_id){

            if($this->hasReported($uid, $testid)){
                echo json_encode(array('status' => false, 'message'=> "You have already reported this test"));;
                exit();
            }

            $this->reportQuery($uid, $testid, $choice_id);

        }

        private function reportQuery($uid, $testid, $choice_id){

            $stmt = $this->dbconn->prepare("INSERT INTO reports(user_id, test_id, choice) VALUES(?,?,?)");
            $stmt->bind_param("iis", $user_id, $test_id, $choice);
            $user_id = $uid;
            $test_id = $testid;
            $choice = $choice_id;
            $stmt->execute();

            $this->updateTestReports($testid);
        }

        private function updateTestReports($testid){

            $numofreports = $this->getReportsTotal($testid) + 1;

            $stmt = $this->dbconn->prepare("UPDATE tests SET reports=? WHERE test_id=?");
            $stmt->bind_param("ii", $reports, $test_id);
            $reports = $numofreports;
            $test_id = $testid;
            $stmt->execute();

        }

        private function getReportsTotal($testid):int{

            $stmt = $this->dbconn->prepare("SELECT test_id, reports FROM tests WHERE(test_id=?)");
            $stmt->bind_param('i', $test_id);
            $test_id = $testid;
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($t_id, $reports);
            if($stmt->fetch()){
                return $reports;
            }
            else
                return 0;

        }

        private function hasReported($uid, $testid):bool{

            $stmt = $this->dbconn->prepare("SELECT user_id, test_id FROM reports WHERE(user_id=? AND test_id=?)");
            $stmt->bind_param("ii", $user_id, $test_id);
            $user_id = $uid;
            $test_id = $testid;
            $stmt->execute();
            if($stmt->fetch() && $stmt->num_rows > 0)
                return true;
            else
                return false;

        }
    }

?>