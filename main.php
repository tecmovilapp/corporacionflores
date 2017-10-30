<?php
session_start();
header('Access-Control-Allow-Origin: *');
date_default_timezone_set("America/Tegucigalpa");
include("smart_resize_image.function.php");
require_once("config/conexion.php");

class main{
	private $info;
	private $script = "";

	function main(){
		if( isset($_SESSION["user"]) && isset($_SESSION["id"]) ){
			if(isset($_POST["titulo"]) && isset($_POST["contenido"]) && isset($_POST["url"]) && isset ( $_FILES ['imagen'] )){
				$this->createPromo();
			} else if(isset($_POST["tituloedit"]) && isset($_POST["contenidoedit"]) && isset($_POST["urledit"]) && isset ( $_POST['editpromoid'] )){
				$this->updatePromo();
			} else if(isset($_POST["deletepromoid"])){
				$this->deletePromo();
			}
		} else {
			header("location:index.html");
		}

		$this->printHTML();
	}

	function getPromos(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM promociones ORDER BY id DESC;");
		$stmt->execute( array() );
		$cont = 0;

		if($stmt->rowCount() > 0){
       		foreach($stmt as $row){
       			$row["titulo"] = stripslashes(utf8_decode($row["titulo"]));
       			$row["descripcion"] = stripslashes(utf8_decode($row["descripcion"]));
       			$this->info[$cont] = $row;
				$cont++;
			}
		} else{
			$this->info = array();
		}
		return $this->info;
	}

	function createPromo(){
		global $dbh;
		$dbh->exec("set names utf8");
		$dir_subida = 'images/';
		$file_name = md5(time()."-".rand(0,100)) .".". pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
		$fichero_subido = $dir_subida . $file_name;
		if (move_uploaded_file($_FILES['imagen']['tmp_name'], $fichero_subido)) {
			$stmt = $dbh->prepare("INSERT INTO promociones VALUES(NULL, :TITLE, :CONTENT,:IMAGE,:URL,:D);");
			$stmt->execute( array(":TITLE"=>utf8_encode($_POST["titulo"]), ":CONTENT"=>utf8_encode($_POST["contenido"]), ":IMAGE"=>$file_name, ":URL"=>$_POST["url"],":D"=>date("Y-m-d H:i:s")) );
			$this->script = 'showMessage("Aviso","La promocion ha sido agregada.")';
		}
	}

	function getOldImage($id){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT imagen FROM promociones WHERE id = :ID;");
		$stmt->execute( array(":ID"=>$id) );

		if($stmt->rowCount() == 1){
       		$row = $stmt->fetch();
       		$imagen = $row["imagen"];
		} else{
			$imagen = "0";
		}
		return $imagen;
	}

	function updatePromo(){//PENDIENTE
		global $dbh;
		$dbh->exec("set names utf8");
		$dir_subida = 'images/';

		if(isset ( $_FILES ['imagenedit'] )){
			$file_name = md5(time()."-".rand(0,100)) .".". pathinfo($_FILES['imagenedit']['name'], PATHINFO_EXTENSION);
			$fichero_subido = $dir_subida . $file_name;
			if (move_uploaded_file($_FILES['imagenedit']['tmp_name'], $fichero_subido)) {
				try {//ELIMINAMOS LA IMAGEN ANTERIOR
				unlink($dir_subida.$this->getOldImage($_POST["editpromoid"]));
				} catch (Exception $e) {}
				$stmt = $dbh->prepare("UPDATE promociones SET imagen = :IMAGE WHERE id =:ID;");
				$stmt->execute( array(":IMAGE"=>$file_name, ":ID"=>$_POST["editpromoid"]) );
				
			}
		}

		$stmt = $dbh->prepare("UPDATE promociones SET titulo=:T, descripcion=:C, enlace=:E WHERE id =:ID;");
		$stmt->execute( array(":T"=>utf8_encode($_POST["tituloedit"]), ":C"=>utf8_encode($_POST["contenidoedit"]), ":E"=>$_POST["urledit"], ":ID"=>$_POST["editpromoid"]) );

		$this->script = 'showMessage("Aviso","La promocion ha sido actualizada.")';
	}

	function deletePromo(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT imagen FROM promociones WHERE id = :ID;");
		$stmt->execute( array(":ID"=>$_POST["deletepromoid"]));
		if($stmt->rowCount()==1){
			$row = $stmt->fetch();
			try {
				unlink('images/'.$row["imagen"]);
			} catch (Exception $e) {}

			$stmt = $dbh->prepare("DELETE FROM promociones WHERE id = :ID;");
			$stmt->execute( array(":ID"=>$_POST["deletepromoid"]));
		}

	}

	function printHTML(){
		$file = file_get_contents("templates/main.html");
		$file = str_replace("@@MENU@@", file_get_contents("templates/menu.html"), $file);
		$data = $this->getPromos();
		$promos = "";
		for($i=0;$i<count($data);$i++){
			$promos .= '<li><div class="collapsible-header"><span>'.$data[$i]["fecha"].'</span> - '.$data[$i]["titulo"].'</div>';
			$promos .= '<div class="collapsible-body"><a class="waves-effect waves-light btn gflores btnEditPromo" style="float:right;margin:10px;" edit-id="'.$data[$i]["id"].'">Editar</a><a class="waves-effect waves-light btn gflores deletePromo" style="float:right;margin:10px;" item-id="'.$data[$i]["id"].'">Eliminar</a><form method="POST" id="deletePromoForm'.$data[$i]["id"].'"><input type="hidden" value="'.$data[$i]["id"].'" name="deletepromoid"/></form><br><img src="images/'.$data[$i]["imagen"].'" style="width:50%;margin-left:20%;margin-top:10px;"/><p>'.$data[$i]["descripcion"].'<br><br>Enlace: '.$data[$i]["enlace"].'</p></div></li>';
			$promos .= '<div style="display:none;"><span id="editTitulo'.$data[$i]["id"].'">'.$data[$i]["titulo"].'</span><span id="editContenido'.$data[$i]["id"].'">'.$data[$i]["descripcion"].'</span><span id="editUrl'.$data[$i]["id"].'">'.$data[$i]["enlace"].'</span></div>';
		}
		$file = str_replace("@@PROMOS@@", $promos, $file);
		$file = str_replace("@@SCRIPT@@", $this->script, $file);
		echo $file;
	}
}

$mainClass = new main();
?>