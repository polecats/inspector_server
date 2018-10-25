<?php
/**
 *
 * @author Dennis the menace
 */
class Lookup
{
    // database connection and table name
    private $conn;
    private $table_name = "lookups";
 
    // object properties
    public $id;
    public $name;
    public $value;
    public $type;
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
	 * Read lookups and return lookup json array
	 */
	public function readAll()
	{
        //select all data
        $query = "SELECT
                    id, name, value, type, created, modified
                FROM
                    " . $this->table_name . "
                ORDER BY
                    id";
 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
		
		// execute query
		$stmt->execute();
	 
		// lookup array
		$lookup_arr = array();
        $num = $stmt->rowCount();
        
        // check if more than 0 record found
        if($num>0)
        { 
			$lookup_arr["lookup"] = array();

            // retrieve our table contents
            // fetch() is faster than fetchAll()
            // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                // extract row
                // this will make $row['name'] to
                // just $name only
                extract($row);
        
                $lookup_item = array(
                    "id" => intval($id),
                    "name" => $name,
                    "value" => html_entity_decode($value),
                    "type" => intval($type),
                    "created" => intval($created),
                    "modified" => intval($modified)
                );
        
                array_push($lookup_arr["lookup"], $lookup_item);
            }
        }

		return $lookup_arr;
	}	
	
	/**
	 *  used when filling up the update lookup form
	 */
	public function readOne()
	{
		// query to read single record
		$query = "SELECT
					id, name, value, type, created, modified
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
	 
		if ($stmt->rowCount() < 1)
		{
			return false;
		}

		// get retrieved row
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
	 
		// set values to object properties
		$this->id = intval($row['id']);
		$this->name = $row['name'];
		$this->value = $row['value'];
		$this->type = intval($row['type']);
		$this->created = intval($row['created']);
		$this->modified = intval($row['modified']);

		return true;
	}

	/**
	 *  create lookup
	 */
	public function create()
	{
		// query to insert record
		$query = "INSERT INTO
					" . $this->table_name . " (
						name,
						value, 
						type, 
						created, 
						modified)
				VALUES (
					:name,
					:value, 
					:type, 
					:created, 
					:modified)";  					
	 
		// prepare query
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->name = htmlspecialchars(strip_tags($this->name));
		$this->type = htmlspecialchars(strip_tags($this->type));
		$this->value = htmlspecialchars(strip_tags($this->value));
		$this->created = htmlspecialchars(strip_tags($this->created));
		$this->modified = htmlspecialchars(strip_tags($this->modified));
	 
		// bind values
		$stmt->bindParam(":name", $this->name);
		$stmt->bindParam(":type", $this->type);
		$stmt->bindParam(":value", $this->value);
		$stmt->bindParam(":created", $this->created);
		$stmt->bindParam(":modified", $this->modified);

		// execute query
		return $stmt->execute();
	}	

	/**
	 *  update the lookup
	 */
	public function update()
	{
		// update query
		$query = "UPDATE
					" . $this->table_name . "
				SET
					name = :name,
					type = :type,
					value = :value,
					modified = :modified
				WHERE
					id = :id";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->id = htmlspecialchars(strip_tags($this->id));
		$this->name = htmlspecialchars(strip_tags($this->name));
		$this->value = htmlspecialchars(strip_tags($this->value));
		$this->type = htmlspecialchars(strip_tags($this->type));
		$this->modified = htmlspecialchars(strip_tags($this->modified));
	 
		// bind new values
		$stmt->bindParam(':id', $this->id);
		$stmt->bindParam(':name', $this->name);
		$stmt->bindParam(':value', $this->value);
		$stmt->bindParam(':type', $this->type);
		$stmt->bindParam(':modified', $this->modified);
		
		// execute the query
		return $stmt->execute();
	}	
	
	/**
	 *  delete the lookup
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
	 *  search lookups
	 */
	public function searchType($typeId)
	{
		// select all query
		$query = "SELECT
					id, name, value, type, created, modified
                FROM
                    " . $this->table_name . "
				WHERE
					type = ?
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
	 
		// sanitize
		$typeId = htmlspecialchars(strip_tags($typeId));
		// $keywords = "%{$keywords}%";
	 
		// bind
		$stmt->bindParam(1, $typeId);
		// $stmt->bindParam(2, $keywords);
		// $stmt->bindParam(3, $keywords);
	 
		// execute query
		$stmt->execute();
	 
		// lookup array
		$lookup_arr = array();
        $num = $stmt->rowCount();
        
        // check if more than 0 record found
        if($num>0)
        { 
			$lookup_arr["lookup"] = array();

            // retrieve our table contents
            // fetch() is faster than fetchAll()
            // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                // extract row
                // this will make $row['name'] to
                // just $name only
                extract($row);
        
                $lookup_item = array(
                    "id" => intval($id),
                    "name" => $name,
                    "value" => html_entity_decode($value),
                    "type" => intval($type),
                    "created" => intval($created),
                    "modified" => intval($modified)
                );
        
                array_push($lookup_arr["lookup"], $lookup_item);
            }
        }

		return $lookup_arr;
	}

	/**
	 *  read lookups with pagination
	 */
	public function readPaging($from_record_num, $records_per_page)
	{
		// select query
		$query = "SELECT
					id, name, value, type, created, modified
                FROM
                    " . $this->table_name . "
				ORDER BY created DESC
				LIMIT ?, ?";

		// // select query
		// $query = "SELECT
		// 			c.name as category_name, p.id, p.name, p.value, p.type, p.category_id, p.created
		// 		FROM
		// 			" . $this->table_name . " p
		// 			LEFT JOIN
		// 				categories c
		// 					ON p.category_id = c.id
		// 		ORDER BY p.created DESC
		// 		LIMIT ?, ?";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	 
		// bind variable values
		$stmt->bindParam(1, $from_record_num, PDO::PARAM_INT);
		$stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);
	 
		// execute query and return values from database
		return $stmt->execute();
	 
		// // return values from database
		// return $stmt;
	}	
	
	/**
	 *  used for paging lookups
	 */
	public function count()
	{
		$query = "SELECT COUNT(*) as total_rows FROM " . $this->table_name . "";
	 
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
	 
		return $row['total_rows'];
	}	
}
?>