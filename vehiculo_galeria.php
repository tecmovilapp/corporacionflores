<?php
session_start();
header('Access-Control-Allow-Origin: *');
date_default_timezone_set("America/Tegucigalpa");
include("smart_resize_image.function.php");
require_once("config/conexion.php");

class galeria{
	private $info;
	private $script = "";
	function galeria(){
		if( isset($_SESSION["user"]) && isset($_SESSION["id"]) ){
			if( isset($_GET["idcar"]) && isset($_GET["idcat"]) && isset($_FILES["imagen"]) ){
				$this->addImage();
			}
		} else {
			header("location:index.html");
		}
		if(!isset($_GET["idcar"]) || !isset($_GET["idcat"]) ){
			header("Location:vehiculos.php");
		}
		$this->printHTML();
	}
	function printHTML(){
		$file = file_get_contents("templates/vehiculo_galeria.html");
		$file = str_replace("@@CARID@@", $_GET["idcar"], $file);
		$file = str_replace("@@CATID@@", $_GET["idcat"], $file);
		$file = str_replace("@@SCRIPT@@", $this->script, $file);
		echo $file;
	}
	function resizeImg($dir, $file, $newfile, $width, $height){
		smart_resize_image($dir.$file , null, $width , $height , true , $dir.$newfile , true , false ,100 );
		return $newfile;
	}

	function addImage(){
		global $dbh;
		$dbh->exec("set names utf8");
		$dir_subida = 'images/cars/';
		$file_name = "g_".md5(time()."-".rand(0,100)) .".". pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
		$fichero_subido = $dir_subida . $file_name;
		
		if (move_uploaded_file($_FILES['imagen']['tmp_name'], $fichero_subido)) {
			$imageRS = $this->resizeImg($dir_subida, $file_name, "g_rs_".$file_name, 1000, 1000);	
			$stmt = $dbh->prepare("INSERT INTO galeria_vehiculos VALUES(NULL, :IMG,:IDV);");
			$stmt->execute( array(":IMG"=>$imageRS, ":IDV"=>$_GET["idcar"]) );
			$this->script = 'showMessage("Aviso","La Imagen ha sido agregada.")';
		}
		
	}
}

$v = new galeria();

?>