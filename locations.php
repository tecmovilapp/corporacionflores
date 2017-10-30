<?php
session_start();
header('Access-Control-Allow-Origin: *');
date_default_timezone_set("America/Tegucigalpa");
include("smart_resize_image.function.php");
require_once("config/conexion.php");

class locations{
	private $info;
	private $script = "";
	function locations(){
		if( isset($_SESSION["user"]) && isset($_SESSION["id"]) ){
			if(isset($_POST["nombrel"]) && isset($_POST["direccion"]) && isset($_POST["telefono"]) && isset($_POST["latitud"]) && isset($_POST["longitud"]) && isset ( $_FILES ['imagen'] )){
				$this->createLocation();
			} else if( isset($_POST["nombreledit"]) && isset($_POST["direccionedit"]) && isset($_POST["telefonoedit"]) && isset($_POST["latitudedit"]) && isset($_POST["longitudedit"]) && isset ($_POST['editlocationid']) ){
				$this->updateLocation();
			} else if(isset($_POST["deletelocationid"])){
				$this->deleteLocation();
			}
		} else {
			header("location:index.html");
		}
		$this->printHTML();
	}

	function deleteLocation(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT img FROM ubicaciones WHERE id = :ID;");
		$stmt->execute( array(":ID"=>$_POST["deletelocationid"]));
		if($stmt->rowCount()==1){
			$row = $stmt->fetch();
			try {
				@unlink('images/locations/'.$row["img"]);
			} catch (Exception $e) {}

			$stmt = $dbh->prepare("DELETE FROM ubicaciones WHERE id = :ID;");
			$stmt->execute( array(":ID"=>$_POST["deletelocationid"]));
			$this->script = 'showMessage("Aviso","La ubicaci&oacute;n ha sido eliminada.")';
		}
	}

	function deleteOldImage($id){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT img FROM ubicaciones WHERE id = :ID;");
		$stmt->execute( array(":ID"=>$id) );

		if($stmt->rowCount() == 1){
       		$row = $stmt->fetch();
       		$imagen = $row["img"];
       		try {
       			@unlink("images/locations/".$imagen);
       		} catch (Exception $e) {}
		} 
	}

	function updateLocation(){
		global $dbh;
		$dbh->exec("set names utf8");
		$dir_subida = 'images/locations/';

		if(isset ( $_FILES ['imagenedit'] )){
			$file_name = md5(time()."-".rand(0,100)) .".". pathinfo($_FILES['imagenedit']['name'], PATHINFO_EXTENSION);
			$fichero_subido = $dir_subida . $file_name;
			if (move_uploaded_file($_FILES['imagenedit']['tmp_name'], $fichero_subido)) {
				$this->deleteOldImage($_POST["editlocationid"]);
				$stmt = $dbh->prepare("UPDATE ubicaciones SET img = :IMAGE WHERE id =:ID;");
				$stmt->execute( array(":IMAGE"=>$file_name, ":ID"=>$_POST["editlocationid"]) );
			}
		}

		$stmt = $dbh->prepare("UPDATE ubicaciones SET nombre=:N, direccion=:D, telefono=:T, lat=:L, lng=:LN WHERE id =:ID;");
		$stmt->execute( array(":N"=>utf8_encode($_POST["nombreledit"]), ":D"=>utf8_encode($_POST["direccionedit"]), ":T"=>$_POST["telefonoedit"], ":L"=>$_POST["latitudedit"], ":LN"=>$_POST["longitudedit"], ":ID"=>$_POST["editlocationid"]) );

		$this->script = 'showMessage("Aviso","La ubicacion ha sido actualizada.")';
	}

	function createLocation(){
		global $dbh;
		$dbh->exec("set names utf8");
		$dir_subida = 'images/locations/';
		$file_name = md5(time()."-".rand(0,100)) .".". pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
		$fichero_subido = $dir_subida . $file_name;
		if (move_uploaded_file($_FILES['imagen']['tmp_name'], $fichero_subido)) {
			$stmt = $dbh->prepare("INSERT INTO ubicaciones VALUES(NULL, :NAME, :DIR, :PHONE, :LAT, :LONG, :IMG);");
			$stmt->execute( array(":NAME"=>utf8_encode($_POST["nombrel"]), ":DIR"=>utf8_encode($_POST["direccion"]), ":PHONE"=>$_POST["telefono"], ":LAT"=>$_POST["latitud"], ":LONG"=>$_POST["longitud"], ":IMG"=>$file_name) );
			$this->script = 'showMessage("Aviso","La ubicaci&oacute;n ha sido agregada.")';
		}
	}

	function getLocations(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM ubicaciones ORDER BY id DESC;");
		$stmt->execute( array() );
		$cont = 0;

		if($stmt->rowCount() > 0){
       		foreach($stmt as $row){
       			$row["nombre"] = stripslashes(utf8_decode($row["nombre"]));
       			$row["direccion"] = stripslashes(utf8_decode($row["direccion"]));
       			$this->info[$cont] = $row;
				$cont++;
			}
		} else{
			$this->info = array();
		}
		return $this->info;
	}

	function printHTML(){
		$file = file_get_contents("templates/locations.html");
		$file = str_replace("@@MENU@@", file_get_contents("templates/menu.html"), $file);
		$data = $this->getLocations();
		$lct = "";
		for($i=0;$i<count($data);$i++){
			$lct .= '<li><div class="collapsible-header">'.$data[$i]["nombre"].'</div>';
			$lct .= '<div class="collapsible-body"><a class="waves-effect waves-light btn gflores btnEditLct" style="float:right;margin:10px;" edit-id="'.$data[$i]["id"].'">Editar</a><a class="waves-effect waves-light btn gflores deleteLct" style="float:right;margin:10px;" item-id="'.$data[$i]["id"].'">Eliminar</a><form method="POST" id="deleteLctForm'.$data[$i]["id"].'" action="locations.php"><input type="hidden" value="'.$data[$i]["id"].'" name="deletelocationid"/></form><br><img src="images/locations/'.$data[$i]["img"].'" style="width:50%;margin-left:20%;margin-top:10px;"/><p>'.$data[$i]["direccion"];
			$lct .='<br><br><a class="btn waves-effect waves-light gflores" href="https://www.google.com/maps/place/'.$data[$i]["lat"].'+'.$data[$i]["lng"].'/@'.$data[$i]["lat"].','.$data[$i]["lng"].',15z" target="_BLANK" name="action">Ver ubicacion<i class="material-icons left">room</i></a></p></div></li>';
			$lct .= '<div style="display:none;"><span id="editName'.$data[$i]["id"].'">'.$data[$i]["nombre"].'</span><span id="editDirection'.$data[$i]["id"].'">'.$data[$i]["direccion"].'</span><span id="editPhone'.$data[$i]["id"].'">'.$data[$i]["telefono"].'</span><span id="editLat'.$data[$i]["id"].'">'.$data[$i]["lat"].'</span><span id="editLong'.$data[$i]["id"].'">'.$data[$i]["lng"].'</span></div>';
		}

		$file = str_replace("@@HELPCOOR@@", file_get_contents("templates/help_coor.html"), $file );
		$file = str_replace("@@LOCATIONS@@", $lct, $file);
		$file = str_replace("@@SCRIPT@@", $this->script, $file);
		echo $file;
	}
}

$l = new locations();

?>