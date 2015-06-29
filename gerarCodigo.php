<?php
require('class/classes.class.php');
require("class/conexao.class.php");

$db = Database::getInstance();
$security = Security::getInstance();
$mysqli = $db->getConnection();
$usuario = $security->getUsuario();

if (!isset($_GET['A']) || !$security->isLogged()) {
    header('Location: index.php');
}
$valorAleatorio = rand(100000, 999999);
if (isset($_GET['Atualizar'])) {
    print "<div align='center'><font color='#FFFFFF'>Código <em>token</em> da conta:<br /><input type='text' value='" . $valorAleatorio . "' style='width: 110px; height: 40px; font-size: 30px' readonly /></font></div>";
    while ($res = ($mysqli->query("SELECT * FROM `tb_usuario` WHERE `usu_cod_sinc` = '" . $valorAleatorio . "'")->fetch_array()) && $res->num_rows != 0) {
        $valorAleatorio = rand(100000, 999999);
    }
    $mysqli->query("UPDATE `tb_usuario` SET `usu_cod_sinc` = " . $valorAleatorio . " WHERE `usu_email` = '" . $usuario->email . "';");
} else {
    $resultado = $mysqli->query("SELECT `usu_cod_sinc` FROM `tb_usuario` WHERE `usu_email` = '" . $usuario->email . "'");
    $a = $resultado->fetch_array();
    if (is_null($a[0])) {
        while ($res = $mysqli->query("SELECT * FROM `tb_usuario` WHERE `usu_cod_sinc` = '" . $valorAleatorio . "'")->fetch_array() && $res->num_rows != 0) {
            $valorAleatorio = rand(100000, 999999);
        }
        $mysqli->query("UPDATE `tb_usuario` SET `usu_cod_sinc` = " . $valorAleatorio . " WHERE `usu_email` = '" . $usuario->email . "';");
        print "<div align='center'><font color='#FFFFFF'>Código <em>token</em> da conta:<br /><input type='text' value='" . $valorAleatorio . "' style='width: 110px; height: 40px; font-size: 30px' readonly /></font></div>";
    } else {
        print "<div align='center'><font color='#FFFFFF'><font color='#FFFFFF'>Código <em>token</em> da conta:<br /><input type='text' value='" . $a[0] . "' style='width: 110px; height: 40px; font-size: 30px' readonly /></font></div>";
    }
}
?>