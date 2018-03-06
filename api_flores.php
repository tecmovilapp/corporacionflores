<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Content-type: application/x-javascript');
date_default_timezone_set("America/Tegucigalpa");
include("smart_resize_image.function.php");
require_once("config/conexion.php");

class cflores{
	
	private $info;
	//private $host = "http://www.yourappland.com/applications/corporacionflores/";
	private $host = "http://10.0.2.2/corporacionflores/";
	//private $host = "http://192.168.200.106/apps/ionic1.7/corporacionflores/";
	//private $hostImg = "http://www.yourappland.com/applications/corporacionflores/images/";
	private $hostImg = "http://10.0.2.2/corporacionflores/images/";

	function cflores(){
		if( isset($_GET["appId"]) && $_GET["appId"]=="0d83420a189fab5cb7197dc431bae006" ){
			if(isset($_GET["action"]) && $_GET['action']=='save-picture' && isset($_FILES["file"]["name"])) {
				$this->savePicture();
			} else if(isset($_GET["action"]) && $_GET['action']=='get-car-category'){
				$this->getCarCategory();
			} else if(isset($_GET["action"]) && $_GET['action']=='get-cars' && isset($_GET["idCarCat"]) ){
				$this->getCars();
			} else if( isset($_GET["action"]) && $_GET['action']=='get-car-data' && isset($_GET["idCar"]) ){
				$this->getFullCarData();
			} else if( isset($_GET["action"]) && $_GET['action']=='get-bac-cf' ){
				$this->getTarjetaBacCF();
			} else if( isset($_GET["action"]) && $_GET['action']=='get-avance-plus' ){
				$this->getAvancePlus();
			} else if( isset($_GET["action"]) && $_GET['action']=='send-avance-request' ){
				$this->sendAvanceRequest();
			} else if( isset($_GET["action"]) && $_GET['action']=='get-ads' ){
				$this->getAds();
			} else if( isset($_GET["action"]) && $_GET['action']=='get-all-cars' ){
				$this->getAllCars();
			} else if( isset($_GET["action"]) && $_GET['action']=='get-data-appointment' ){
				$this->getDataForAppointment();
			} else if( isset($_GET["action"]) && $_GET['action']=='send-appointment' ){
				$this->sendAppointment();
			} else if( isset($_GET["action"]) && $_GET['action']=='send-car-quote'){
				$this->sendCarQuote();
			} else if( isset($_GET["action"]) && $_GET['action']=='send-card-request' ){
				$this->sendCardRequest();
			} else if( isset($_GET["action"]) && $_GET['action']=='send-contact-info' ){
				$this->sendContactInfo();
			} else if( isset($_GET["action"]) && $_GET['action']=='get-locations' ){
				$this->getLocations();
			} else if( isset($_GET["action"]) && $_GET['action']=='get-promos' ){
				$this->getPromos();
			} else if( isset($_GET["action"]) && $_GET['action']=='get-news' ){
				$this->getNews();
			} else if( isset($_GET["action"]) && $_GET['action']=='get-pagos-agencias' ){
				$this->getPagosAgencias();
			} else if( isset($_GET["action"]) && $_GET['action']=='get-contact-info' ){
				$this->getContactInfo();
			}
		}
	}

	function getContactInfo(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT telefono, enlace FROM informacion WHERE id = 'contact';");
		$stmt->execute( array() );
		if($stmt->rowCount() == 1){
       		foreach($stmt as $row){
				$this->info = $row;
			}
		} else{
			$this->info = array();
		}

		echo json_encode($this->info);
	}

	function getPagosAgencias(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM pagos_agencias;");
		$stmt->execute( array() );
		$cont = 0;
		if($stmt->rowCount() > 0){
       		foreach($stmt as $row){
       			$row["nombre"] = stripslashes(utf8_decode($row["nombre"]));
       			$row["direccion"] = stripslashes(utf8_decode($row["direccion"]));
				$row["img"] = $this->hostImg."locationsp/".$row["img"];
       			$this->info[$cont] = $row;
				$cont++;
			}
		} else{
			$this->info = array();
		}
		echo json_encode($this->info);
	}

	function getNews(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM noticias ORDER BY fecha DESC LIMIT 10;");
		$stmt->execute( array() );
		$cont = 0;
		if($stmt->rowCount() > 0){
       		foreach($stmt as $row){
       			$row["imagen"] = $this->hostImg."news/".$row["imagen"];
       			$row["titulo"] = stripslashes(utf8_decode($row["titulo"]));
       			$row["descripcion"] = stripslashes(utf8_decode($row["descripcion"]));
				$this->info[$cont] = $row;
				$cont++;
			}
		} else{
			$this->info = array();
		}
		echo json_encode($this->info);
	}

	function getPromos(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM promociones ORDER BY fecha DESC;");
		$stmt->execute( array() );
		$cont = 1;
		$cont1 = 0;
		$myRow = array();
		if($stmt->rowCount() > 0){
       		foreach($stmt as $row){
       			$row["imagen"] = $this->hostImg.$row["imagen"];
       			$row["titulo"] = stripslashes(utf8_decode($row["titulo"]));
       			$row["descripcion"] = stripslashes(utf8_decode($row["descripcion"]));
				if($cont%2 != 0){
					$myRow[0] = $row;
				} else {
					$myRow[1] = $row;
					$this->info[0][$cont1] = $myRow;
					$myRow = array();
					$cont1++;
				}
				$cont++;
			}
			if(!empty($myRow)){
				$myRow[1] = array("id"=>-1,"titulo"=>"","descripcion"=>"");
				$this->info[0][$cont1] = $myRow;
				$myRow = array();
			}
		} else{
			$this->info[0] = array();
		}

		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM promociones ORDER BY titulo DESC;");
		$stmt->execute( array() );
		$cont1 = 0;
		$cont = 1;
		
		if($stmt->rowCount() > 0){
       		foreach($stmt as $row){
       			$row["imagen"] = $this->hostImg.$row["imagen"];
       			$row["titulo"] = stripslashes(utf8_decode($row["titulo"]));
       			$row["descripcion"] = stripslashes(utf8_decode($row["descripcion"]));
				if($cont%2 != 0){
					$myRow[0] = $row;
				} else {
					$myRow[1] = $row;
					$this->info[1][$cont1] = $myRow;
					$cont1++;
				}
				$cont++;
			}
			if(!empty($myRow)){
				$myRow[1] = array("id"=>-1,"titulo"=>"","descripcion"=>"");
				$this->info[1][$cont1] = $myRow;
				$myRow = array();
			}
		} else{
			$this->info[1] = array();
		}

		echo json_encode($this->info);
	}

	function getLocations(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM ubicaciones;");
		$stmt->execute( array() );
		$cont = 0;
		if($stmt->rowCount() > 0){
       		foreach($stmt as $row){
       			$row["img"] = $this->hostImg."locations/".$row["img"];
       			$row["nombre"] = stripslashes(utf8_decode($row["nombre"]));
       			$row["direccion"] = stripslashes(utf8_decode($row["direccion"]));
				$this->info[$cont] = $row;
				$cont++;
			}
		} else{
			$this->info = array();
		}
		echo json_encode($this->info);
	}

	function sendContactInfo(){
		$nombre = $_GET["nombre"];
		$tel = $_GET["telefono"];
		$email = $_GET["email"];
		$tipo = $_GET["tipo"];
		$comment = $_GET["comentario"];

		$title = $tipo." - enviado desde el APP de corporacion flores";
		$content1 = "Nombre: ".$nombre."<br>Telefono: ".$tel."<br>Email: ".$email;
		$content2 = $tipo.":<br>".$comment;
		$this->sendEmail($title,$content1,$content2,$this->getEmailFor("contact"), $email);

		echo json_encode(array("result"=>1));
	}

	function sendCardRequest(){
		$nombre = $_GET["nombre"];
		$tel = $_GET["telefono"];
		$email = $_GET["email"];
		$ciudad = $_GET["ciudad"];
		$identidad = $_GET["identidad"];

		$title = "Solicitud de informacion sobre las tarjetas BAC";
		$content1 = "Nombre: ".$nombre."<br>Telefono: ".$tel."<br>Email: ".$email;
		$content1 .= "<br>Ciudad: ".$ciudad."<br>Identidad: ".$identidad;
		$content2 = "";
		$this->sendEmail($title,$content1,$content2,$this->getEmailFor("bac-cf"), $email);

		echo json_encode(array("result"=>1));
	}

	function sendAvanceRequest() {
		$nombre = $_GET["nombre"];
		$tel = $_GET["telefono"];
		$email = $_GET["email"];

		$title = "Solicitud de informacion del plan Avance Plus";
		$content1 = "Nombre: ".$nombre."<br>Telefono: ".$tel."<br>Email: ".$email;
		$content2 = "";
		$this->sendEmail($title,$content1,$content2,$this->getEmailFor("avance-plus"), $email);

		echo json_encode(array("result"=>1));
	}

	function sendCarQuote(){
		$nombre = $_GET["nombre"];
		$tel = $_GET["telefono"];
		$email = $_GET["email"];
		$ciudad = $_GET["ciudad"];
		$idCar = $_GET["idCarro"];

		$title = "Solicitud para cotizacion";
		$content1 = "Nombre: ".$nombre."<br>Telefono: ".$tel."<br>Email: ".$email;
		$content1 .= "<br>Ciudad: ".$ciudad;
		$content2 = $this->getCarInfo($idCar);
		$this->sendEmail($title,$content1,$content2,$this->getEmailFor("quote"), $email);

		echo json_encode(array("result"=>1));
	}

	function getCarInfo($id){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT nombre, year, foto FROM vehiculos WHERE id = :ID;");
		$stmt->execute( array(":ID"=>$id) );
		$data = "<b>Vehiculo a cotizar:</b><br>";
		if($stmt->rowCount() == 1){
			$row = $stmt->fetch();
       		$data .= "Nombre: ".stripslashes($row["nombre"]);
       		$data .= "<br>A&ntilde;o: ".$row["year"];
       		$data .= "<br><img src='".$this->hostImg."cars/".$row["foto"]."' style='max-width:300px;'/>";
		}
		return $data;
	}

	function sendAppointment(){
		$nombre = $_GET["nombre"];
		$tel = $_GET["telefono"];
		$email = $_GET["email"];
		$carro = $_GET["carro"];
		$placa = $_GET["placa"];
		$ciudad = $_GET["ciudad"];
		$tipo = $_GET["tipo"];
		$taller = $_GET["taller"];
		$fecha = $_GET["fecha"];
		$nota = $_GET["nota"];
		$trans = $_GET["transporte"];
		$avance = $_GET["avance"];

		$title = "Solicitud para cita de servicios";
		$content1 = "Nombre: ".$nombre."<br>Telefono: ".$tel."<br>Email: ".$email;
		$content1 .= "<br>Fecha y hora: ".$fecha."<br>Ciudad: ".$ciudad;
		$content1 .= "<br>Necesita Transporte: ".(($trans=="true")?"Si":"No")."<br>Afiliado al plan de Avance plus: ".(($avance=="true")?"Si":"No");
		$content1 .= "<br>Nota adicional: ".$nota;
		$content2 = "Vehiculo: ".$carro."<br>Placa: ".$placa."<br>Tipo: ".$tipo;
		$content2 .= "<br>Taller: ".$taller;

		$this->sendEmail($title,$content1,$content2,$this->getEmailFor("appointment"), $email);

		echo json_encode(array("result"=>1));
	}

	function getEmailFor($name){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT correo FROM informacion WHERE id = :ID;");
		$stmt->execute( array(":ID"=>$name) );
		$data = "";
		if($stmt->rowCount() > 0){
       		$row = $stmt->fetch();
			$data = $row["correo"];
		} else{
			$data = "";
		}
		return $data;
	}

	function getDataForAppointment() {
		$this->info[0] = $this->getTipos();
		$this->info[1] = $this->getTalleres();
		echo json_encode($this->info);
	}

	function getTipos(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM tipos;");
		$stmt->execute( array() );
		$data = array();
		$cont = 0;
		if($stmt->rowCount() > 0){
       		foreach($stmt as $row){
				$data[$cont] = $row;
				$cont++;
			}
		} else{
			$data = array();
		}
		return $data;
	}

	function getTalleres(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM talleres;");
		$stmt->execute( array() );
		$data = array();
		$cont = 0;
		if($stmt->rowCount() > 0){
       		foreach($stmt as $row){
				$data[$cont] = $row;
				$cont++;
			}
		} else{
			$this->info = array();
		}
		return $data;
	}

	function savePicture() {
		
		$allowedExts = array("jpg", "jpeg");
		$extension = explode(".", $_FILES["file"]["name"]);
		$fileEx = $extension[sizeof($extension)-1];
		if ($_FILES["file"]["type"] == "image/jpeg" && $_FILES["file"]["size"] < 20000000 && in_array($extension[sizeof($extension)-1], $allowedExts)) {
			if ($_FILES["file"]["error"] > 0) {
				echo 0;
			} else {
				$dir = "imguser";
				if (!is_dir($dir)) {
					 mkdir($dir, 0777, true);
				}
				$name = md5(time()).".".$fileEx;
				move_uploaded_file($_FILES["file"]["tmp_name"], $dir."/" . $name);
				$file= $dir."/" . $name;
				$filer = $dir."/r" . $name;
				smart_resize_image($file , null, 500 , 500 , true , $filer, true , false ,100 );
      
			    echo $name."@".$this->host.$filer;
			}
		} else {
			echo 0;
		}
	}

	function getCarCategory() {
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT id, nombre FROM categoria_vehiculos ORDER BY id ASC;");
		$stmt->execute( array() );
		$cont = 0;
		if($stmt->rowCount() > 0){
       		foreach($stmt as $row){
       			$row["nombre"] = stripslashes(utf8_decode($row["nombre"]));
				$this->info[$cont] = $row;
				$cont++;
			}
		} else{
			$this->info = array();
		}
		echo json_encode($this->info);
	}

	function getCars(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM vehiculos WHERE id_categoria=:ID;");
		$stmt->execute( array(":ID"=>$_GET["idCarCat"]) );
		$cont = 0;
		if($stmt->rowCount() > 0){
       		foreach($stmt as $row){
       			$row["foto"] = $this->hostImg."cars/".$row["foto"];
       			$row["nombre"] = stripslashes(utf8_decode($row["nombre"]));
       			$row["descripcion"] = stripslashes(utf8_decode($row["descripcion"]));
				$this->info[1][$cont] = $row;
				$cont++;
			}
			$this->info[0] = $this->getCategoryName($_GET["idCarCat"]);
		} else{
			$this->info[0] = $this->getCategoryName($_GET["idCarCat"]);
			$this->info[1] = array();
		}
		echo json_encode($this->info);
	}

	function getAllCars(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT DISTINCT nombre as model FROM modelos ORDER BY nombre;");
		$stmt->execute( array() );
		$cont = 0;
		if($stmt->rowCount() > 0){
       		foreach($stmt as $row){
       			$row["model"] = stripslashes(utf8_decode($row["model"]));
				$this->info[$cont] = $row;
				$cont++;
			}
		} else{
			$this->info = array();
		}
		echo json_encode($this->info);
	}

	function getCategoryName($id){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT nombre FROM categoria_vehiculos WHERE id = :ID;");
		$stmt->execute( array(":ID"=>$id) );
		if($stmt->rowCount() == 1){
       		foreach($stmt as $row){
				return stripslashes($row["nombre"]);
			}
		} else{
			return array();
		}
	}

	function getFullCarData(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM vehiculos WHERE id = :ID;");
		$stmt->execute( array(":ID"=>$_GET["idCar"]) );
		if($stmt->rowCount() == 1){
       		foreach($stmt as $row){
       			$row["nombre"] = stripslashes(utf8_decode($row["nombre"]));
       			$row["descripcion"] = stripslashes(utf8_decode($row["descripcion"]));
				$this->info[0] = $row;
				$this->info[1][0] = array("id"=>"a1","url"=>$this->hostImg."cars/".$row["foto"]."?".time());
			}
		}

		$stmt = $dbh->prepare("SELECT * FROM galeria_vehiculos WHERE id_vehiculo = :ID;");
		$stmt->execute( array(":ID"=>$_GET["idCar"]) );
		$cont = 1;
		if($stmt->rowCount() > 0){
       		foreach($stmt as $row){
       			$row["url"] = $this->hostImg."cars/".$row["url"];
				$this->info[1][$cont] = $row;
				$cont++;
			}
		} /*else {
			$this->info[1] = array();
		}*/

		echo json_encode($this->info);

	}

	function getTarjetaBacCF(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM informacion WHERE id = 'bac-cf';");
		$stmt->execute( array() );
		if($stmt->rowCount() == 1){
       		foreach($stmt as $row){
       			$row["descripcion"] = stripslashes(utf8_decode($row["descripcion"]));
				$this->info = $row;
			}
		} else{
			$this->info = array();
		}

		echo json_encode($this->info);
	}

	function getAvancePlus(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM informacion WHERE id = 'avance-plus';");
		$stmt->execute( array() );
		if($stmt->rowCount() == 1){
       		foreach($stmt as $row){
       			$row["descripcion"] = stripslashes(utf8_decode($row["descripcion"]));
				$this->info = $row;
			}
		} else{
			$this->info = array();
		}

		echo json_encode($this->info);
	}

	function getAds(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM publicidad AS p1 JOIN (SELECT CEIL(RAND() * (SELECT MAX(id) FROM publicidad)) AS id) AS p2 WHERE p1.id >= p2.id ORDER BY p1.id ASC LIMIT 1");
		$stmt->execute( array() );
		if($stmt->rowCount() == 1){
       		foreach($stmt as $row){
       			$row["imagenSmall"] = $this->hostImg."ads/".$row["imagenSmall"];
       			$row["imagenLarge"] = $this->hostImg."ads/".$row["imagenLarge"];
				$this->info = $row;
			}
		} else{
			$this->info = array();
		}

		for($i=0;$i<10000000;$i++){}

		echo json_encode($this->info);
	}

	function sendEmail($title, $content1, $content2, $emailto, $emailfrom){
		$title = utf8_decode($title);
		$content1 = utf8_decode($content1);
		$content2 = utf8_decode($content2);

		$message = file_get_contents("templates/email_template.html");
		$message = str_replace("@@TITLE@@",$title, $message);
		$message = str_replace("@@CONTENT1@@",$content1, $message);
		$message = str_replace("@@CONTENT2@@",$content2, $message);
		$subject = $title.' - APP';  //change subject of email 

		$headers  = "From: " . $emailfrom . "\r\n"; 
		$headers .= "Reply-To: ". $emailfrom . "\r\n"; 
		//$headers .= "CC: flazarus@tecmovil.net \r\n"; 
		$headers .= "MIME-Version: 1.0\r\n"; 
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		mail($emailto, $subject, $message, $headers);
	}
}

$cfloresAPI = new cflores();

?>