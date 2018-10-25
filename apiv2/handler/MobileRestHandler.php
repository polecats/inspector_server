<?php
/**
 *
 * @author Dennis the menace
 */
require_once(__DIR__ . "/../core/SimpleRest.php");
require_once(__DIR__ . "/../data/Mobile.php");
		
class MobileRestHandler extends SimpleRest 
{
	function getAllMobiles() 
	{	
		$mobile = new Mobile();
		$rawData = $mobile->getAllMobile();

		if(empty($rawData)) 
		{
			$statusCode = 404;
			$rawData = array("error" => "No mobiles found!");		
		} 
		else 
		{
			$statusCode = 200;
		}

		$requestContentType = $_SERVER["HTTP_ACCEPT"];
		// echo $this->encodeJson($requestContentType, JSON_UNESCAPED_SLASHES);return;
		$this->setHttpHeaders($requestContentType, $statusCode);
				
		if(strpos($requestContentType,"application/json") !== false)
		{
			$response = $this->encodeJson($rawData);
			echo $response;
		} 
		else if(strpos($requestContentType,"text/html") !== false)
		{
			$response = $this->encodeHtml($rawData);
			echo $response;
		} 
		else if(strpos($requestContentType,"application/xml") !== false)
		{
			$response = $this->encodeXml($rawData);
			echo $response;
		}
	}
	
	public function getMobile($id) 
	{
		$mobile = new Mobile();
		$rawData = $mobile->getMobile($id);

		if(empty($rawData)) 
		{
			$statusCode = 404;
			$rawData = array("error" => "No mobiles found!");		
		} 
		else 
		{
			$statusCode = 200;
		}

		$requestContentType = $_SERVER["HTTP_ACCEPT"];
		$this ->setHttpHeaders($requestContentType, $statusCode);
				
		if(strpos($requestContentType,"application/json") !== false)
		{
			$response = $this->encodeJson($rawData);
			echo $response;
		} 
		else if(strpos($requestContentType,"text/html") !== false)
		{
			$response = $this->encodeHtml($rawData);
			echo $response;
		} 
		else if(strpos($requestContentType,"application/xml") !== false)
		{
			$response = $this->encodeXml($rawData);
			echo $response;
		}
	}
}
?>