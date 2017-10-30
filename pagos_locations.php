<?php
session_start();
header('Access-Control-Allow-Origin: *');
date_default_timezone_set("America/Tegucigalpa");
include("smart_resize_image.function.php");
require_once("config/conexion.php");

class locationsp{
	private $info;
	private $script = "";
	function locationsp(){
		if( isset($_SESSION["user"]) && isset($_SESSION["id"]) ){
			if(isset($_POST["nombrelp"]) && isset($_POST["direccion"]) && isset($_POST["url"]) && isset($_FILES["imagen"]) ){
				$this->createLocation();
			} else if( isset($_POST["nombrelpedit"]) && isset($_POST["direccionedit"]) && isset($_POST["urledit"]) && isset($_FILES["imagenedit"]) && isset ($_POST['editlocationpid']) ){
				$this->updateLocation();
			} else if(isset($_POST["deletelocationpid"])){
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
		$stmt = $dbh->prepare("SELECT img FROM pagos_agencias WHERE id = :ID;");
		$stmt->execute( array(":ID"=>$_POST["deletelocationpid"]));
		if($stmt->rowCount()==1){
			$row = $stmt->fetch();
			try {
				@unlink('images/locationsp/'.$row["img"]);
			} catch (Exception $e) {}
			$stmt = $dbh->prepare("DELETE FROM pagos_agencias WHERE id = :ID;");
			$stmt->execute( array(":ID"=>$_POST["deletelocationpid"]));
			$this->script = 'showMessage("Aviso","La ubicaci&oacute;n ha sido eliminada.")';
		}
	}

	function deleteOldImage($id){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT img FROM pagos_agencias WHERE id = :ID;");
		$stmt->execute( array(":ID"=>$id) );

		if($stmt->rowCount() == 1){
       		$row = $stmt->fetch();
       		$imagen = $row["img"];
       		try {
       			@unlink("images/locationsp/".$imagen);
       		} catch (Exception $e) {}
		} 
	}

	function updateLocation(){
		global $dbh;
		$dbh->exec("set names utf8");
		$dir_subida = 'images/locationsp/';

		if(isset ( $_FILES ['imagenedit'] )){
			$file_name = md5(time()."-".rand(0,100)) .".". pathinfo($_FILES['imagenedit']['name'], PATHINFO_EXTENSION);
			$fichero_subido = $dir_subida . $file_name;
			if (move_uploaded_file($_FILES['imagenedit']['tmp_name'], $fichero_subido)) {
				$this->deleteOldImage($_POST["editlocationpid"]);
				$imageRS = $this->resizeImg($dir_subida, $file_name, "rs_".$file_name, 300, 300);	
				$stmt = $dbh->prepare("UPDATE pagos_agencias SET img = :IMAGE WHERE id =:ID;");
				$stmt->execute( array(":IMAGE"=>$imageRS, ":ID"=>$_POST["editlocationpid"]) );
			}
		}

		$stmt = $dbh->prepare("UPDATE pagos_agencias SET nombre=:N, direccion=:D, url=:E WHERE id =:ID;");
		$stmt->execute( array(":N"=>utf8_encode($_POST["nombrelpedit"]), ":D"=>utf8_encode($_POST["direccionedit"]), ":E"=>$_POST["urledit"], ":ID"=>$_POST["editlocationpid"]) );

		$this->script = 'showMessage("Aviso","La ubicacion ha sido actualizada.")';
	}

	function createLocation(){
		global $dbh;
		$dbh->exec("set names utf8");
		$dir_subida = 'images/locationsp/';
		$file_name = md5(time()."-".rand(0,100)) .".". pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
		$fichero_subido = $dir_subida . $file_name;
		if (move_uploaded_file($_FILES['imagen']['tmp_name'], $fichero_subido)) {	
			$imageRS = $this->resizeImg($dir_subida, $file_name, "rs_".$file_name, 300, 300);	
			$stmt = $dbh->prepare("INSERT INTO pagos_agencias VALUES(NULL, :NAME, :DIR, 0, 0, 0,:IMG,:URL);");
			$stmt->execute( array(":NAME"=>utf8_encode($_POST["nombrelp"]), ":DIR"=>utf8_encode($_POST["direccion"]), ":IMG"=>$imageRS, ":URL"=>$_POST["url"]) );
			$this->script = 'showMessage("Aviso","La ubicacion ha sido agregada.")';
		}
	}

	function resizeImg($dir, $file, $newfile, $width, $height){
		smart_resize_image($dir.$file , null, $width , $height , false , $dir.$newfile , true , false ,100 );
		return $newfile;
	}

	function getLocations(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM pagos_agencias ORDER BY id DESC;");
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
		$file = file_get_contents("templates/pagos_locations.html");
		$file = str_replace("@@MENU@@", file_get_contents("templates/menu.html"), $file);
		$data = $this->getLocations();
		$lct = "";
		for($i=0;$i<count($data);$i++){
			$lct .= '<li><div class="collapsible-header">'.$data[$i]["nombre"].'</div>';
			$lct .= '<div class="collapsible-body"><a class="waves-effect waves-light btn gflores btnEditLctp" style="float:right;margin:10px;" edit-id="'.$data[$i]["id"].'">Editar</a><a class="waves-effect waves-light btn gflores deleteLctp" style="float:right;margin:10px;" item-id="'.$data[$i]["id"].'">Eliminar</a><form method="POST" id="deleteLctpForm'.$data[$i]["id"].'" action="pagos_locations.php"><input type="hidden" value="'.$data[$i]["id"].'" name="deletelocationpid"/></form><img src="images/locationsp/'.$data[$i]["img"].'" style="margin-left:20px;margin-top:10px;"/><p>'.$data[$i]["direccion"];
			$lct .='<br><img src=""/><br>Enlace: '.$data[$i]["url"].'<br><!--a class="btn waves-effect waves-light gflores" href="https://www.google.com/maps/place/'.$data[$i]["lat"].'+'.$data[$i]["lng"].'/@'.$data[$i]["lat"].','.$data[$i]["lng"].',15z" target="_BLANK" name="action">Ver ubicaci&oacute;n<i class="material-icons left">room</i></a--></p></div></li>';
			$lct .= '<div style="display:none;"><span id="editName'.$data[$i]["id"].'">'.$data[$i]["nombre"].'</span><span id="editDirection'.$data[$i]["id"].'">'.$data[$i]["direccion"].'</span><span id="editPhone'.$data[$i]["id"].'">'.$data[$i]["telefono"].'</span><span id="editLat'.$data[$i]["id"].'">'.$data[$i]["lat"].'</span><span id="editLong'.$data[$i]["id"].'">'.$data[$i]["lng"].'</span><span id="editUrl'.$data[$i]["id"].'">'.$data[$i]["url"].'</span></div>';
		}

		$file = str_replace("@@HELPCOOR@@", file_get_contents("templates/help_coor.html"), $file );
		$file = str_replace("@@LOCATIONS@@", $lct, $file);
		$file = str_replace("@@SCRIPT@@", $this->script, $file);
		echo $file;
	}
}

$l = new locationsp();

?>