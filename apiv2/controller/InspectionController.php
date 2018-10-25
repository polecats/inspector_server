<?php
/**
 *
 * @author Dennis the menace
 */
require_once(__DIR__ . "/../handler/InspectionRestHandler.php");

$view = "";

if(isset($_GET["view"]))
{
    $view = $_GET["view"];
}    
    
/*
controls the RESTful services
URL mapping
*/
switch($view)
{
    case "all":
        // to handle REST Url /lookup/list
        $inspectionRestHandler = new InspectionRestHandler();
        $inspectionRestHandler->getList();
        break;

    case "single":
        // to handle REST Url /lookup/list/$id
        $inspectionRestHandler = new InspectionRestHandler();
        $inspectionRestHandler->getReport($_GET["id"]);
        break;
        
    case "add":
        // to handle REST Url /lookup/add
        $data = json_decode(file_get_contents("php://input"));
        $inspectionRestHandler = new InspectionRestHandler();
        $inspectionRestHandler->save($data);
        break;

    case "edit":
        // to handle REST Url /lookup/update
        $data = json_decode(file_get_contents("php://input"));
        $inspectionRestHandler = new InspectionRestHandler();
        $inspectionRestHandler->update($data);
        break;

    case "remove":
        // to handle REST Url /lookup/delete
        $data = json_decode(file_get_contents("php://input"));
        $inspectionRestHandler = new LookupRestHandler();
        $inspectionRestHandler->delete($data);
        break;

    case "check":
        // to handle REST Url /lookup/list/$id
        $inspectionRestHandler = new InspectionRestHandler();
        $inspectionRestHandler->checkReport($_GET["id"]);
        break;

    case "" :
        //404 - not found;
        break;
}
?>
