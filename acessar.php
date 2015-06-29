<?php

# Logging in with Google accounts requires setting special identity, so this example shows how to do it.
require("openid.php");
require("class/classes.class.php");
require("class/conexao.class.php");

$db = Database::getInstance();
$security = Security::getInstance();
$mysqli = $db->getConnection();

try {
    # Change 'localhost' to your domain name.
    $openid = new LightOpenID('localhost');
    if (!$openid->mode) {
        if (isset($_GET['l'])) {
            $openid->identity = 'https://www.google.com/accounts/o8/id';
            $openid->required = array('namePerson/first', 'namePerson/last', 'contact/email');
            header('Location: ' . $openid->authUrl());
        }
    } elseif ($openid->mode == 'cancel') {
        echo "<script type='text/javascript'>window.opener.location = 'index.php?C'; window.close();</script>";
    } else {
        echo "Feche esta janela para concluir a autentica&ccedil;&atilde;o.";
        $data = $openid->getAttributes();
        $usuario = new Usuario($openid->identity, utf8_encode($data['namePerson/first']), utf8_encode($data['namePerson/last']), $data['contact/email']);
        $security->setLogged($usuario);
        if ($queryPesquisa = $mysqli->query("SELECT COUNT(*) FROM `tb_usuario` WHERE `usu_email` = '" . $usuario->email . "'")) {
            if ($queryPesquisa->num_rows == 0) {
                $mysqli->query("INSERT INTO `tb_usuario` (`usu_id` ,`usu_primeironome` ,`usu_ultimonome` ,`usu_email` ,`usu_ultimologin` ,`usu_cod_sinc`) VALUES (NULL , '" . $usuario->primeiroNome . "', '" . $usuario->ultimoNome . "', '" . $usuario->email . "',CURRENT_TIMESTAMP , NULL);");
            } else {
                $k = $queryPesquisa->fetch_array();
                if ($usuario->primeiroNome == '' || $usuario->ultimoNome == '') {
                    $mysqli->query("UPDATE `tb_usuario` SET `usu_primeironome` = '" . $usuario->primeiroNome. "', `usu_ultimonome` = '" . $usuario->ultimoNome . "', `usu_ultimologin` = CURRENT_TIMESTAMP WHERE `usu_email` = '" . $usuario->email . "';");
                } else {
                    $mysqli->query("UPDATE `tb_usuario` SET `usu_ultimologin` = CURRENT_TIMESTAMP WHERE `usu_email` = '" . $usuario->email . "';");
                }
            }
        }
        echo "<script type='text/javascript'>window.opener.location = 'index.php?A'; window.close();</script>";
    }
} catch (ErrorException $e) {
    echo "Ocorreu um erro. Favor enviar para o webmaster a seguinte mensagem -> " . $e->getMessage();
} catch (TimeoutException $t) {
    echo "Timeout -> " . $t->getMessage();
}
?>