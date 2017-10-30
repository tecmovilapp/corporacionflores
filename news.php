<?php
session_start();
header('Access-Control-Allow-Origin: *');
date_default_timezone_set("America/Tegucigalpa");
include("smart_resize_image.function.php");
require_once("config/conexion.php");

class news{
	private $info;
	private $script = "";
	function news(){
		if( isset($_SESSION["user"]) && isset($_SESSION["id"]) ){
			if(isset($_POST["titulon"]) && isset($_POST["contenido"]) && isset ( $_FILES ['imagen'] )){
				$this->createNews();
			} else if(isset($_POST["titulonedit"]) && isset($_POST["contenidoedit"]) && isset ( $_POST['editnewsid'] )){
				$this->updateNews();
			} else if(isset($_POST["deletennewsid"])){
				$this->deleteNews();
			}
		} else {
			header("location:index.html");
		}
		$this->printHTML();
	}

	function deleteNews(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT imagen FROM noticias WHERE id = :ID;");
		$stmt->execute( array(":ID"=>$_POST["deletennewsid"]));
		if($stmt->rowCount()==1){
			$row = $stmt->fetch();
			try {
				@unlink('images/news/'.$row["imagen"]);
			} catch (Exception $e) {}

			$stmt = $dbh->prepare("DELETE FROM noticias WHERE id = :ID;");
			$stmt->execute( array(":ID"=>$_POST["deletennewsid"]));
			$this->script = 'showMessage("Aviso","La noticia ha sido eliminada.")';
		}

	}

	function deleteOldImage($id){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT imagen FROM noticias WHERE id = :ID;");
		$stmt->execute( array(":ID"=>$id) );

		if($stmt->rowCount() == 1){
       		$row = $stmt->fetch();
       		$imagen = $row["imagen"];
       		try {
       			@unlink("images/news/".$row["imagen"]);
       		} catch (Exception $e) {}
		} 
	}

	function updateNews(){//PENDIENTE
		global $dbh;
		$dbh->exec("set names utf8");
		$dir_subida = 'images/news/';

		if(isset ( $_FILES ['imagenedit'] )){
			$file_name = md5(time()."-".rand(0,100)) .".". pathinfo($_FILES['imagenedit']['name'], PATHINFO_EXTENSION);
			$fichero_subido = $dir_subida . $file_name;
			if (move_uploaded_file($_FILES['imagenedit']['tmp_name'], $fichero_subido)) {
				$this->deleteOldImage($_POST["editnewsid"]);
				$stmt = $dbh->prepare("UPDATE noticias SET imagen = :IMAGE WHERE id =:ID;");
				$stmt->execute( array(":IMAGE"=>$file_name, ":ID"=>$_POST["editnewsid"]) );
				
			}
		}

		$stmt = $dbh->prepare("UPDATE noticias SET titulo=:T, descripcion=:C, enlace=:URL WHERE id =:ID;");
		$stmt->execute( array(":T"=>utf8_encode($_POST["titulonedit"]), ":C"=>utf8_encode($_POST["contenidoedit"]), ":URL"=>$_POST["enlaceedit"], ":ID"=>$_POST["editnewsid"]) );

		$this->script = 'showMessage("Aviso","La noticia ha sido actualizada.")';
	}

	function createNews(){
		global $dbh;
		$dbh->exec("set names utf8");
		$dir_subida = 'images/news/';
		$file_name = md5(time()."-".rand(0,100)) .".". pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
		$fichero_subido = $dir_subida . $file_name;
		if (move_uploaded_file($_FILES['imagen']['tmp_name'], $fichero_subido)) {
			$stmt = $dbh->prepare("INSERT INTO noticias VALUES(NULL,:TITLE,:CONTENT,:IMAGE,:URL,:D);");
			$stmt->execute( array(":TITLE"=>utf8_encode($_POST["titulon"]), ":CONTENT"=>utf8_encode($_POST["contenido"]), ":IMAGE"=>$file_name, ":URL"=>$_POST["enlace"], ":D"=>date("Y-m-d H:i:s")) );
			$this->script = 'showMessage("Aviso","La noticia ha sido agregada.")';
		}
	}

	function getNews(){
		global $dbh;
		$dbh->exec("set names utf8");
		$stmt = $dbh->prepare("SELECT * FROM noticias ORDER BY id DESC;");
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

	function printHTML(){
		$file = file_get_contents("templates/news.html");
		$file = str_replace("@@MENU@@", file_get_contents("templates/menu.html"), $file);
		$data = $this->getNews();
		$news = "";
		for($i=0;$i<count($data);$i++){
			$news .= '<li><div class="collapsible-header"><span>'.$data[$i]["fecha"].'</span> - '.$data[$i]["titulo"].'</div>';
			$news .= '<div class="collapsible-body"><a class="waves-effect waves-light btn gflores btnEditNews" style="float:right;margin:10px;" edit-id="'.$data[$i]["id"].'">Editar</a><a class="waves-effect waves-light btn gflores deletePromo" style="float:right;margin:10px;" item-id="'.$data[$i]["id"].'">Eliminar</a><form method="POST" id="deleteNewsForm'.$data[$i]["id"].'" action="news.php"><input type="hidden" value="'.$data[$i]["id"].'" name="deletennewsid"/></form><br><img src="images/news/'.$data[$i]["imagen"].'" style="width:50%;margin-left:20%;margin-top:10px;"/><p>Enlace: '.$data[$i]["enlace"].'<br><br>'.$data[$i]["descripcion"].'</p></div></li>';
			$news .= '<div style="display:none;"><span id="editTitulo'.$data[$i]["id"].'">'.$data[$i]["titulo"].'</span><span id="editContenido'.$data[$i]["id"].'">'.$data[$i]["descripcion"].'</span><span id="editEnlace'.$data[$i]["id"].'">'.$data[$i]["enlace"].'</span></div>';
		}

		$file = str_replace("@@NEWS@@", $news, $file);
		$file = str_replace("@@SCRIPT@@", $this->script, $file);
		echo $file;
	}
}

$v = new news();

?>