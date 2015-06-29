<?php

require('class/classes.class.php');
require('class/conexao.class.php');

$security = Security::getInstance();
$db = Database::getInstance();
$mysqli = $db->getConnection();
$return_arr = array();
if (!$security->isLogged()) {
    header('Location: index.php');
}
    $usuario = $security->getUsuario();

    if (isset($_GET['term']) && isset($_GET['action'])) {
        $varTerm = $_GET['term'];
        $varAction  = $_GET['action'];
        switch ($varAction) {
            case "autores":
                $result = $mysqli->query("SELECT DISTINCT `usu_primeironome`, `usu_ultimonome`, `usu_email` FROM `tb_usuario` WHERE `usu_email` <> '" . $usuario->email . "' AND (`usu_primeironome` LIKE '%" . $varTerm . "%' OR `usu_ultimonome` LIKE '%" . $varTerm . "%');");

                while ($l = $result->fetch_array()) {
                    $return_arr[] = utf8_encode($l[0]) . ' ' . utf8_encode($l[1]) . ' - ' . $l[2];
                }
                //print print_r($return_arr);
                break;
            case "cidades":
                $cod_estados = $mysqli->real_escape_string($varTerm);
                $cidades = array();

                $result = $mysqli->query("SELECT `cid_id`, `cid_nome` FROM `tb_cidade` WHERE `cid_est_id` = '" . $cod_estados . "'");
				
                while ($l = $result->fetch_array()) {
                    $cidades[] = array(
                        'cid_id' => utf8_encode($l['cid_id']),
                        'cid_nome' => utf8_encode($l['cid_nome']),
                    );
                }
                $return_arr = $cidades;
                break;
            case "evento":
                $result = $mysqli->query("SELECT `eve_nome` FROM `tb_evento` WHERE `eve_nome` LIKE '%" . $varTerm . "%'");

                while ($l = $result->fetch_array()) {
                    $return_arr[] = utf8_encode($l[0]);
                }
                break;
            case "arquivo":				
                $result = $mysqli->query("SELECT `arq_id`, `arq_nome`, `arq_titulo` FROM `tb_arquivo` WHERE `arq_id` NOT IN 
(SELECT DISTINCT `arq_id` FROM `tb_sincronizado`, `tb_arquivo`, `tb_usuario` WHERE `usu_email` = '" . $usuario->email . "' AND `usu_id` = `sinc_usu_id` AND `sinc_arq_id` = `arq_id`) AND `arq_titulo` LIKE '%" . $varTerm . "%'");
				
                while ($l = $result->fetch_array()) {
                    $return_arr[] = utf8_encode($l[2]);
                }
                break;
                case "compartilhar":
                    if (isset($_GET['term'])) {
                        $return_arr = array();
                        $cont_query = "";
                        $t = $_GET['term'];
                        if (isset($_GET['arq_id'])) {
                            $cont_query = " AND `usu_id` NOT IN (SELECT `usu_id` FROM `tb_usuario`, `tb_sincronizado` WHERE `usu_id` = `sinc_usu_id` AND `sinc_arq_id` = " . $_GET['arq_id'] . ")";
                        }
                        $rsd = $mysqli->query("SELECT DISTINCT `usu_primeironome`, `usu_ultimonome`, `usu_email` FROM `tb_usuario` WHERE `usu_email` <> '" . $usuario->email . "' AND (`usu_primeironome` LIKE '%" . $t . "%' OR `usu_ultimonome` LIKE '%" . $t . "%') " . $cont_query . ";");

                        while ($row = $rsd->fetch_array()) {
                            $return_arr[] = utf8_encode($row[0]) . ' ' . utf8_encode($row[1]) . ' - ' . $row[2];
                        }
                    }
                break;
            default:
            break;
        }
    }
    echo json_encode($return_arr);

    
?>