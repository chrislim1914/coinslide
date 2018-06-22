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
    public $snsProviderName;
    public $snsProviderId;

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
        $query = "UPDATE " .$this->table. 
                    " SET first_name = :first_name, 
                    last_name = :last_name, 
                    email = :email, 
                    phone = :phone, 
                    nickname = :nickname,
                    national = :national  
                    WHERE iduser = :iduser";

        //prepare query statement        
        $stmt = $this->mysql_conn->prepare($query);

        //sanitize        
        $this->first_name=htmlspecialchars(strip_tags($this->first_name));
        $this->last_name=htmlspecialchars(strip_tags($this->last_name));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->phone=htmlspecialchars(strip_tags($this->phone));
        $this->nickname=htmlspecialchars(strip_tags($this->nickname));
        $this->national=htmlspecialchars(strip_tags($this->national));
        $this->iduser=htmlspecialchars(strip_tags($this->iduser));
        
        // bind values        
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":nickname", $this->nickname);
        $stmt->bindParam(":national", $this->national);
        $stmt->bindParam(":iduser", $this->iduser);

        // execute query
        if($stmt->execute()){
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;
        

    }

    // update User password
    public function updateUserPassword() {
        //query statement
        $query = "UPDATE "   .$this->table. 
                    " SET password = :password
                    WHERE iduser = :iduser";

        //prepare query statement        
        $stmt = $this->mysql_conn->prepare($query);

        //sanitize        
        $this->password=htmlspecialchars(strip_tags($this->password));
        $this->iduser=htmlspecialchars(strip_tags($this->iduser));
        
        // bind values        
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":iduser", $this->iduser);

        // execute query
        if($stmt->execute()){
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;
        

    }

    // soft delete user
    public function deleteUser() {
        //query statement
        $query = "UPDATE " .$this->table. " SET `delete` = 1 WHERE iduser = :iduser";

        // prepare statement
        $stmt = $this->mysql_conn->prepare($query);

        //sanitize
        $this->iduser=htmlspecialchars(strip_tags($this->iduser));
        
        // bind values
        $stmt->bindParam(":iduser", $this->iduser);

        // execute query
        if($stmt->execute()){
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;

    }
    
    //validate email: if email is already in database
    public function validateDuplicateEmail($email) {

        //query statement
        $query = 'SELECT COUNT(*) AS count from user where email = :email LIMIT 1';

        //prepare query statement
        $stmt = $this->mysql_conn->prepare($query);

        // bind email of user
        $stmt->bindParam(":email", $email);

        // execute query
        $stmt->execute();
    
        // get retrieved row
        return $row = $stmt->fetch(PDO::FETCH_ASSOC);

    }

    //create user with SNS data
    public function createSNSUser() {
        //query for insert
        $query = 'INSERT INTO ' .$this->table. '
                    SET first_name = :first_name, 
                    last_name = :last_name, 
                    email = :email, 
                    password = :password,
                    snsProviderName = :snsProviderName, 
                    snsProviderId = :snsProviderId';

        //prepare query statement
        $stmt = $this->mysql_conn->prepare($query);

        //sanitize
        $this->first_name=htmlspecialchars(strip_tags($this->first_name));
        $this->last_name=htmlspecialchars(strip_tags($this->last_name));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->password=htmlspecialchars(strip_tags($this->password));
        $this->snsProviderName=htmlspecialchars(strip_tags($this->snsProviderName));
        $this->snsProviderId=htmlspecialchars(strip_tags($this->snsProviderId));
        
        // bind values
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":snsProviderName", $this->snsProviderName);
        $stmt->bindParam(":snsProviderId", $this->snsProviderId);
        // execute query
        if($stmt->execute()){
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;
    }
    

}