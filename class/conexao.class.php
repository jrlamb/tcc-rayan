<?php
include('constants.php');
class Database {

    private $_connection;
    private static $_instance; //The single instance
    private $_host = "dbmy0010.whservidor.com";	//
    private $_username = "odanilo_1";
    private $_password = "R.ayan";
    private $_database = "odanilo_1";

    /*
      Get an instance of the Database
      @return Instance
     */

    public static function getInstance() {
        if (!self::$_instance) { // If no instance then make one
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    // Constructor
    private function __construct() {
        $this->_connection = new mysqli($this->_host, $this->_username, $this->_password, $this->_database);

        // Error handling
        if (mysqli_connect_error()) {
            $this->trigger_error("Failed to conencto to MySQL: " . mysql_connect_error(), E_USER_ERROR);
        }
		//mysqli_set_charset($this->_connection,"utf8");
    }

    // Get mysqli connection
    public function getConnection() {
        return $this->_connection;
    }

}

?>