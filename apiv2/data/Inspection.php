<?php
/**
 *
 * @author Dennis the menace
 */
class Inspection
{
    // database connection and table name
    private $conn;
    private $table_name = "inspections";//"report";

    // object properties
    public $id;         // This is not auto increment. App UUID specific.
    public $name;
    public $document;   // Refers to the json document
    public $status;
    public $created;
    public $modified;

    /**
	 *  constructor with $db as database connection
	 */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
	 *  used for checking inspection record exist
	 */
	public function countOne()
	{
		$query = "SELECT 
                    COUNT(*) as total_rows 
                FROM 
                    " . $this->table_name . "
                WHERE 
                    id = ?
                LIMIT
                    0,1";
     
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // bind id of lookup to be updated
        $stmt->bindParam(1, $this->id);
        
        // execute query
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
	 
		return $row['total_rows'];
    }
    
    /**
     * Read a single report.
     */
    public function readOne()
    {
		// query to read single record
		$query = "SELECT
					id, name, status, document, created, modified
                FROM
                    " . $this->table_name . "
				WHERE
                    id = ?
                AND
                    status != " . Status::ARCHIVE . "  
				LIMIT
					0,1";

		// prepare query statement
		$stmt = $this->conn->prepare($query);
	 
		// bind id of lookup to be updated
		$stmt->bindParam(1, $this->id);
	 
		// execute query
		$stmt->execute();
	 
		if ($stmt->rowCount() < 1)
		{
			return false;
		}

		// get retrieved row
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
	 
		// set values to object properties
		$this->id = $row['id'];
		$this->name = $row['name'];
        // $this->document = html_entity_decode($row['document']);
        // $this->document = filter_var($row['document'], FILTER_SANITIZE_EMAIL);
        $this->document = json_decode($row['document']); // Since we are storing as JSON datatype in MySql
		$this->status = intval($row['status']);
		$this->created = intval($row['created']);
		$this->modified = intval($row['modified']);

		return true;
    }

    /**
     * Returns a list of report in json array. Simple format without
     * JSON document.
     */
    public function readAllReport()
    {
        //select all data
        $query = "SELECT
                    id, name, status, created, modified
                FROM
                    " . $this->table_name . "
                WHERE
                    status != " . Status::ARCHIVE . "  
                ORDER BY
                    created";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
		// lookup array
		$report_arr = array();
        $num = $stmt->rowCount();
        
        // check if more than 0 record found
        if($num>0)
        { 
            $report_arr["inspections"] = array();

            // retrieve our table contents
            // fetch() is faster than fetchAll()
            // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                // extract row
                // this will make $row['name'] to
                // just $name only
                extract($row);
        
                $report_item = array(
                    "id" => $id,
                    "name" => $name,
                    "status" => intval($status),
                    "created" => intval($created),
                    "modified" => intval($modified)
                );
        
                array_push($report_arr["inspections"], $report_item);
            }
        }

		return $report_arr;        
    }

    public function storeAll()
    {
		// query to insert record
		$query = "INSERT INTO
					" . $this->table_name . " (
                        id,
                        name,
                        status,
                        document,
                        created,
                        modified)
                VALUES (
                    :id,
                    :name, 
                    :status, 
                    :document, 
                    :created, 
                    :modified)";               
                         
		// prepare query
		$stmt = $this->conn->prepare($query);

        // sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
		$this->name = htmlspecialchars(strip_tags($this->name));
		$this->status = htmlspecialchars(strip_tags($this->status));
		// $this->document = htmlspecialchars(json_encode($this->document));
		$this->created = htmlspecialchars(strip_tags($this->created));
		$this->modified = htmlspecialchars(strip_tags($this->modified));

        // try
        // {
        // bind values
        $stmt->bindParam(":id", $this->id, PDO::PARAM_STR);
        $stmt->bindParam(":name", $this->name, PDO::PARAM_STR);
        $stmt->bindParam(":status", $this->status, PDO::PARAM_INT);
        $stmt->bindParam(":document", json_encode($this->document));//, PDO::PARAM_STR);
        $stmt->bindParam(":created", $this->created, PDO::PARAM_INT);
        $stmt->bindParam(":modified", $this->modified, PDO::PARAM_INT);

        // execute query
        return $stmt->execute();
        // }
        // catch (Exception $e) 
        // {
        //     echo 'Caught exception: ',  $e->getMessage(), "\n";
        // }

		// return false;
    }

    /**
	 * Update the inspection record.
	 * Returns TRUE or FALSE
	 */
	public function update()
	{
		// update query
		$query = "UPDATE
					" . $this->table_name . "
				SET
					name = :name,
					status = :status,
					document = :document,
					created = :created,
					modified = :modified
				WHERE
					id = :id";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
		$this->name = htmlspecialchars(strip_tags($this->name));
		$this->status = htmlspecialchars(strip_tags($this->status));
		// $this->document = htmlspecialchars(json_encode($this->document));
		$this->created = htmlspecialchars(strip_tags($this->created));
		$this->modified = htmlspecialchars(strip_tags($this->modified));
	 
		// bind new values
        $stmt->bindParam(":id", $this->id, PDO::PARAM_STR);
        $stmt->bindParam(":name", $this->name, PDO::PARAM_STR);
        $stmt->bindParam(":status", $this->status, PDO::PARAM_INT);
        $stmt->bindParam(":document", json_encode($this->document));//, PDO::PARAM_STR);
        $stmt->bindParam(":created", $this->created, PDO::PARAM_INT);
        $stmt->bindParam(":modified", $this->modified, PDO::PARAM_INT);
	 
		// execute the query
		return $stmt->execute();
    }	

    /**
	 * Delete the inspection record.
	 * Returns TRUE or FALSE.
	 */
	public function delete()
	{
		// delete query
		$query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
	 
		// prepare query
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->id = htmlspecialchars(strip_tags($this->id));
	 
		// bind id of record to delete
		$stmt->bindParam(1, $this->id);
	 
		// execute query
		return $stmt->execute();
    }
}
?>