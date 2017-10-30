<?php
session_start();
header('Access-Control-Allow-Origin: *');
date_default_timezone_set("America/Tegucigalpa");
include("smart_resize_image.function.php");
require_once("config/conexion.php");

class configurations{
	private $info;
	private $script = "";
	function configurations(){
		if( isset($_SESSION["user"]) && isset($_SESSION["id"]) ){
		} else {
			header("location:index.html");
		}
		$this->printHTML();
	}
	function printHTML(){
		$file = file_get_contents("templates/configurations.html");
		$file = str_replace("@@MENU@@", file_get_contents("templates/menu.html"), $file);
		$file = str_replace("@@SCRIPT@@", $this->script, $file);
		echo $file;
	}
}

$v = new configurations();

?>