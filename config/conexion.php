<?php

// $user_conexion = "cfloresapp";
// $pass_conexion = "Grupofapp2016!";
// $data_base_conexion = "cfloresapp";
// $host_conexion = "cfloresapp.db.10397118.hostedresource.com";
// date_default_timezone_set('America/Tegucigalpa');


$user_conexion = "root";
$pass_conexion = "";
$data_base_conexion = "cfloresapp";
$host_conexion = "localhost:3306";
$conexion = mysql_connect($host_conexion, $user_conexion, $pass_conexion) or die("Error en la conexion en la base de datos [PHP CONNECTION]");


/*
$user_conexion = "tecmovilapp";
$pass_conexion = "Tecmovil2013!";
$data_base_conexion = "cflores";
$host_conexion = "localhost";
$conexion = mysql_connect($host_conexion, $user_conexion, $pass_conexion) or die("Error en la conexion en la base de datos [PHP CONNECTION]");
*/
$dbh = new PDO("mysql:host=".$host_conexion.";dbname=".$data_base_conexion, $user_conexion, $pass_conexion);
?>