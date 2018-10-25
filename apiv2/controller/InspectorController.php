<?php
/**
 *
 * @author Dennis the menace
 */
require_once(__DIR__ . "/../handler/InspectorRestHandler.php");

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
    case "login":
        // to handle REST Url /login
        $username = isset($_GET["u"]) ? $_GET["u"] : NULL;//die();
        $password = isset($_GET["p"]) ? $_GET["p"] : NULL;//die();

        $inspectorRestHandler = new InspectorRestHandler();
        $inspectorRestHandler->login($username, $password);
        break;
        
    case "signup":
        // to handle REST Url /signup & /user/update
        $data = json_decode(file_get_contents("php://input"));
        $inspectorRestHandler = new InspectorRestHandler();
        $inspectorRestHandler->create($data);
        break;

    case "logout":
        // to handle REST Url /logout
        $inspectorRestHandler = new InspectorRestHandler();
        $inspectorRestHandler->logout();
        break;

    case "all":
        // to handle REST Url /user/list
        $inspectorRestHandler = new InspectorRestHandler();
        $inspectorRestHandler->getAll();
        break;

    // case "single":
    //     // to handle REST Url /user/list/$id
    //     $inspectorRestHandler = new InspectorRestHandler();
    //     $inspectorRestHandler->logout();
    //     break;

    case "edit":
        // to handle REST Url /user/update
        $data = json_decode(file_get_contents("php://input"));
        $inspectorRestHandler = new InspectorRestHandler();
        $inspectorRestHandler->update($data);
        break;

    case "remove":
        // to handle REST Url /user/delete
        $data = json_decode(file_get_contents("php://input"));
        $inspectorRestHandler = new InspectorRestHandler();
        $inspectorRestHandler->delete($data);
        break;

    case "" :
        //404 - not found;
        break;
}
?>
