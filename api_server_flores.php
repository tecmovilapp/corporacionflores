<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Content-type: application/x-javascript');
date_default_timezone_set("America/Tegucigalpa");
include("smart_resize_image.function.php");
require_once("config/conexion.php");

class scflores{
	private $info;
	//private $host = "http://www.yourappland.com/applications/corporacionflores/";
	private $host = "http://localhost/corporacionflores/";
	//private $host = "http://localhost/apps/ionic1.7/corporacionflores/";
	private $hostImg = "http://www.yourappland.com/applications/corporacionflores/images/";

	function scflores(){
		if( isset($_GET["appId"]) && $_GET["appId"]=="f87be42e48579e1d6fb8103fa556b869" ){
			if( isset($_GET["action"]) && $_GET['action']=='do-login' && isset($_GET["user"]) && isset($_GET["pass"]) ){
				$this->doLogin();
			} else if(isset($_GET["action"]) && $_GET['action']=='check-session'){
				if( isset($_SESSION["user"]) && isset($_SESSION["id"]) ){
					$this->info["result"] = 1; 
					echo json_encode($this->info);
				} else {
					$this->info["result"] = 0; 
					echo json_encode($this->info);
				}//FIN VALIDAMOS SI HAY SESION
			} else if( isset($_GET["action"]) && $_GET['action']=='get-cars-by-cat' && isset($_GET["idcat"]) ){
				$this->getCarsByCat();
			} else if( isset($_GET["action"]) && $_GET['action']=='delete-car' && isset($_GET["idcar"]) ){
				$this->deleteCar();
			} else if( isset($_GET["action"]) && $_GET['action']=='get-car-gallery' && isset($_GET["idcar"]) ){
				$this->getGallery();
			} else if( isset($_GET["action"]) && $_GET['action']=='delete-image' && isset($_GET["idimg"]) ) {
				$this->deletePicture();
			} else if( isset($_GET["action"]) && $_GET['action']=='get-quote-info' ){
				$this->getInfoQuote();
			} else if( isset($_GET["action"]) && $_GET['action']=='update-quote-info' && isset($_GET["emailq"]) ){
				$this->updateInfoQuote();
			} else if( isset($_GET["action"]) && $_GET['action']=='get-contact-info' ){
				$this->getInfoContact();
			} else if( isset($_GET["action"]) && $_GET['action']=='update-contact-info' && isset($_GET["emailc"]) && isset($_GET["phone"]) && isset($_GET["url"]) ){
				$this->updateInfoContact();
			} else if( isset($_GET["action"]) && $_GET['action']=='get-bac-info' ){
				$this->getInfoBAC();
			} else if( isset($_GET["action"]) && $_GET['action']=='update-bac-info' && isset($_GET["emailbac"]) && isset($_GET["contenido"]) && isset($_GET["pdf"]) ){
				$this->updateInfoBAC();
			} else if( isset($_GET["action"]) && $_GET['action']=='get-avance-info' ){
				$this->getInfoAvance();
			} else if( isset($_GET["action"]) && $_GET['action']=='update-avance-info' && isset($_GET["emailavance"]) && isset($_GET["contenido"]) && isset($_GET["pdf"]) && isset($_GET["video"]) ){
				$this->updateInfoAvance();
			} else if( isset($_GET["action"]) && $_GET['action']=='get-types' ){
				$this->getTypes();
			} else if( isset($_GET["action"]) && $_GET['action']=='create-type' && isset($_GET["nombret"]) ){
				$this->createType();
			} else if( isset($_GET["action"]) && $_GET['action']=='delete-type' && isset($_GET["idtype"]) ){
				$this->deleteType();
			} else if( isset($_GET["action"]) && $_GET['action']=='get-workshops' ){
				$this->getWorkshops();
			} else if( isset($_GET["action"]) && $_GET['action']=='create-workshop' && isset($_GET["nombrew"]) ){
				$this->createWorkshop();
			} else if( isset($_GET["action"]) && $_GET['action']=='delete-workshop' && isset($_GET["idworkshop"]) ){
				$this->deleteWorkshop();
			} else if( isset($_GET["action"]) && $_GET['action']=='get-appointment-info' ){
				$this->getInfoAppointment();
			} else if( isset($_GET["action"]) && $_GET['action']=='update-appointment-info' && isset($_GET["emailap"]) ){
				$this->updateInfoAppointment();
			} else if( isset($_GET["action"]) && $_GET['action']=='get-models' ){
				$this->getModels();
			} else if( isset($_GET["action"]) && $_GET['action']=='create-model' && isset($_GET["nombrem"]) ){
				$this->createModel();
			} else if( isset($_GET["action"]) && $_GET['action']=='delete-model' && isset($_GET["idmodel"]) ){
				$this->deleteModel();
			}
		}
	}

	function createModel(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("REPLACE INTO modelos VALUES(:NAME);");
		$stmt->execute( array(":NAME"=>str_replace("\\","",(str_replace('"','',str_replace("'","",$_GET["nombrem"]))))) );
		$this->info["result"] = 1;
		
		echo json_encode($this->info);
	}

	function deleteModel(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("DELETE FROM modelos WHERE nombre = :ID;");
		$stmt->execute( array(":ID"=>$_GET["idmodel"]) );
		$this->info["result"] = 1;
		
		echo json_encode($this->info);
	}

	function getModels(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM modelos ORDER BY nombre;");
		$stmt->execute( array() );
		$cont = 0;

		if($stmt->rowCount() > 0){
       		foreach($stmt as $row){
       			$row["nombre"] = $row["nombre"];
       			$this->info[$cont] = $row;
				$cont++;
			}
		} else{
			$this->info = array();
		}
		echo json_encode($this->info);
	}

	function updateInfoAppointment(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("UPDATE informacion SET correo=:EMAIL WHERE id='appointment';");
		$stmt->execute( array(":EMAIL"=>$_GET["emailap"]) );
		$this->info["result"] = 1;
		echo json_encode($this->info);
	}

	function getInfoAppointment(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT correo FROM informacion WHERE id='appointment';");
		$stmt->execute( array() );
		$cont = 0;
		if($stmt->rowCount() == 1){
       		$row = $stmt->fetch();
			$this->info = $row;
		} else{
			$this->info = array();
		}
		echo json_encode($this->info);
	}

	function createWorkshop(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("INSERT INTO talleres VALUES(NULL, :NAME);");
		$stmt->execute( array(":NAME"=>$_GET["nombrew"]) );
		$this->info["result"] = 1;
		
		echo json_encode($this->info);
	}

	function deleteWorkshop(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("DELETE FROM talleres WHERE id = :ID;");
		$stmt->execute( array(":ID"=>$_GET["idworkshop"]) );
		$this->info["result"] = 1;
		
		echo json_encode($this->info);
	}

	function getWorkshops(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM talleres ORDER BY nombre;");
		$stmt->execute( array() );
		$cont = 0;

		if($stmt->rowCount() > 0){
       		foreach($stmt as $row){
       			$this->info[$cont] = $row;
				$cont++;
			}
		} else{
			$this->info = array();
		}
		echo json_encode($this->info);
	}

	function createType(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("INSERT INTO tipos VALUES(NULL, :NAME);");
		$stmt->execute( array(":NAME"=>$_GET["nombret"]) );
		$this->info["result"] = 1;
		
		echo json_encode($this->info);
	}

	function deleteType(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("DELETE FROM tipos WHERE id = :ID;");
		$stmt->execute( array(":ID"=>$_GET["idtype"]) );
		$this->info["result"] = 1;
		
		echo json_encode($this->info);
	}

	function getTypes(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM tipos ORDER BY nombre;");
		$stmt->execute( array() );
		$cont = 0;

		if($stmt->rowCount() > 0){
       		foreach($stmt as $row){
       			$this->info[$cont] = $row;
				$cont++;
			}
		} else{
			$this->info = array();
		}
		echo json_encode($this->info);
	}

	function updateInfoAvance(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("UPDATE informacion SET correo=:EMAIL, descripcion=:D, pdf=:P, video=:V WHERE id='avance-plus';");
		$stmt->execute( array(":EMAIL"=>$_GET["emailavance"], ":D"=>utf8_encode($_GET["contenido"]), ":P"=>$_GET["pdf"], ":V"=>$_GET["video"]) );
		$this->info["result"] = 1;
		echo json_encode($this->info);
	}

	function getInfoAvance(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT descripcion, correo, pdf, video FROM informacion WHERE id='avance-plus';");
		$stmt->execute( array() );
		if($stmt->rowCount() == 1){
       		$row = $stmt->fetch();
       		$row["descripcion"] = utf8_decode($row["descripcion"]);
			$this->info = $row;
		} else{
			$this->info = array();
		}
		echo json_encode($this->info);
	}

	function updateInfoBAC(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("UPDATE informacion SET correo=:EMAIL, descripcion=:D, pdf=:P WHERE id='bac-cf';");
		$stmt->execute( array(":EMAIL"=>$_GET["emailbac"], ":D"=>utf8_encode($_GET["contenido"]), ":P"=>$_GET["pdf"]) );
		$this->info["result"] = 1;
		echo json_encode($this->info);
	}

	function getInfoBAC(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT descripcion, correo, pdf FROM informacion WHERE id='bac-cf';");
		$stmt->execute( array() );
		$cont = 0;
		if($stmt->rowCount() == 1){
       		$row = $stmt->fetch();
       		$row["descripcion"] = utf8_decode( $row["descripcion"] );
			$this->info = $row;
		} else{
			$this->info = array();
		}
		echo json_encode($this->info);
	}

	function updateInfoContact(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("UPDATE informacion SET correo=:EMAIL, telefono=:T, enlace=:E WHERE id='contact';");
		$stmt->execute( array(":EMAIL"=>$_GET["emailc"], ":T"=>$_GET["phone"], ":E"=>$_GET["url"]) );
		$this->info["result"] = 1;
		echo json_encode($this->info);
	}

	function getInfoContact(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT telefono, correo, enlace FROM informacion WHERE id='contact';");
		$stmt->execute( array() );
		$cont = 0;
		if($stmt->rowCount() == 1){
       		$row = $stmt->fetch();
			$this->info = $row;
		} else{
			$this->info = array();
		}
		echo json_encode($this->info);
	}

	function updateInfoQuote(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("UPDATE informacion SET correo=:EMAIL WHERE id='quote';");
		$stmt->execute( array(":EMAIL"=>$_GET["emailq"]) );
		$this->info["result"] = 1;
		echo json_encode($this->info);
	}

	function getInfoQuote(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT correo FROM informacion WHERE id='quote';");
		$stmt->execute( array() );
		$cont = 0;
		if($stmt->rowCount() == 1){
       		$row = $stmt->fetch();
			$this->info = $row;
		} else{
			$this->info = array();
		}
		echo json_encode($this->info);
	}

	function deletePicture(){
		global $dbh;
		$dbh->exec("set names utf8");
		$this->deleteOldImage($_GET["idimg"]);
		$stmt = $dbh->prepare("DELETE FROM galeria_vehiculos WHERE id=:ID;");
		$stmt->execute( array(":ID"=>$_GET["idimg"]) );
		$this->info["result"] = 1;
		echo json_encode($this->info);
	}

	function deleteOldImage($id){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT url FROM galeria_vehiculos WHERE id = :ID;");
		$stmt->execute( array(":ID"=>$id) );
        $dir = 'images/cars/';
		if($stmt->rowCount() == 1){
       		$row = $stmt->fetch();
       		try {
       			unlink($dir.$row["url"]);
       		} catch (Exception $e) {}
		}
	}

	function getGallery(){//RECUPERA LA GALERIA DE IAMGENES
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM galeria_vehiculos WHERE id_vehiculo=:ID;");
		$stmt->execute( array(":ID"=>$_GET["idcar"]) );
		$cont = 0;
		if($stmt->rowCount() > 0){
       		foreach($stmt as $row){
       			//$row["url"] = $this->hostImg."cars/".$row["url"];
				$this->info[$cont] = $row;
				$cont++;
			}
		} else{
			$this->info = array();
		}
		echo json_encode($this->info);
	}

	function deleteCar(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT foto  FROM vehiculos WHERE id = :ID");
		$stmt->execute( array(":ID"=>$_GET["idcar"]));
		if($stmt->rowCount()==1){
			$row = $stmt->fetch();
			try {
				@unlink('images/cars/'.$row["foto"]);
			} catch (Exception $e) {}

			$this->deleteCarGallery($_GET["idcar"]);
			$stmt = $dbh->prepare("DELETE FROM vehiculos WHERE id = :ID");
			$stmt->execute( array(":ID"=>$_GET["idcar"]) );
		}
	}

	function deleteCarGallery($idCar){//ELIMINA TODA LA GALERIA DE IMAGENES CUANDO SE ELIMINA EL VEHICULO
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT url FROM galeria_vehiculos WHERE id_vehiculo = :ID;");
		$stmt->execute( array(":ID"=>$idCar) );
        $dir = 'images/cars/';
		if($stmt->rowCount() > 0){
       		foreach($stmt as $row){
       			try {
       				@unlink($dir.$row["url"]);
       			} catch (Exception $e) {}
       		}
		}
		$stmt = $dbh->prepare("DELETE FROM galeria_vehiculos WHERE id_vehiculo=:ID;");
		$stmt->execute( array(":ID"=>$idCar) );
		
		$this->info["result"]= 1;
		echo json_encode($this->info);
	}

	function getCarsByCat() {
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM vehiculos WHERE id_categoria=:ID;");
		$stmt->execute( array(":ID"=>$_GET["idcat"]) );
		$cont = 0;
		if($stmt->rowCount() > 0){
       		foreach($stmt as $row){
       			$row["nombre"] = stripslashes(utf8_decode($row["nombre"]));
       			$row["descripcion"] = stripslashes(utf8_decode($row["descripcion"]));
				$this->info[$cont] = $row;
				$cont++;
			}
		} else{
			$this->info = array();
		}
		echo json_encode($this->info);
	}

	function doLogin(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM usuarios WHERE usuario=:USER AND password=:PASS;");
		$stmt->execute( array(":USER"=>$_GET["user"], ":PASS"=>md5($_GET["pass"])) );
		$cont = 0;
		if($stmt->rowCount() == 1){
			$row = $stmt->fetch();
			$_SESSION["user"] = $row["usuario"];
			$_SESSION["id"] = $row["id"];
			$stmt = $dbh->prepare("UPDATE usuarios SET last_login = :LAST WHERE id=:ID");
			$stmt->execute( array(":LAST"=>date("Y-m-d H:i:s"),":ID"=>$row["id"]) );
			$this->info["result"] = 1; 
		} else {
			$this->info["result"] = 0; 
		}

		for ($i=0; $i < 10000000; $i++) {}

		echo json_encode($this->info);
	}
}

$scfloresAPI = new scflores();

?>