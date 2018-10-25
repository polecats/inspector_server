<?php
/**
 *
 * @author Dennis the menace
 */
class RestResponse
{ 
    /**
     * All parameter values should be JSON compatible
     */
    public function send($status, $code, $data)
    {
        $result_json = array("status" => $status, "code" => $code, "data" => $_SERVER["HTTP_ORIGIN"]);//$data);

        // headers for not caching the results
        // header("Cache-Control: no-cache, must-revalidate");
        // header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        
        // headers to tell that result is JSON
        header("Content-Type: application/json; charset=UTF-8");
        // header("Content-type: application/json");
        // header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header("Access-Control-Allow-Methods: GET, DELETE, PUT, POST, OPTIONS");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") 
        {
            if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");         

            if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }

        // send the result now
        echo json_encode($result_json, JSON_UNESCAPED_SLASHES);
    }
}
?>