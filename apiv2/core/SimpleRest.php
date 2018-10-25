<?php 
/**
 * A simple RESTful webservices base class
 * Use this as a template and build upon it
 * 
 * @author Dennis the menace
 */
class SimpleRest 
{
	private $httpVersion = "HTTP/1.1";

	public function setHttpHeaders($contentType, $statusCode)
	{
		$statusMessage = $this->getHttpStatusMessage($statusCode);
		
		header($this->httpVersion. " ". $statusCode ." ". $statusMessage);		
		header("Content-Type:". $contentType);
		// header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
		header("Access-Control-Max-Age: 3600");
		header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
	}
	
	public function getHttpStatusMessage($statusCode)
	{
		$httpStatus = array(
			100 => "Continue",  
			101 => "Switching Protocols",  
			200 => "OK",
			201 => "Created",  
			202 => "Accepted",  
			203 => "Non-Authoritative Information",  
			204 => "No Content",  
			205 => "Reset Content",  
			206 => "Partial Content",  
			300 => "Multiple Choices",  
			301 => "Moved Permanently",  
			302 => "Found",  
			303 => "See Other",  
			304 => "Not Modified",  
			305 => "Use Proxy",  
			306 => "(Unused)",  
			307 => "Temporary Redirect",  
			400 => "Bad Request",  
			401 => "Unauthorized",  
			402 => "Payment Required",  
			403 => "Forbidden",  
			404 => "Not Found",  
			405 => "Method Not Allowed",  
			406 => "Not Acceptable",  
			407 => "Proxy Authentication Required",  
			408 => "Request Timeout",  
			409 => "Conflict",  
			410 => "Gone",  
			411 => "Length Required",  
			412 => "Precondition Failed",  
			413 => "Request Entity Too Large",  
			414 => "Request-URI Too Long",  
			415 => "Unsupported Media Type",  
			416 => "Requested Range Not Satisfiable",  
			417 => "Expectation Failed",  
			500 => "Internal Server Error",  
			501 => "Not Implemented",  
			502 => "Bad Gateway",  
			503 => "Service Unavailable",  
			504 => "Gateway Timeout",  
			505 => "HTTP Version Not Supported");
		return ($httpStatus[$statusCode]) ? $httpStatus[$statusCode] : $status[500];
	}

	public function encodeHtml($responseData) 
	{
		$this->setHttpHeaders("Content-Type: text/html", 200);

		$htmlResponse = "<table border='1'>";

		foreach($responseData as $key=>$value) 
		{
    		$htmlResponse .= "<tr><td>". $key. "</td><td>". $value. "</td></tr>";
		}

		$htmlResponse .= "</table>";

		return $htmlResponse;		
	}
	
	public function encodeJson($status, $code, $responseData) 
	{
        // // headers for not caching the results
        // // header("Cache-Control: no-cache, must-revalidate");
        // // header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        
        // // headers to tell that result is JSON
        // header("Content-Type: application/json; charset=UTF-8");
        // // header("Content-type: application/json");
        // // header("Access-Control-Allow-Origin: *');
        // header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        // header("Access-Control-Allow-Methods: GET, DELETE, PUT, POST, OPTIONS");
        // header("Access-Control-Max-Age: 3600");
        // header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        // // Access-Control headers are received during OPTIONS requests
        // if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") 
        // {
        //     if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
        //         // may also be using PUT, PATCH, HEAD etc
        //         header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");         

        //     if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]))
        //         header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        //     exit(0);
        // }

		$this->setHttpHeaders("Content-Type: application/json; charset=UTF-8", 200);
				
		$result_json = array("status" => $status, "code" => $code, "data" => $responseData);
		$jsonResponse = json_encode($result_json, JSON_UNESCAPED_SLASHES);

		return $jsonResponse;		
	}
	
	public function encodeXml($responseData) 
	{
		$this->setHttpHeaders("Content-Type: application/xml", 200);

		// creating object of SimpleXMLElement
		$xml = new SimpleXMLElement('<?xml version="1.0"?><response></response>');

		foreach($responseData as $key=>$value)
		{
			$xml->addChild($key, $value);
		}

		return $xml->asXML();
	}

	public function logger($msg)
	{
		error_log($msg, 3, "c:\\temp\\inspipe-api-errors.log");
	}
}
?>