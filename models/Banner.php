<?php

class Banner {

    //DB
    private $mysql_conn;
    private $table = 'banner';

    //content properties
    public $idbanner;
    public $title;
    public $description;
    public $img;
    public $startdate;
    public $enddate;
    public $position;

    //initialize connection
    public function __construct($db) {
        $this->mysql_conn = $db;
    }

    //get all banner
    public function readAllBanners() {
        //query
        $query = 'SELECT
                    idbanner, title, `description`,
                    img, startdate, enddate, position
                    FROM ' .$this->table. ' ORDER BY idbanner DESC';

        //prepare query statement
        $stmt = $this->mysql_conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    //get banner list with pagination
    public function bannerPagination() {

        // select query
        $query = 'SELECT
                    idbanner, title, `description`,
                    img, startdate, enddate, position
                    FROM ' .$this->table. ' ORDER BY idbanner DESC';

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

    //search banner
    public function searchBanner($keywords) {
        // select all query
        $query = 'SELECT
                    idbanner, title, `description`,
                    img, startdate, enddate, position
                    FROM ' .$this->table. ' WHERE title LIKE ? OR `description` LIKE ?  ORDER BY title ASC';

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

    //create banner
    public function createBanner() {
        //query for insert
        $query = 'INSERT INTO ' .$this->table. '
                    SET title = :title, 
                    description = :description,
                    img = :img,
                    startdate = :startdate,
                    enddate = :enddate,
                    position = :position';

        //prepare query statement
        $stmt = $this->mysql_conn->prepare($query);

        //sanitize
        $this->title=htmlspecialchars(strip_tags($this->title));
        $this->description=htmlspecialchars(strip_tags($this->description));
        $this->img=htmlspecialchars(strip_tags($this->img));
        $this->startdate=htmlspecialchars(strip_tags($this->startdate));
        $this->enddate=htmlspecialchars(strip_tags($this->enddate));
        $this->position=htmlspecialchars(strip_tags($this->position));
        
        // bind values
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":img", $this->img);
        $stmt->bindParam(":startdate", $this->startdate);
        $stmt->bindParam(":enddate", $this->enddate);
        $stmt->bindParam(":position", $this->position);

        // execute query
        if($stmt->execute()){
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // update banner
    public function updateBanner() {
        //query statement
        $query = "UPDATE " .$this->table. 
                    " SET title = :title, 
                    description = :description,
                    img = :img,
                    startdate = :startdate,
                    enddate = :enddate,
                    position = :position  
                    WHERE idbanner = :idbanner";

        //prepare query statement        
        $stmt = $this->mysql_conn->prepare($query);

        //sanitize        
        $this->idbanner=htmlspecialchars(strip_tags($this->idbanner));
        $this->title=htmlspecialchars(strip_tags($this->title));
        $this->description=htmlspecialchars(strip_tags($this->description));
        $this->img=htmlspecialchars(strip_tags($this->img));
        $this->startdate=htmlspecialchars(strip_tags($this->startdate));
        $this->enddate=htmlspecialchars(strip_tags($this->enddate));
        $this->position=htmlspecialchars(strip_tags($this->position));
        
        // bind values        
        $stmt->bindParam(":idbanner", $this->idbanner);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":img", $this->img);
        $stmt->bindParam(":startdate", $this->startdate);
        $stmt->bindParam(":enddate", $this->enddate);
        $stmt->bindParam(":position", $this->position);

        // execute query
        if($stmt->execute()){
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;
        

    }



}