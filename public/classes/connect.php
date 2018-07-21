<?php 

class Connect 
{
    private $host, $user, $password, $db, $charset;
           
    public function __construct() {
        $this->host = DB_SERVER;
        $this->user = DB_USER;
        $this->password = DB_PASSWORD;
        $this->db = DB_NAME;
        $this->charset = DB_CHARSET;
    }

    public function connect() {
        $con = new mysqli($this->host, $this->user, $this->password, $this->db);
        
        if ($con->connect_errno) {
            echo "MySQL Error: " . $con->connect_error;
        } 

        $con->set_charset($this->charset);

        return $con;
    }
}

class DBEntity {
    protected $con;
    protected $user_id;
    protected $table;
    
    public function __construct($con, $user_id) {
        $this->con = $con;
        $this->user_id = $user_id;
    }

    protected function convertJSONtoCSV($json) {
        $json = json_decode($json);
        $result = implode(',', $json);
        return $this->con->real_escape_string($result);
    }

}

?>