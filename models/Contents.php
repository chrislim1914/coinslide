<?php

class Contents {

    //DB
    private $mysql_conn;
    private $table = 'content';

    //content properties
    public $idcontent;
    public $user_id;
    public $title;
    public $content;
    public $createdate;
    public $modifieddate;
    public $delete;
    public $nickname;
    public $recordPerPage;

    //initialize connection
    public function __construct($db) {
        $this->mysql_conn = $db;
    }

    //get all contents
    public function readAllContents() {
        //query
        $query = 'SELECT
                    content.idcontent,
                    content.user_id,
                    `user`.nickname,
                    content.title,
                    content.content,
                    content.createdate,
                    content.modifieddate,
                    content.`delete`
                    FROM ' .$this->table. ' INNER JOIN `user` ON content.user_id = `user`.iduser 
                    WHERE  content.`delete` <> 1';

        //prepare query statement
        $stmt = $this->mysql_conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    //search content
    public function searchContent($keywords) {
        // select all query
        $query = 'SELECT
                    content.idcontent,              
                    `user`.nickname,
                    content.title,
                    content.`delete`
                    FROM ' .$this->table. ' INNER JOIN `user` ON content.user_id = `user`.iduser 
                    WHERE `user`.nickname LIKE ? OR content.title LIKE ? HAVING content.`delete` <> 1';

        // prepare query statement
        $stmt = $this->mysql_conn->prepare($query);

        // sanitize
        $keywords=htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";

        // bind
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);

        // execute query
        $stmt->execute();

        return $stmt;
    }

    //content pagination
    public function readPaging($from_record_num, $records_per_page) {

        // select query
            $query = 'SELECT
            content.idcontent,
            content.user_id,
            `user`.nickname,
            content.title,
            content.content,
            content.createdate,
            content.modifieddate,
            content.`delete`
            FROM ' .$this->table. ' INNER JOIN `user` ON content.user_id = `user`.iduser 
            WHERE  content.`delete` <> 1';

        // prepare query statement
        $stmt = $this->mysql_conn->prepare($query);

        // bind variable values
        $stmt->bindParam(1, $from_record_num, PDO::PARAM_INT);
        $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);

        // execute query
        $stmt->execute();

        // return values from database
        return $stmt;
    }

    //count method for content pagination
    public function count(){
        $query = "SELECT COUNT(*) as total_rows FROM " . $this->table . "";
     
        $stmt = $this->mysql_conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
     
        return $row['total_rows'];
    }

    //create content
    public function createContent() {
        //query for insert
        $query = 'INSERT INTO ' .$this->table. '
                    SET user_id = :user_id, 
                    title = :title, 
                    content = :content';

        //prepare query statement
        $stmt = $this->mysql_conn->prepare($query);

        //sanitize
        $this->user_id=htmlspecialchars(strip_tags($this->user_id));
        $this->title=htmlspecialchars(strip_tags($this->title));
        $this->content=htmlspecialchars(strip_tags($this->content));
        
        // bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":content", $this->content);

        // execute query
        if($stmt->execute()){
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    //get one content
    public function readOneContent() {
        //query statement
        $query = 'SELECT
                content.idcontent,
                content.user_id,
                `user`.nickname,
                content.title,
                content.content,
                content.`delete` FROM 
                ' .$this->table. ' INNER JOIN `user` ON content.user_id = `user`.iduser  
                WHERE content.idcontent = ? AND content.`delete` <> 1';

        //prepare query statement
        $stmt = $this->mysql_conn->prepare($query);

        // bind id of user
        $stmt->bindParam(1, $this->idcontent);

        // execute query
        $stmt->execute();
    
        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // set values to object properties
        $this->idcontent = $row['idcontent'];
        $this->user_id = $row['user_id'];
        $this->nickname = $row['nickname'];
        $this->title = $row['title'];
        $this->content = $row['content'];
    }

    // soft delete content
    public function deleteContent() {
        //query statement
        $query = 'UPDATE ' .$this->table. ' SET `delete` = 1 WHERE idcontent = :idcontent';

        // prepare statement
        $stmt = $this->mysql_conn->prepare($query);

        //sanitize
        $this->idcontent=htmlspecialchars(strip_tags($this->idcontent));
        
        // bind values
        $stmt->bindParam(":idcontent", $this->idcontent);

        // execute query
        if($stmt->execute()){
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;

    }

    // update content
    public function updateContent() {
        //query statement
        $query = "UPDATE " .$this->table. 
                    " SET title = :title, 
                    content = :content  
                    WHERE idcontent = :idcontent";

        //prepare query statement        
        $stmt = $this->mysql_conn->prepare($query);

        //sanitize        
        $this->title=htmlspecialchars(strip_tags($this->title));
        $this->content=htmlspecialchars(strip_tags($this->content));
        
        // bind values        
        $stmt->bindParam(":idcontent", $this->idcontent);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":content", $this->content);

        // execute query
        if($stmt->execute()){
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;
        

    }
}