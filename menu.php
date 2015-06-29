<?php

require('class/classes.class.php');

$security = Security::getInstance();
$usuario = $security->getUsuario();

$varLink = '<a href="#" onClick="javascript: window.open(\'acessar.php?l\', \'acessar\',\'width=500,height=400\');">Acessar o sistema</a><script type=\"text/javascript\">$(\"#conteudoInicial\").load(\"conteudoInicial.php\");</script>';
if (isset($_GET['A'])) {
    if ($security->isLogged()) {
        echo "<div align='left' class='divAcesso'><div class='floatMaior' align='left'>Seja bem vindo, " . utf8_decode($usuario->primeiroNome) . " - <i>" . $usuario->email . "</i></div><div class='floatMenor' align='right' id='btnSair'><a href='javascript: mostrarResultado(\"S\");'><strong>Sair</strong></a></div></div><script type='text/javascript'>$('#conteudoInicial').load('painel.php?A');</script>";
    } else {
        echo $varLink;
    }
}else {
    $security->setUnlogged();
    echo $varLink;
}
?>