<?php

        class ConnectionHandler{

                private $dbconnection;

                public function __construct() {
                        $this->openConnection();
                }

                public function __destruct() {
                        $this->closeConnection();
                }

                private function openConnection() {

                        $this->dbconnection = new mysqli("", "", "", "");

                        if ($this->dbconnection->connect_error)
                                die("Connection failed: " . $this->dbconnection->connect_error);

                }

                private function closeConnection() {
                        mysqli_close($this->dbconnection);
                }

                public function getConnection(){
                        return $this->dbconnection;
                }
        }

?>