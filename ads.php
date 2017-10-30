<?php
session_start();
header('Access-Control-Allow-Origin: *');
date_default_timezone_set("America/Tegucigalpa");
include("smart_resize_image.function.php");
require_once("config/conexion.php");

class ads{
	private $info;
	private $script = "";

	function ads(){
		if( isset($_SESSION["user"]) && isset($_SESSION["id"]) ){
			if( isset($_POST["description"]) && isset($_POST["url"]) && isset ( $_FILES ['imagenl'] ) && isset ( $_FILES ['imagens']) ){
				$this->createAds();
			} else if(isset($_POST["deleteadsid"])){
				$this->deleteAds();
			} else if( isset($_POST["descriptionedit"]) && isset($_POST["urledit"]) && isset ( $_POST['editadsid'] )){
				$this->updateAds();
			}
		} else {
			header("location:index.html");
		} 
		$this->printHTML();
	}

	function updateAds(){//PENDIENTE
		global $dbh;
		$dbh->exec("set names utf8");
		$dir_subida = 'images/ads/';

		if(isset ( $_FILES ['imagensedit'] )){
			$file_name = md5(time()."-".rand(0,50)) .".". pathinfo($_FILES['imagensedit']['name'], PATHINFO_EXTENSION);
			$fichero_subido = $dir_subida . $file_name;
			if (move_uploaded_file($_FILES['imagensedit']['tmp_name'], $fichero_subido)) {
				try {//ELIMINAMOS LA IMAGEN ANTERIOR
					$this->deleteOldImage($_POST["editadsid"], "imagenSmall");
				} catch (Exception $e) {}
				$newfile = $this->resizeImg($dir_subida,$file_name,"s_".$file_name,320,80);
				$stmt = $dbh->prepare("UPDATE publicidad SET imagenSmall = :IMAGE WHERE id =:ID;");
				$stmt->execute( array(":IMAGE"=>$newfile, ":ID"=>$_POST["editadsid"]) );
				
			}
		}

		if(isset ( $_FILES ['imagenledit'] )){
			$file_name = md5(time()."-".rand(50,100)) .".". pathinfo($_FILES['imagenledit']['name'], PATHINFO_EXTENSION);
			$fichero_subido = $dir_subida . $file_name;
			if (move_uploaded_file($_FILES['imagenledit']['tmp_name'], $fichero_subido)) {
				try {//ELIMINAMOS LA IMAGEN ANTERIOR
					$this->deleteOldImage($_POST["editadsid"], "imagenLarge");
				} catch (Exception $e) {}
				$newfile = $this->resizeImg($dir_subida,$file_name,"l_".$file_name,600,80);
				$stmt = $dbh->prepare("UPDATE publicidad SET imagenLarge = :IMAGE WHERE id =:ID;");
				$stmt->execute( array(":IMAGE"=>$newfile, ":ID"=>$_POST["editadsid"]) );
				
			}
		}

		$stmt = $dbh->prepare("UPDATE publicidad SET descripcion=:C, enlace=:E WHERE id =:ID;");
		$stmt->execute( array( ":C"=>utf8_encode($_POST["descriptionedit"]), ":E"=>$_POST["urledit"], ":ID"=>$_POST["editadsid"]) );

		$this->script = 'showMessage("Aviso","La publicidad ha sido actualizada.")';
	}

	function deleteAds(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT imagenSmall, imagenLarge  FROM publicidad WHERE id = :ID;");
		$stmt->execute( array(":ID"=>$_POST["deleteadsid"]));
		if($stmt->rowCount()==1){
			$row = $stmt->fetch();
			try {
				unlink('images/ads/'.$row["imagenSmall"]);
				unlink('images/ads/'.$row["imagenLarge"]);
			} catch (Exception $e) {}

			$stmt = $dbh->prepare("DELETE FROM publicidad WHERE id = :ID;");
			$stmt->execute( array(":ID"=>$_POST["deleteadsid"]));
			$this->script = 'showMessage("Aviso","La publicidad ha sido eliminada.")';
	
		}
	}

	function deleteOldImage($id, $field){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT ".$field." FROM publicidad WHERE id = :ID;");
		$stmt->execute( array(":ID"=>$id) );
        $dir = 'images/ads/';
		if($stmt->rowCount() == 1){
       		$row = $stmt->fetch();
       		try {
       			unlink($dir.$row[$field]);
       		} catch (Exception $e) {}
		}
	}

	function createAds(){
		global $dbh;
		$dbh->exec("set names utf8");
		$dir_subida = 'images/ads/';
		$file_name = md5(time()."-".rand(0,50)) .".". pathinfo($_FILES['imagens']['name'], PATHINFO_EXTENSION);
		$fichero_subido = $dir_subida . $file_name;
		$file_namel = md5(time()."-".rand(50,100)) .".". pathinfo($_FILES['imagenl']['name'], PATHINFO_EXTENSION);
		$fichero_subidol = $dir_subida . $file_namel;

		if (move_uploaded_file($_FILES['imagens']['tmp_name'], $fichero_subido)) {
			$imageS = $this->resizeImg($dir_subida, $file_name, "s_".$file_name, 320, 80);//320x80
			if (move_uploaded_file($_FILES['imagenl']['tmp_name'], $fichero_subidol)) {
				$imageL = $this->resizeImg($dir_subida, $file_namel, "l_".$file_namel, 600, 80);//600x80
				$stmt = $dbh->prepare("INSERT INTO publicidad VALUES(NULL, :IMGS,:IMGL,:URL, :DES, :D);");
				$stmt->execute( array(":IMGS"=>$imageS, ":IMGL"=>$imageL, ":URL"=>$_POST["url"],":DES"=>utf8_encode($_POST["description"]), ":D"=>date("Y-m-d H-i:s")) );
				$this->script = 'showMessage("Aviso","La publicidad ha sido agregada.")';
			}
		}
	}

	function resizeImg($dir, $file, $newfile, $width, $height){
		smart_resize_image($dir.$file , null, $width , $height , false , $dir.$newfile , true , false ,100 );
		return $newfile;
	}


	function getADS(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM publicidad ORDER BY id DESC;");
		$stmt->execute( array() );
		$cont = 0;

		if($stmt->rowCount() > 0){
       		foreach($stmt as $row){
       			$row["descripcion"] = stripslashes(utf8_decode($row["descripcion"]));
       			$this->info[$cont] = $row;
				$cont++;
			}
		} else{
			$this->info = array();
		}
		return $this->info;
	}

	function printHTML(){
		$file = file_get_contents("templates/ads.html");
		$file = str_replace("@@MENU@@", file_get_contents("templates/menu.html"), $file);
		$data = $this->getADS();
		$ads = "";
		for($i=0;$i<count($data);$i++){
			$ads .= '<li><div class="collapsible-header"><span>'.$data[$i]["fecha"].'</span> - '.$data[$i]["descripcion"].'</div>';
			$ads .= '<div class="collapsible-body"><a class="waves-effect waves-light btn gflores btnEditAds" style="float:right;margin:10px;" edit-id="'.$data[$i]["id"].'">Editar</a><a class="waves-effect waves-light btn gflores deleteAds" style="float:right;margin:10px;" item-id="'.$data[$i]["id"].'">Eliminar</a><form method="POST" id="deleteAdsForm'.$data[$i]["id"].'"><input type="hidden" value="'.$data[$i]["id"].'" name="deleteadsid"/></form><img src="images/ads/'.$data[$i]["imagenSmall"].'" style="width:auto;margin-top:10px;margin-left:10px;"/><br><img src="images/ads/'.$data[$i]["imagenLarge"].'" style="margin-left:10px;margin-top:10px;"/><p>Enlace: '.$data[$i]["enlace"].'</p></div></li>';
			$ads .= '<div style="display:none;"><span id="editDescription'.$data[$i]["id"].'">'.$data[$i]["descripcion"].'</span><span id="editUrl'.$data[$i]["id"].'">'.$data[$i]["enlace"].'</span></div>';
		}
		$file = str_replace("@@ADS@@", $ads, $file);
		$file = str_replace("@@SCRIPT@@", $this->script, $file);
		echo $file;
	}

}

$adsClass = new ads();
?>