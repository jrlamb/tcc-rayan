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
    if (isset($_GET['arq_id']) && isset($_GET['tipo'])) {
        $arq_id = $mysqli->real_escape_string($_GET['arq_id']);
        $tipo = $mysqli->real_escape_string($_GET['tipo']);
        $obs = $mysqli->real_escape_string($_GET['obs']);
		
        if ($tipo == "arquivo") {
            $encontraDono = $mysqli->query("SELECT `usu_id` FROM `tb_usuario`, `tb_arquivo`, `tb_sincronizado` WHERE `arq_id` = `sinc_arq_id` AND `sinc_usu_id` = `usu_id` AND `usu_id` = `arq_dono` AND `arq_id` = '" . $arq_id . "'");
        } else {
            $encontraDono = $mysqli->query("SELECT `usu_id` FROM `tb_usuario`, `tb_evento` WHERE `usu_id` = `eve_dono` AND `eve_id` = '" . $arq_id . "'");
        }
        $id = $mysqli->query("SELECT `usu_id` FROM `tb_usuario` WHERE `usu_email` = '" . $usuario->email . "'");
        $id = $id->fetch_array();
        $id = $id[0];
        $donoID = $encontraDono->fetch_array();
        $donoID = $donoID[0];
        if ($mysqli->query("INSERT INTO `tb_mensagem` (`men_solicitante` , `men_solicitado`, `men_obj_id` , `men_tipo` , `men_obs` , `men_data` ) VALUES ('" . $id . "', '" . $donoID . "', '" . $arq_id . "', '" . $tipo . "', '" . $obs . "', CURRENT_TIMESTAMP);")) {
            echo "<div class='sucesso'>Report enviado com sucesso!</div>";
        } else {
            echo "<div class='erro'>Erro ao enviar report.</div>";
        }
    }

?>