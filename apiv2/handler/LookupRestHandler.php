<?php
/**
 *
 * @author Dennis the menace
 */
// required classes (must be relative to the main caller class if not using __DIR__ .)
require_once(__DIR__ . "/../core/SimpleRest.php");
require_once(__DIR__ . "/../config/Database.php");
require_once(__DIR__ . "/../data/Lookup.php");

class LookupRestHandler extends SimpleRest 
{
    private $database;
    private $db;
    private $lookup;

    /**
     * constructor
     */
	public function __construct()
	{
        $this->database = new Database();
        $this->db = $this->database->getConnection();
        $this->lookup = new Lookup($this->db);     

        // $statusCode = 200;
        // $requestContentType = "Content-Type: application/json; charset=UTF-8";//$_SERVER["HTTP_ACCEPT"];
		// $this->setHttpHeaders($requestContentType, $statusCode);
    }

	/**
     * Retrieve all lookup.
     */
	public function getAll()
	{
        if ($_SERVER["REQUEST_METHOD"] != "GET")
        {
            echo $this->encodeJson("Wrong resource call", 0xDEAD, NULL);

            return;
        }

        $lookup_arr = $this->lookup->readAll();

        if (count($lookup_arr) > 0)
        {
            echo $this->encodeJson("Lookup found.", 0x00, $lookup_arr);
        }
        else
        {
            echo $this->encodeJson("No lookup found.", 0xD1ED, NULL);
        }
	}	

    /**
     * Retrieve lookup based on id.
     */
	function getWithId($id)
	{
        if ($_SERVER["REQUEST_METHOD"] != "GET")
        {
            echo $this->encodeJson("Wrong resource call", 0xDEAD, NULL);

            return;
        }

        $this->lookup->id = $id;

        // read the details of product to be edited
        $this->lookup->readOne();
        
        // create array
        $lookup_arr = array(
            "id" => $this->lookup->id,
            "name" => $this->lookup->name,
            "value" => html_entity_decode($this->lookup->value),
            "type" => $this->lookup->type,
            "created" => $this->lookup->created,
            "modified" => $this->lookup->modified
        );

        echo $this->encodeJson("Lookup found", 0x00, $lookup_arr);
    }
 
    /**
     * Search lookup
     */
	function find($type)
	{
        if ($_SERVER["REQUEST_METHOD"] != "GET")
        {
            echo $this->encodeJson("Wrong resource call", 0xDEAD, NULL);

            return;
        }

        $lookup_arr = $this->lookup->searchType($type);

        if (count($lookup_arr) > 0)
        {
            echo $this->encodeJson("Lookup found.", 0x00, $lookup_arr);
        }
        else
        {
            echo $this->encodeJson("No lookup found.", 0xD1ED, NULL);
        }
    }

    /**
     * Update lookup
     */
	function update($data)
	{
        if ($_SERVER["REQUEST_METHOD"] != "PUT" && $_SERVER["REQUEST_METHOD"] != "POST")
        {
            echo $this->encodeJson("Wrong resource call", 0xDEAD, NULL);

            return;
        }

        if ($data == NULL)
        {
            echo $this->encodeJson("Unable to update lookup. Info not provided", 0xDEAD, NULL);

            return;
        }

        // Get timestamp (now, today, unixtime)
        $timestamp = time();

        // set ID property of lookup to be edited
        $this->lookup->id = $data->id;
        
        // set lookup property values
        $this->lookup->name = $data->name;
        $this->lookup->value = $data->value;
        $this->lookup->modified = $timestamp;
        
        if($this->lookup->update()) // update the lookup
        {
            echo $this->encodeJson("Lookup was updated.", 0x00, NULL);
        } 
        else // if unable to update the lookup, tell the user
        {
            echo $this->encodeJson("Unable to update lookup.", 0xD1ED, NULL);
        }
	}

    /**
     * Create lookup
     */
	function create($data)
	{
        if ($_SERVER["REQUEST_METHOD"] != "POST")
        {
            echo $this->encodeJson("Wrong resource call", 0xDEAD, NULL);

            return;
        }

        if ($data == NULL)
        {
            echo $this->encodeJson("Unable to create lookup. Info not provided", 0xDEAD, NULL);

            return;
        }

        // Get timestamp (now, today, unixtime)
        $timestamp = time();

        // set lookup property values
        $this->lookup->name = $data->name;
        $this->lookup->value = $data->price;
        $this->lookup->type = $data->description;
        $this->lookup->created = $timestamp;
        $this->lookup->modified = $timestamp;

        if($this->lookup->create()) // create the lookup
        {
            echo $this->encodeJson("Lookup was created.", 0x00, NULL);
        }
        else // if unable to create the lookup, tell the user
        {
            echo $this->encodeJson("Unable to create lookup.", 0xD1ED, NULL);
        }
	}
	
	/**
     * Delete the lookup
     */
	function remove($data)
	{
        if ($_SERVER["REQUEST_METHOD"] != "DELETE" && $_SERVER["REQUEST_METHOD"] != "POST")
        {
            echo $this->encodeJson("Wrong resource call", 0xDEAD, NULL);

            return;
        }

        if ($data == NULL)
        {
            echo $this->encodeJson("Unable to delete lookup. Info not provided", 0xDEAD, NULL);

            return;
        }

        // set lookup id to be deleted
        $this->lookup->id = $data->id;
        
        if($this->lookup->delete()) // delete the lookup
        {
            echo $this->encodeJson("Lookup was deleted.", 0x00, NULL);
        }
        else // if unable to delete the lookup
        {
            echo $this->encodeJson("Unable to delete lookup.", 0xD1ED, NULL);
        }
	}	
}
?>