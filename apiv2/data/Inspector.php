<?php
/**
 *
 * @author Dennis the menace
 */
class Inspector
{
    // database connection and table name
    private $conn;
    private $table_name = "inspector";
 
    // object properties
    public $id;
    public $name;
	public $position;
	public $company;
    public $username;
	public $password;
	public $photo;
    public $created;
    public $modified;

    public function __construct($db)
    {
		$this->conn = $db;
    }
 
    /**
	 * Read all inspector and return a Lookup JSON array
	 */
    public function readAll()
    {
        //select all data
        $query = "SELECT
                    id, name, position, company, username, password, photo, created, modified
                FROM
                    " . $this->table_name . "
                ORDER BY
                    name";
 
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
 
		// inspector array
		$inspector_arr = array();
		$num = $stmt->rowCount();
		
        // check if more than 0 record found
        if($num>0)
        { 
			$inspector_arr["inspector"] = array();

            // retrieve our table contents
            // fetch() is faster than fetchAll()
            // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                // extract row
                // this will make $row['name'] to
                // just $name only
                extract($row);
        
                $inspector_item = array(
                    "id" => intval($id),
                    "name" => $name,
                    "position" => $position,
                    "company" => $company,
                    "username" => $username,
                    "password" => $password,    
                    "photo" => $photo, // No need conversion since it is already in an json format                  
                    "created" => intval($created),
                    "modified" => intval($modified)
                );
        
                array_push($inspector_arr["inspector"], $inspector_item);
            }
        }
		
		return $inspector_arr;
    }
	
	/**
	 * Return a single inspector based on the provided ID.
	 * Returns TRUE or FALSE.
	 */
    public function readOne()
    {
		//select all data
		$query = "SELECT
					id, name, position, company, username, password, photo, created, modified
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

		$stmt->execute();
 
		if ($stmt->rowCount() < 1)
		{
			return false;
		}

		// get retrieved row
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
	 
		// set values to object properties
		$this->id = intval($row['id']);
		$this->name = $row['name'];
		$this->position = $row['position'];
		$this->company = $row['company'];
		$this->username = $row['username'];
		$this->password = $row['password'];		
		$this->photo = $row['photo']; // No need conversion since it is already in an json format
		$this->created = intval($row['created']);
		$this->modified = intval($row['modified']);

		return true;
    }
    
	/**
	 * Create inspector.
	 * Returns TRUE or FALSE.
	 */
	public function create()
	{
		// query to insert record
		$query = "INSERT INTO
					" . $this->table_name . " (
						name, 
						position, 
						company, 
						username, 
						password, 
						photo, 
						created, 
						modified)
                VALUES (
					:name, 
					:position, 
					:company,
					:username, 
					:password, 
					:photo, 
					:created, 
					:modified)";
	 
		// prepare query
		$stmt = $this->conn->prepare($query);

		// // sanitize
		// $this->name = htmlspecialchars(strip_tags($this->name));
		// $this->position = htmlspecialchars(strip_tags($this->position));
		// $this->company = htmlspecialchars(strip_tags($this->company));
		// $this->username = htmlspecialchars(strip_tags($this->username));
		// $this->password = htmlspecialchars(strip_tags($this->password));
		// $this->photo = htmlspecialchars(strip_tags($this->photo));
		// $this->created = htmlspecialchars(strip_tags($this->created));
        // $this->modified = htmlspecialchars(strip_tags($this->modified));

		// bind values
		$mynull = 'NULL';
		$stmt->bindParam(":name", $this->name, PDO::PARAM_STR);
		$stmt->bindParam(":position", $this->position, PDO::PARAM_STR);
		$stmt->bindParam(":company", $this->company, PDO::PARAM_STR);
		$stmt->bindParam(":username", $this->username, PDO::PARAM_STR);
		$stmt->bindParam(":password", $this->password, PDO::PARAM_STR);
		$stmt->bindParam(":created", $this->created, PDO::PARAM_INT);
		$stmt->bindParam(":modified", $this->modified, PDO::PARAM_INT);
		
		// Handle nullable columns
		isset($this->photo) ? $stmt->bindParam(":photo", $mynull, PDO::PARAM_INT) : $stmt->bindParam(":photo", $this->photo);
		// isset($this->photo) ? $stmt->bindValue(':photo', null, PDO::PARAM_INT) : $stmt->bindParam(":photo", $this->photo);

		// execute query
		if ($stmt->execute()) 
		{
			return true;
		}
		else 
		{
			error_log("error " .  json_encode($stmt->error), 3, "c:\\temp\\inspipe-api-errors.log");
			// error_log("error " .  json_encode($stmt->error), 3, __DIR__ . "/logs/inspipe-api-errors.log");
			return false;
		}
    }	
    
    /**
	 * Update the inspector.
	 * Returns TRUE or FALSE
	 */
	public function update()
	{
		// update query
		$query = "UPDATE
					" . $this->table_name . "
				SET
					name = :name,
					position = :position,
					company = :company,
					username = :username,
					password = :password,
					photo = :photo,
                    modified = :modified
				WHERE
					id = :id";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->name = htmlspecialchars(strip_tags($this->name));
		$this->position = htmlspecialchars(strip_tags($this->position));
		$this->company = htmlspecialchars(strip_tags($this->company));
		$this->username = htmlspecialchars(strip_tags($this->username));
		$this->password = htmlspecialchars(strip_tags($this->password));
		$this->photo = htmlspecialchars(strip_tags($this->photo));
        $this->modified = htmlspecialchars(strip_tags($this->modified));
		$this->id = htmlspecialchars(strip_tags($this->id));
	 
		// bind new values
		$stmt->bindParam(':name', $this->name);
		$stmt->bindParam(':position', $this->position);
		$stmt->bindParam(':company', $this->company);
		$stmt->bindParam(':username', $this->username);
		$stmt->bindParam(':password', $this->password);
		$stmt->bindParam(':photo', $this->photo);
        $stmt->bindParam(':modified', $this->modified);
		$stmt->bindParam(':id', $this->id);
	 
		// execute the query
		return $stmt->execute();
    }	
    
	/**
	 * Delete the inspector.
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
    
	/**
	 * Search inspector with username properties.
	 * Returns TRUE or FALSE.
	 */
	public function searchByUsername()
	{
		// select all query
		$query = "SELECT
					id, name, position, company, username, password, photo, created, modified
                FROM
                    " . $this->table_name . "
				WHERE
					username = ?
			ORDER BY
				id DESC";

		// // select all query
		// $query = "SELECT
		// 			c.name as category_name, p.id, p.name, p.value, p.type, p.category_id, p.created
		// 		FROM
		// 			" . $this->table_name . " p
		// 			LEFT JOIN
		// 				categories c
		// 					ON p.category_id = c.id
		// 		WHERE
		// 			p.name LIKE ? OR p.value LIKE ? OR c.name LIKE ?
		// 		ORDER BY
		// 			p.created DESC";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	 
		// // sanitize
		// $this->username = htmlspecialchars(strip_tags($this->username));
		// // $keywords = "%{$keywords}%";
	 
		// bind
		$stmt->bindParam(1, $this->username);
	 
		// execute query
		$stmt->execute();
	 
		// return $stmt;

		$num = $stmt->rowCount();

        // check if more than 0 record found
        if($num>0)
        { 
            // retrieve our table contents
            // fetch() is faster than fetchAll()
            // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                // extract row
                // this will make $row['name'] to
                // just $name only
                extract($row);
        
                // $inspector_item = array(
                //     "id" => $id,
                //     "name" => $name,
                //     "position" => $position,
                //     "company" => $company,
                //     "username" => $username,
                //     "password" => $password,    
                //     "photo" => $photo,                  
                //     "created" => $created,
                //     "modified" => $modified
				// );
				// echo base64_decode($photo);return false;
				// set values to object properties
				$this->id = intval($id);
				$this->name = $name;
				$this->position = $position;
				$this->company = $company;
				$this->username = $username;
				$this->password = $password;		
				$this->photo = $photo; // No need conversion since it is already in an json format
				$this->created = intval($created);
				$this->modified = intval($modified);
		 
				return true;//$inspector_item;
            }
        }
		
		return false;
	}
}
?>