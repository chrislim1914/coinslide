<?php    

class Users {

    //DB
    private $mysql_conn;
    private $table = 'user';

    //Users properties
    public $iduser;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $nickname;
    public $password;
    public $delete;
    public $national;
    public $createdate;

    //initialize connection
    public function __construct($db) {
        $this->mysql_conn = $db;
    }

    //get Users
    public function readUsers() {
        //query
        $query = 'SELECT iduser, first_name, last_name, email, phone, nickname, `password`, createdate, national FROM ' .$this->table. ' WHERE `delete` <> 1';
        //prepare query statement
        $stmt = $this->mysql_conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    //get one Users
    public function readOneUser() {
        //query statement
        $query = "SELECT iduser, first_name, last_name, email, phone, nickname, password, createdate, national FROM " .$this->table. " WHERE iduser = ? AND `delete` <> 1";

        //prepare query statement
        $stmt = $this->mysql_conn->prepare($query);

        // bind id of user
        $stmt->bindParam(1, $this->iduser);

        // execute query
        $stmt->execute();
    
        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // set values to object properties
        $this->iduser = $row['iduser'];
        $this->first_name = $row['first_name'];
        $this->last_name = $row['last_name'];
        $this->email = $row['email'];
        $this->phone = $row['phone'];
        $this->nickname = $row['nickname'];
        $this->password = $row['password'];
        $this->createdate = $row['createdate'];
        $this->national = $row['national'];
    }

    //create user
    public function createUser() {
        //query for insert
        $query = 'INSERT INTO ' .$this->table. '
                    SET first_name = :first_name, 
                    last_name = :last_name, 
                    email = :email, 
                    phone = :phone, 
                    nickname = :nickname,
                    password = :password,
                    national = :national';

        //prepare query statement
        $stmt = $this->mysql_conn->prepare($query);

        //sanitize
        $this->first_name=htmlspecialchars(strip_tags($this->first_name));
        $this->last_name=htmlspecialchars(strip_tags($this->last_name));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->phone=htmlspecialchars(strip_tags($this->phone));
        $this->nickname=htmlspecialchars(strip_tags($this->nickname));
        $this->password=htmlspecialchars(strip_tags($this->password));
        $this->national=htmlspecialchars(strip_tags($this->national));
        
        // bind values
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":nickname", $this->nickname);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":national", $this->national);

        // execute query
        if($stmt->execute()){
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // update User
    public function updateUser() {
        //query statement
        $query = "UPDATE "   .$this->table. 
                    " SET ufirstname = :ufirstname,
                    ulastname = :ulastname, 
                    uemail = :uemail, 
                    unickname = :unickname, 
                    upw = :upw  
                    WHERE uid = :uid";

        //prepare query statement        
        $stmt = $this->mysql_conn->prepare($query);
        var_dump($stmt);

        //sanitize        
        $this->ufirstname=htmlspecialchars(strip_tags($this->ufirstname));
        $this->ulastname=htmlspecialchars(strip_tags($this->ulastname));
        $this->uemail=htmlspecialchars(strip_tags($this->uemail));
        $this->unickname=htmlspecialchars(strip_tags($this->unickname));
        $this->upw=htmlspecialchars(strip_tags($this->upw));
        $this->uid=htmlspecialchars(strip_tags($this->uid));
        
        // bind values        
        $stmt->bindParam(":ufirstname", $this->ufirstname);
        $stmt->bindParam(":ulastname", $this->ulastname);
        $stmt->bindParam(":uemail", $this->uemail);
        $stmt->bindParam(":unickname", $this->unickname);
        $stmt->bindParam(":upw", $this->upw);
        $stmt->bindParam(":uid", $this->uid);

        // execute query
        if($stmt->execute()){
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;
        

    }

    // delete user
    public function deleteUser() {
        //query statement
        $query = "DELETE FROM " .$this->table. " WHERE uid = :uid";

        // prepare statement
        $stmt = $this->mysql_conn->prepare($query);

        //sanitize
        $this->uid=htmlspecialchars(strip_tags($this->uid));
        
        // bind values
        $stmt->bindParam(":uid", $this->uid);

        // execute query
        if($stmt->execute()){
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;

    }

}