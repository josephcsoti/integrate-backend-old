<?php

    require_once('ConnectionHandler.php');

    class SearchHandler {

        private $dbconn_obj;
        private $dbconn;
        private $searchresultarray;

        public function __construct(){
            $this->searchresultarray = array();
            $this->dbconn_obj = new ConnectionHandler();
            $this->dbconn = $this->dbconn_obj->getConnection();
        }

        public function __destruct() {
            unset($this->dbconn_obj);
        }

        public function searchClasses($schoolid, $search) {

            $stmt = $this->dbconn->prepare("SELECT class_id, class_name, school_id FROM classes WHERE class_name LIKE ? AND school_id=? ORDER BY class_name");
            $stmt->bind_param("si", $class_name, $school_id);
            $class_name = '%'.$search.'%';
            $school_id = $schoolid;
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($c_id, $c_name, $s_id);
            if($stmt->num_rows >= 1) {
                while ($stmt->fetch()) {
                    $this->searchresultarray[] = array('ClassID' => $c_id, 'ClassName' => $c_name);
                }
            }
            else {
                echo json_encode(array('status' => false, 'message'=> "No classes found for that name"));
                exit();
            }

        }

        public function searchSchools($search) {

            $stmt = $this->dbconn->prepare("SELECT school_id, school_name FROM schools WHERE school_name LIKE ? ORDER BY school_name");
            $stmt->bind_param('s', $school_name);
            $school_name = '%'.$search.'%';
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($s_id, $s_name);;
            if($stmt->num_rows >= 1) {
                while ($stmt->fetch()) {
                    $this->searchresultarray[] = array('SchoolID' => $s_id, 'SchoolName' => $s_name);
                }
            }
            else {
                echo json_encode(array('status' => false, 'message'=> "No schools found for that name"));
                exit();
            }

        }
        
        public function getResult() {
            return $this->searchresultarray;
        }
    }

?>