<?php

$uriPart=str_replace($_SERVER["SCRIPT_NAME"],"",$_SERVER["REQUEST_URI"]);
if (strpos($uriPart,"?")!==FALSE)
	$uriPart=substr($uriPart,0,strpos($uriPart,"?"));

$uriComponents=explode("/",$uriPart);
$path=array();
foreach ($uriComponents as $uriComponent)
	if ($uriComponent)
		$path[]=$uriComponent;

if (sizeof($path)<1)
	$path[0]="";

switch ($path[0]) {
	case "":
		$data=array(
			"devices"=>array("test","device2"),
		);
		echo json_encode($data,JSON_PRETTY_PRINT);
		break;

	case "test":
		switch ($path[1]) {
			case "status":
				$data=array(
					"fields"=>array(
						array(
							"key"=>"one",
							"type"=>"text",
							"name"=>"One",
							"value"=>"hello"
						),

						array(
							"key"=>"two",
							"type"=>"text",
							"name"=>"Two",
							"value"=>"world"
						),
					),
				);
				echo json_encode($data,JSON_PRETTY_PRINT);
				break;
		}
		break;

	default:
		$data=array(
			"ok"=>1
		);
		echo json_encode($data,JSON_PRETTY_PRINT);
		break;
}
