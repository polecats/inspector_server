<?php
/**
 *
 * @author Dennis the menace
 */
// required classes (must be relative to the main caller class if not using __DIR__ .)
require_once(__DIR__ . "/../core/SimpleRest.php");
require_once(__DIR__ . "/../config/Database.php");
require_once(__DIR__ . "/../data/Inspector.php");

class InspectorRestHandler extends SimpleRest 
{
    private $database;
    private $db;
    private $inspector;

    /**
     * constructor
     */
	public function __construct()
	{
        $this->database = new Database();
        $this->db = $this->database->getConnection();
        $this->inspector = new Inspector($this->db);        

        // $statusCode = 200;
        // $requestContentType = "Content-Type: application/json; charset=UTF-8";//$_SERVER['HTTP_ACCEPT'];
		// $this->setHttpHeaders($requestContentType, $statusCode);
    }

    /**
     * Check the login details and return the login data
     */
    public function login($username, $password)
    {
        if ($_SERVER["REQUEST_METHOD"] != "POST")
        {
            echo $this->encodeJson("Wrong resource call", 0xDEAD, NULL);

            return;
        }

        if ($username == NULL && $password == NULL)
        {
            echo $this->encodeJson("Unable to login. Info not provided", 0xDEAD, NULL);

            return;
        }

        // Sanitize
        $this->inspector->username = htmlspecialchars(strip_tags($username));

        if (!$this->inspector->searchByUsername())
        {
            echo $this->encodeJson("Could not find user", 0xD1ED, NULL);

            return;           
        }

        if(password_verify($password, $this->inspector->password))
        {
            echo $this->encodeJson("User login successful.", 0x00, $this->inspector);
        }
        else
        {
            echo $this->encodeJson("User password does not match.", 0xD1ED, NULL);
        }
    }

    /**
     * Logs out the inspector
     */
    public function logout()
    {
        if ($_SERVER["REQUEST_METHOD"] != "GET")
        {
            echo $this->encodeJson("Wrong resource call", 0xDEAD, NULL);

            return;
        }

        // For now do nothing
        echo $this->encodeJson("User logout.", 0x00, NULL);
    }

	/**
     * Retrieve all inspector.
     */
	public function getAll()
	{
        if ($_SERVER["REQUEST_METHOD"] != "GET")
        {
            echo $this->encodeJson("Wrong resource call", 0xDEAD, NULL);

            return;
        }

        $inspector_arr = $this->inspector->readAll();

        if (count($inspector_arr) > 0)
        {
            echo $this->encodeJson("Inspector found.", 0x00, $inspector_arr);
        }
        else
        {
            echo $this->encodeJson("No inspector found.", 0xD1ED, NULL);
        }
    }	
    
    /**
     * Update inspector
     */
	public function update($data)
	{
        if ($_SERVER["REQUEST_METHOD"] != "PUT" && $_SERVER["REQUEST_METHOD"] != "POST")
        {
            echo $this->encodeJson("Wrong resource call", 0xDEAD, NULL);

            return;
        }

        if ($data == NULL)
        {
            echo $this->encodeJson("Unable to update inspector. Info not provided.", 0xDEAD, NULL);

            return;
        }

        // Get timestamp (now, today, unixtime)
        $timestamp = time();

        // First get the original data
        // set ID property of inspector to be edited
        $this->inspector->id = $data->id;
        
        if (!$this->inspector->readOne())
        {
            echo $this->encodeJson("Unable to update inspector. Could not find record.", 0xD1ED, NULL);

            return;          
        }

        // set inspector property values
        // to the retrieved data.
        // Dont allow change username
        $this->inspector->name = isset($data->name) ? $data->name : $this->inspector->name;
        $this->inspector->position = isset($data->position) ? $data->position : $this->inspector->position;
        $this->inspector->company = isset($data->company) ? $data->company : $this->inspector->company;
        $this->inspector->photo = isset($data->photo) ? $data->photo : $this->inspector->photo;
        $this->inspector->modified = $timestamp;
        
        // Handle password field
        if (isset($data->password))
        {
            // Validate that the provided password is the same by verifying it
            if(!password_verify($data->password, $this->inspector->password))
            {
                // Password does not match, lets change it
                $this->inspector->password = password_hash($data->password, PASSWORD_BCRYPT); 
            }
        }

        if($this->inspector->update()) // update the inspector
        {
            echo $this->encodeJson("Inspector was updated", 0x00, NULL);
        } 
        else // if unable to update the inspector, tell the user
        {
            echo $this->encodeJson("Unable to update inspector.", 0xD1ED, NULL);
        }  
    }
    
    /**
     * Create inspector
     */
	public function create($data)
	{
        if ($_SERVER["REQUEST_METHOD"] != "POST")
        {
            echo $this->encodeJson("Wrong resource call", 0xDEAD, NULL);

            return;
        }

        if ($data == NULL)
        {
            echo $this->encodeJson("Unable to create inspector. Info not provided", 0xDEAD, NULL);

            return;
        }

        // Get timestamp (now, today, unixtime)
        $timestamp = time();

        // set inspector property values
        $this->inspector->name = $data->name;
        $this->inspector->position = $data->position;
        $this->inspector->company = $data->company;
        $this->inspector->username = $data->username;
        $this->inspector->photo = $data->photo;
        $this->inspector->created = $timestamp;
        $this->inspector->modified = $timestamp;

        // Get password hash
        $this->inspector->password = password_hash($data->password, PASSWORD_BCRYPT); 

        if($this->inspector->create()) // create the inspector
        {
            echo $this->encodeJson("Inspector was created", 0x00, NULL);
        }
        else // if unable to create the inspector, tell the user
        {
            echo $this->encodeJson("Unable to create inspector.", 0xD1ED, NULL);
        }
    }    
    
    /**
     * Delete the inspector
     */
	public function delete($data)
	{
        if ($_SERVER["REQUEST_METHOD"] != "DELETE" && $_SERVER["REQUEST_METHOD"] != "POST")
        {
            echo $this->encodeJson("Wrong resource call", 0xDEAD, NULL);

            return;
        }

        if ($data == NULL)
        {
            echo $this->encodeJson("Unable to delete inspector. Info not provided", 0xDEAD, NULL);

            return;
        }

        // set inspector id to be deleted
        $this->inspector->id = $data->id;
        
        if($this->inspector->delete()) // delete the inspector
        {
            echo $this->encodeJson("Inspector was deleted", 0x00, NULL);
        }
        else // if unable to delete the inspector
        {
            echo $this->encodeJson("Unable to delete inspector.", 0xD1ED, NULL);
        }
	}	
}
?>