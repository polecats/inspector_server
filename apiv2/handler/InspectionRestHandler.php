<?php
/**
 *
 * @author Dennis the menace
 */
// required classes (must be relative to the main caller class if not using __DIR__ .)
require_once(__DIR__ . "/../core/SimpleRest.php");
require_once(__DIR__ . "/../config/Database.php");
require_once(__DIR__ . "/../data/Inspection.php");
require_once(__DIR__ . "/../shared/Statuses.php");

class InspectionRestHandler extends SimpleRest 
{
    private $database;
    private $db;
    private $inspection;

    /**
     * constructor
     */
	public function __construct()
	{
        $this->database = new Database();
        $this->db = $this->database->getConnection();
        $this->inspection = new Inspection($this->db);     

        // $statusCode = 200;
        // $requestContentType = "Content-Type: application/json; charset=UTF-8";//$_SERVER["HTTP_ACCEPT"];
		// $this->setHttpHeaders($requestContentType, $statusCode);
    }

    /**
     * Get the list of reports available.
     */
    public function getList()
    {
        if ($_SERVER["REQUEST_METHOD"] != "GET")
        {
            echo $this->encodeJson("Wrong resource call", 0xDEAD, NULL);

            return;
        }

        $report_arr = $this->inspection->readAllReport();

        if (count($report_arr) > 0)
        {
            echo $this->encodeJson("Inspections found.", 0x00, $report_arr);
        }
        else
        {
            echo $this->encodeJson("No inspections found.", 0xD1ED, NULL);
        }
    }

    /**
     * Check if a record id exist
     */
    public function checkReport($id)
    {
        if ($_SERVER["REQUEST_METHOD"] != "GET")
        {
            echo $this->encodeJson("Wrong resource call", 0xDEAD, NULL);

            return;
        }

        $this->inspection->id = $id;

        // read the details of product to be edited
        $report_count = $this->inspection->countOne();

        if ($report_count > 0)
        {
            echo $this->encodeJson("Inspections found.", 0x00, $report_count);
        }
        else
        {
            echo $this->encodeJson("No inspections found.", 0xD1ED, NULL);
        }
    }

    /**
     * Retrieve report with id.
     */
    public function getReport($id)
    {
        if ($_SERVER["REQUEST_METHOD"] != "GET")
        {
            echo $this->encodeJson("Wrong resource call", 0xDEAD, NULL);

            return;
        }

        $this->inspection->id = $id;

        // read the details of product to be edited
        if ($this->inspection->readOne())
        {
            // echo json_encode($this->inspection->document);return;
            // create array
            $report_arr = array(
                "id" => $this->inspection->id,
                "name" => $this->inspection->name,
                // "document" => html_entity_decode($this->inspection->document),
                // "document" => json_encode($this->inspection->document),
                "document" => $this->inspection->document,
                "status" => $this->inspection->status,
                "created" => $this->inspection->created,
                "modified" => $this->inspection->modified
            );

            echo $this->encodeJson("Inspections found.", 0x00, $report_arr);
        }
        else
        {
            echo $this->encodeJson("No inspections found.", 0xD1ED, NULL);
        }
    }

    public function update($data)
    {
        if ($_SERVER["REQUEST_METHOD"] != "POST")
        {
            echo $this->encodeJson("Wrong resource call", 0xDEAD, NULL);

            return;
        }
        
        if ($data == NULL)
        {
            echo $this->encodeJson("Unable to update inspections. Data not provided.", 0xDEAD, NULL);

            return;
        }

        // Process data first
        $this->inspection->id = $data->id;
		$this->inspection->name = $data->name;
		$this->inspection->status = Status::ACTIVE;     // Since we are updating, this will make the record active
		$this->inspection->created = $data->timestamp; // Will follow the app reported date
        $this->inspection->modified = $data->timestamp;
        
        // Group the report objects
        // $document_arr = array();
        $document_arr = new stdClass(); // generic empty class for anonymous objects, dynamic properties

        if (isset($data->datasheet))
        {
            // array_push($document_arr["document"], array("datasheet" => $data->datasheet));
            // $document_arr->datasheet = array("datasheet" => $data->datasheet);
            $document_arr->datasheet = $data->datasheet;
        }

        if (isset($data->observation))
        {
            // $document_arr = array("observation" => $data->observation);
            $document_arr->observation = $data->observation;
        }

        if (isset($data->drawings))
        {
            // array_push($document_arr["document"], array("drawings" => $data->drawings));
            // $document_arr = array("drawings" => $data->drawings);
            $document_arr->drawings = $data->drawings;
        }

        if (isset($data->criticals))
        {
            // array_push($document_arr, array("criticals" => $data->criticals));
            $document_arr->criticals = $data->criticals;
        }

        $this->inspection->document = $document_arr;

        if ($this->inspection->update())
        {
            echo $this->encodeJson("Inspections updated.", 0x00, NULL);
        }
        else
        {
            echo $this->encodeJson("Inspections was not updated.", 0xD1ED, NULL);
        }      
    }

    public function save($data)
    {
        if ($_SERVER["REQUEST_METHOD"] != "POST")
        {
            echo $this->encodeJson("Wrong resource call", 0xDEAD, NULL);

            return;
        }

        if ($data == NULL)
        {
            echo $this->encodeJson("Unable to save inspections. Info not provided.", 0xDEAD, NULL);

            return;
        }

        // Process data first
        $this->inspection->id = $data->id;
		$this->inspection->name = $data->name;
		$this->inspection->status = Status::NEW_ENTRY;
		$this->inspection->created = $data->timestamp; // Will follow the app reported date
        $this->inspection->modified = $data->timestamp;
        
        // Group the report objects
        // $document_arr = array();
        $document_arr = new stdClass(); // generic empty class for anonymous objects, dynamic properties

        if (isset($data->datasheet))
        {
            // array_push($document_arr["document"], array("datasheet" => $data->datasheet));
            // $document_arr->datasheet = array("datasheet" => $data->datasheet);
            $document_arr->datasheet = $data->datasheet;
        }

        if (isset($data->observation))
        {
            // $document_arr = array("observation" => $data->observation);
            $document_arr->observation = $data->observation;
        }

        if (isset($data->drawings))
        {
            // array_push($document_arr["document"], array("drawings" => $data->drawings));
            // $document_arr = array("drawings" => $data->drawings);
            $document_arr->drawings = $data->drawings;
        }

        if (isset($data->criticals))
        {
            // array_push($document_arr, array("criticals" => $data->criticals));
            $document_arr->criticals = $data->criticals;
        }

        $this->inspection->document = $document_arr;

        if ($this->inspection->storeAll())
        {
            echo $this->encodeJson("Inspections saved.", 0x00, NULL);
        }
        else
        {
            echo $this->encodeJson("Inspections was not saved.", 0xD1ED, NULL);
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
            echo $this->encodeJson("Unable to delete inspection. Data not provided", 0xDEAD, NULL);

            return;
        }

        // set inspection id to be deleted
        $this->inspection->id = $data->id;
        
        if($this->inspection->delete()) // delete the inspector
        {
            echo $this->encodeJson("Inspection record was deleted", 0x00, NULL);
        }
        else // if unable to delete the inspector
        {
            echo $this->encodeJson("Unable to delete inspection record.", 0xD1ED, NULL);
        }
	}	
}
?>