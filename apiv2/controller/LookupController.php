<?php
/**
 *
 * @author Dennis the menace
 */
require_once(__DIR__ . "/../handler/LookupRestHandler.php");

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
        $lookupRestHandler = new LookupRestHandler();
        $lookupRestHandler->getAll();
        break;

    case "single":
        // to handle REST Url /lookup/list/$id
        $lookupRestHandler = new LookupRestHandler();
        $lookupRestHandler->getWithId($_GET["id"]);
        break;

    case "find":
        // to handle REST Url /lookup/find
        $lookupRestHandler = new LookupRestHandler();
        $lookupRestHandler->find($_GET["type"]);
        break;
        
    case "add":
        // to handle REST Url /lookup/add
        $data = json_decode(file_get_contents("php://input"));
        $lookupRestHandler = new LookupRestHandler();
        $lookupRestHandler->create($data);
        break;

    case "edit":
        // to handle REST Url /lookup/update
        $data = json_decode(file_get_contents("php://input"));
        $lookupRestHandler = new LookupRestHandler();
        $lookupRestHandler->update($data);
        break;

    case "remove":
        // to handle REST Url /lookup/delete
        $data = json_decode(file_get_contents("php://input"));
        $lookupRestHandler = new LookupRestHandler();
        $lookupRestHandler->remove($data);
        break;

    case "" :
        //404 - not found;
        break;
}
?>
