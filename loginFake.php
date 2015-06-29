<?php
require("class/classes.class.php");
require("class/conexao.class.php");

$db = Database::getInstance();
$security = Security::getInstance();
$mysqli = $db->getConnection();

$id = $_GET['id'];
$usu = $mysqli->query("SELECT * FROM `tb_usuario` WHERE `usu_id` = '" .$id."'");
$usu = $usu->fetch_array();

$usuario = new Usuario($usu[0], utf8_encode($usu[1]), utf8_encode($usu[2]), $usu[3]);
$security->setLogged($usuario);
header("Location: index.php?A");