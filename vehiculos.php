<?php
session_start();
header('Access-Control-Allow-Origin: *');
date_default_timezone_set("America/Tegucigalpa");
include("smart_resize_image.function.php");
require_once("config/conexion.php");

class vehiculos{
	private $info;
	private $script = "";
	function vehiculos(){
		if( isset($_SESSION["user"]) && isset($_SESSION["id"]) ){
			if(isset($_POST["actions"]) && $_POST["actions"] == "cat"){
				if( isset($_POST["nombre"]) ){
					$this->createCat();
				} else if(isset($_POST["nombreedit"]) && isset($_POST["editcatid"]) ){
					$this->editCat();
				}
			} else if(isset($_POST["actions"]) && $_POST["actions"] == "veh") {
				if( isset($_POST["nombrec"]) && isset($_FILES["imagenc"]) && isset($_POST["yearc"]) && isset($_POST["descripcionc"]) && isset($_POST["categoriac"]) && isset($_POST["pdfc"]) ) {
					$this->createCar();
				} else if( isset($_POST["nombrecedit"]) && isset($_POST["yearcedit"]) && isset($_POST["descripcioncedit"]) && isset($_POST["categoriacedit"]) && isset($_POST["pdfcedit"]) ){
					$this->editCar();
				}
			} 
		} else {
			header("location:index.html");
		}
		if( isset($_GET["l"]) && isset($_GET["valc"]) ){
			$this->script = 'showMessage("Aviso","El veh&iacute;culo a sido agregado.");';
			$this->script .= '$("ul.tabs").tabs("select_tab", "'.$_GET['l'].'");';
			$this->script .= '$("#catSelect").val('.$_GET["valc"].');';
			$this->script .= '$("#catSelect").material_select();';
			$this->script .= 'getCarsByCat();';
		} else if( isset($_GET["l"]) && isset($_GET["vale"]) ){
			$this->script = 'showMessage("Aviso","El veh&iacute;culo a sido editado.");';
			$this->script .= '$("ul.tabs").tabs("select_tab", "'.$_GET['l'].'");';
			$this->script .= '$("#catSelect").val('.$_GET["vale"].');';
			$this->script .= '$("#catSelect").material_select();';
			$this->script .= 'getCarsByCat();';
		} else if( isset($_GET["l"]) && isset($_GET["valr"]) ){
			$this->script .= '$("ul.tabs").tabs("select_tab", "'.$_GET['l'].'");';
			$this->script .= '$("#catSelect").val('.$_GET["valr"].');';
			$this->script .= '$("#catSelect").material_select();';
			$this->script .= 'getCarsByCat();';
		}
		$this->printHTML();
	}

	function resizeImg($dir, $file, $newfile, $width, $height){
		smart_resize_image($dir.$file , null, $width , $height , true , $dir.$newfile , true , false ,100 );
		return $newfile;
	}

	function editCar(){
		global $dbh;
		$dbh->exec("set names utf8");
		$dir_subida = 'images/cars/';

		if(isset ( $_FILES ['imagencedit'] )){
			$file_name = md5(time()."-".rand(0,50)) .".". pathinfo($_FILES['imagencedit']['name'], PATHINFO_EXTENSION);
			$fichero_subido = $dir_subida . $file_name;
			if (move_uploaded_file($_FILES['imagencedit']['tmp_name'], $fichero_subido)) {
				try {//ELIMINAMOS LA IMAGEN ANTERIOR
					$this->deleteOldImage($_POST["editcarid"]);
				} catch (Exception $e) {}
				$imageRS = $this->resizeImg($dir_subida, $file_name, "rs_".$file_name, 1000, 1000);	
				$stmt = $dbh->prepare("UPDATE vehiculos SET foto = :IMAGE WHERE id =:ID;");
				$stmt->execute( array(":IMAGE"=>$imageRS, ":ID"=>$_POST["editcarid"]) );
				
			}
		}
		$stmt = $dbh->prepare("UPDATE vehiculos SET nombre=:N, descripcion=:D, year=:Y, pdf=:P, id_categoria = :IDC, enlace=:URL WHERE id =:ID;");
		$stmt->execute( array( ":N"=>utf8_encode($_POST["nombrecedit"]), ":D"=>utf8_encode($_POST["descripcioncedit"]), ":Y"=>$_POST["yearcedit"], ":P"=>$_POST["pdfcedit"], ":URL"=>$_POST["enlacecedit"], ":IDC"=>$_POST["categoriacedit"], ":ID"=>$_POST["editcarid"]) );

		header("Location: vehiculos.php?l=vehiculos&vale=".$_POST["categoriacedit"]);

	}

	function deleteOldImage($id){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT foto FROM vehiculos WHERE id = :ID;");
		$stmt->execute( array(":ID"=>$id) );
        $dir = 'images/cars/';
		if($stmt->rowCount() == 1){
       		$row = $stmt->fetch();
       		try {
       			unlink($dir.$row["foto"]);
       		} catch (Exception $e) {}
		}
	}

	function createCar(){
		global $dbh;
		$dbh->exec("set names utf8");
		$dir_subida = 'images/cars/';
		$file_name = md5(time()."-".rand(0,100)) .".". pathinfo($_FILES['imagenc']['name'], PATHINFO_EXTENSION);
		$fichero_subido = $dir_subida . $file_name;

		if (move_uploaded_file($_FILES['imagenc']['tmp_name'], $fichero_subido)) {
			$imageRS = $this->resizeImg($dir_subida, $file_name, "rs_".$file_name, 1000, 1000);	
			$stmt = $dbh->prepare("INSERT INTO vehiculos VALUES(NULL, :NAME, :IMG, :DES, :YEAR, :PDF, :URL, :CAT);");
			$stmt->execute( array(":NAME"=>utf8_encode($_POST["nombrec"]), ":IMG"=>$imageRS, ":DES"=>utf8_encode($_POST["descripcionc"]),":YEAR"=>$_POST["yearc"],":PDF"=>$_POST["pdfc"],":URL"=>$_POST["enlacec"], ":CAT"=>$_POST["categoriac"]) );
			header("Location: vehiculos.php?l=vehiculos&valc=".$_POST["categoriac"]);
		} else {
			header("Location: vehiculos.php?l=vehiculos&valv=-1");
		}

	}

	function editCat(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("UPDATE categoria_vehiculos SET nombre=:NAME WHERE id = :ID");
		$stmt->execute( array(":NAME"=>str_replace('"','',str_replace("'","",utf8_encode($_POST["nombreedit"]))), ":ID"=>$_POST["editcatid"]) );
		$this->script = 'showMessage("Aviso","La catego&iacute;a a sido actualizada.")';
		header("Location:vehiculos.php");
	}

	function createCat(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("INSERT INTO categoria_vehiculos VALUES(NULL, :NAME)");
		$stmt->execute( array(":NAME"=>str_replace('"','',str_replace("'","",utf8_encode($_POST["nombre"])))) );
		$this->script = 'showMessage("Aviso","La Catego&iacute;a a sido agregada.")';
		header("Location:vehiculos.php");
	}

	function getCat(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM categoria_vehiculos ORDER BY id DESC;");
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
		return $this->info;
	}
	function printHTML(){
		$file = file_get_contents("templates/vehiculos.html");
		$file = str_replace("@@MENU@@", file_get_contents("templates/menu.html"), $file);
		$cats = "";
		$optcats= "";
		$firstidcat = 0;
		$data = $this->getCat();
		for($i=0;$i<count($data);$i++){
			if($i==0){
				$firstidcat=$data[$i]["id"];
			}
			$optcats .= '<option value="'.$data[$i]["id"].'">'.$data[$i]["nombre"].'</option>';
			$cats .= '<li class="collection-item"><div style="min-height:40px;">'.$data[$i]["nombre"].'<div class="secondary-content tooltipped editCategory" item-id="'.$data[$i]["id"].'" data-tooltip="Editar" data-position="left" style="cursor:pointer;"><i class="material-icons gflores-text" style="font-size:40px;">assignment</i></div></div><input type="hidden" id="nombre'.$data[$i]["id"].'" value="'.$data[$i]["nombre"].'"></li>';
		}
		$file = str_replace("@@CATEGORIAS@@", $cats, $file);
		$file = str_replace("@@CATCAR@@", $optcats, $file);

		$file = str_replace("@@YEARS@@",$this->getOptionYears(), $file);
		$file = str_replace("@@SCRIPT@@", $this->script, $file);
		echo $file;
	}

	function getOptionYears(){
		$years = "";
		$i = date("Y");
		$t = $i-30;
		for($i;$i>$t;$i--){
			$years .= '<option value="'.$i.'">'.$i.'</option>';
		}
		return $years;
	}
}

$v = new vehiculos();

?>