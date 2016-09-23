<?php

    require_once('ConnectionHandler.php');

    class ClassesHandler {

        private $dbconn_obj;
        private $dbconn;

        private $test_obj;

        private $AllClasses;

        public function __construct() {
            $this->dbconn_obj = new ConnectionHandler();
            $this->dbconn = $this->dbconn_obj->getConnection();
        }

        public function __destruct() {
            unset($this->dbconn_obj);
        }

        public function getUserClasses($userid){

            $this->AllClasses = array();

            $stmt = $this->dbconn->prepare("SELECT class_1_id, class_2_id, class_3_id, class_4_id, class_5_id, class_6_id, class_7_id, class_8_id FROM users WHERE user_id=?");
            $stmt->bind_param('i', $user_id);
            $user_id = $userid;
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($class_1_id, $class_2_id, $class_3_id, $class_4_id, $class_5_id, $class_6_id, $class_7_id, $class_8_id);
            $stmt->fetch();

            $this->validateHelper($class_1_id, $class_2_id, $class_3_id, $class_4_id, $class_5_id, $class_6_id, $class_7_id, $class_8_id);

            $this->classNameQuery($class_1_id);
            $this->classNameQuery($class_2_id);
            $this->classNameQuery($class_3_id);
            $this->classNameQuery($class_4_id);
            $this->classNameQuery($class_5_id);
            $this->classNameQuery($class_6_id);
            $this->classNameQuery($class_7_id);
            $this->classNameQuery($class_8_id);

            echo json_encode(array('status' => true, 'allclasses' => $this->AllClasses));

        }

        public function updateUserClasses($uid, $class1id, $class2id, $class3id, $class4id, $class5id, $class6id, $class7id, $class8id){

            $this->validateHelper($class1id, $class2id, $class3id, $class4id, $class5id, $class6id, $class7id, $class8id);

            $this->updateUserClassesQuery($uid, 1, $class1id);
            $this->updateUserClassesQuery($uid, 2, $class2id);
            $this->updateUserClassesQuery($uid, 3, $class3id);
            $this->updateUserClassesQuery($uid, 4, $class4id);
            $this->updateUserClassesQuery($uid, 5, $class5id);
            $this->updateUserClassesQuery($uid, 6, $class6id);
            $this->updateUserClassesQuery($uid, 7, $class7id);
            $this->updateUserClassesQuery($uid, 8, $class8id);
        }

        private function classNameQuery($classid){

/*          if($classid == 0){
                $this->AllClasses[] = array('ClassID' => 0, 'ClassName' => "");
                return;
            }*/

            $stmt = $this->dbconn->prepare("SELECT class_id, class_name FROM classes WHERE class_id=?");
            $stmt->bind_param('i', $class_id);
            $class_id = $classid;
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($c_id, $c_name);
            $stmt->fetch();

            $testdata = $this->getRecentTestDataForClass($classid);
            unset($this->test_obj);

            $testid = $testdata['testid'];
            $testname = $testdata['testname'];
            $testtime = $testdata['testtime'];

            $this->AllClasses[] = array('ClassID' => $c_id, 'ClassName' => $c_name, 'LatestTestID' => $testid, 'LatestTestName' => $testname, 'LatestTestTime' => $testtime);
        }

        private function updateUserClassesQuery($userid, $num, $classid){

            $stmt = $this->dbconn->prepare("UPDATE users SET class_".$num."_id=? WHERE user_id=?");
            $stmt->bind_param("ii", $class_id, $uid);
            $class_id = $classid;
            $uid = $userid;
            $stmt->execute();

            echo json_encode(array('status' => true, 'message'=> "Class #".$num." with the ID: ".$classid." was added"));
        }

        private function validateHelper($class1id, $class2id, $class3id, $class4id, $class5id, $class6id, $class7id, $class8id){

            $errors = array();

            if(!$this->validateClassID($class1id)){
                $errors[] = array('status' => false, 'message'=> "Class ID: ".$class1id." is not valid");
            }

            if(!$this->validateClassID($class2id)){
                $errors[] = array('status' => false, 'message'=> "Class ID: ".$class2id." is not valid");
            }

            if(!$this->validateClassID($class3id)){
                $errors[] = array('status' => false, 'message'=> "Class ID: ".$class3id." is not valid");
            }

            if(!$this->validateClassID($class4id)){
                $errors[] = array('status' => false, 'message'=> "Class ID: ".$class4id." is not valid");
            }

            if(!$this->validateClassID($class5id)){
                $errors[] = array('status' => false, 'message'=> "Class ID: ".$class5id." is not valid");
            }

            if(!$this->validateClassID($class6id)){
                $errors[] = array('status' => false, 'message'=> "Class ID: ".$class6id." is not valid");
            }

            if(!$this->validateClassID($class7id)){
                $errors[] = array('status' => false, 'message'=> "Class ID: ".$class7id." is not valid");
            }

            if(!$this->validateClassID($class8id)){
                $errors[] = array('status' => false, 'message'=> "Class ID: ".$class8id." is not valid");
            }

            if(!sizeof($errors) == 0) {
                echo json_encode(array('status' => false, 'message' => $errors));
                exit();
            }

        }

        private function validateClassID($classid):bool {

            $stmt = $this->dbconn->prepare("SELECT class_id FROM classes WHERE class_id=?");
            $stmt->bind_param('i', $class_id);
            $class_id = $classid;
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->fetch())
                return true;
            else
                return false;

        }

        private function getRecentTestDataForClass($classid){

            require_once('TestsHandler.php');

            $this->test_obj = new TestsHandler();
            return $this->test_obj->getRecentTestData($classid);

        }

    }

?>