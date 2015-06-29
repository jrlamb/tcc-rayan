<?php

require('class/classes.class.php');
require('class/conexao.class.php');

$security = Security::getInstance();
$db = Database::getInstance();
$mysqli = $db->getConnection();

if (!$security->isLogged()) {
	header('Location: index.php');
}
$usuario = $security->getUsuario();

$anosArray = array();
$tipoArray = array();
$autoresArray = array();

$anosQuery = $mysqli->query("SELECT COUNT(*), EXTRACT(YEAR FROM `eve_dtinicial`) FROM `tb_arquivo`, `tb_sincronizado`, `tb_evento`, `tb_usuario` WHERE `arq_id` = `sinc_arq_id` AND `sinc_usu_id` = `usu_id` AND `arq_eve_id` = `eve_id` AND `usu_email` = '" . $usuario->email . "' GROUP BY EXTRACT(YEAR FROM `eve_dtinicial`) ORDER BY EXTRACT(YEAR FROM `eve_dtinicial`) ASC");
$tipoQuery = $mysqli->query("SELECT COUNT(*), `tparq_descricao` FROM `tb_arquivo`, `tb_sincronizado`, `tb_evento`, `tb_usuario`, `tb_tipoarquivo` WHERE `arq_id` = `sinc_arq_id` AND `sinc_usu_id` = `usu_id` AND `arq_eve_id` = `eve_id` AND `tparq_id` = `arq_tparq_id` AND `usu_email` = '" . $usuario->email . "' GROUP BY `tparq_descricao` ORDER BY `tparq_descricao` ASC");
$autoresQuery = $mysqli->query("SELECT COUNT(*), `usu_primeironome`, `usu_email` FROM `tb_arquivo`, `tb_sincronizado`, `tb_evento`, `tb_usuario`, `tb_arquivo_autores` WHERE `arq_id` = `sinc_arq_id` AND `sinc_usu_id` = `usu_id` AND `arq_eve_id` = `eve_id` AND `arqaut_usu_id` = `usu_id` AND `arqaut_arq_id` = `arq_id` AND `usu_email` <> '" . $usuario->email . "' AND `sinc_arq_id` IN (SELECT `sinc_arq_id` FROM `tb_usuario`, `tb_sincronizado` WHERE `sinc_usu_id` = `usu_id` AND `usu_email` = '" . $usuario->email . "') GROUP BY `usu_id` ORDER BY `usu_primeironome` ASC");

$anoText = $anoQt = $tipoText = $tipoQt = $autoresText = $autoresQt = "";

$c = 0;
while ($anos = $anosQuery->fetch_array()) {
    $anosArray[$c] = $anos[0] . '|' . $anos[1];
    $c++;
}
for ($i = 0; $i < count($anosArray); $i++) {
    $q = explode('|', $anosArray[$i]);
    if ($i == 0) {
        $anoText .= '"' . $q[1] . '"';
        $anoQt .= $q[0];
    } else {
        $anoText .= ', "' . $q[1] . '"';
        $anoQt .= ',' . $q[0];
    }
}
$anoData = 'var Ano = {
	labels : ["0", ' . utf8_encode($anoText) . '],
	datasets : [
		{
			fillColor : "rgba(4,120,183,0.5)",
			strokeColor : "rgba(220,220,220,1)",
			data : [0, ' . $anoQt . ']
		}
	]
	
	};';

$c = 0;
while ($tipos = $tipoQuery->fetch_array()) {
    $tipoArray[$c] = $tipos[0] . '|' . $tipos[1];
    $c++;
}
for ($i = 0; $i < count($tipoArray); $i++) {
    $q = explode('|', $tipoArray[$i]);
    if ($i == 0) {
        $tipoText .= '"' . $q[1] . '"';
        $tipoQt .= $q[0];
    } else {
        $tipoText .= ', "' . $q[1] . '"';
        $tipoQt .= ',' . $q[0];
    }
}
$tipoData = 'var Tipo = {
	labels : ["0", ' . utf8_encode($tipoText) . '],
	datasets : [
		{
			fillColor : "rgba(4,120,183,0.5)",
			strokeColor : "rgba(220,220,220,1)",
			data : [0, ' . $tipoQt . ']
		}
	]
	
	};';
$c = 0;
while ($autores = $autoresQuery->fetch_array()) {
    $autoresArray[$c] = $autores[0] . '|' . $autores[1];
    $c++;
}
for ($i = 0; $i < count($autoresArray); $i++) {
    $q = explode('|', $autoresArray[$i]);
    if ($i == 0) {
        $autoresText .= '"' . $q[1] . '"';
        $autoresQt .= $q[0];
    } else {
        $autoresText .= ', "' . $q[1] . '"';
        $autoresQt .= ',' . $q[0];
    }
}
$autoresData = 'var Autores = {
	labels : ["0", ' . utf8_encode($autoresText) . '],
	datasets : [
		{
			fillColor : "rgba(4,120,183,0.5)",
			strokeColor : "rgba(220,220,220,1)",
			data : [0, ' . $autoresQt . ']
		}
	]
	
	};';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Gráficos</title>
        <link rel="stylesheet" type="text/css" href="libs/style.css" />
    <script src="libs/jquery.js" type="text/javascript"></script>
        <script src="libs/Chart.js"></script>
        <script type="text/javascript">
		<?php
print $anoData;
print $tipoData;
print $autoresData;
?>
				function alterarGrafico(o) {
					var retornoAcao;
					for (i = 0; i < document.getElementsByTagName('span').length; i++) {
						document.getElementsByTagName('span')[i].style.backgroundColor = '#06C';
					}
					document.getElementById(o).style.backgroundColor = '#0033CC';
					switch (o) {
						case 'btAno':
							//if(rnd())
							try{
								retornoAcao = new Chart(document.getElementById("canvas").getContext("2d")).Line(Ano);
							}catch(e){}
							//else
							//	retornoAcao = new Chart(document.getElementById("canvas").getContext("2d")).Bar(Ano);
							break;
						case 'btTipo':
							//if(rnd())
							//	retornoAcao = new Chart(document.getElementById("canvas").getContext("2d")).Line(Tipo);
							//else
							try{
								retornoAcao = new Chart(document.getElementById("canvas").getContext("2d")).Bar(Tipo);
							}catch(e){}
							break;
						case 'btAutores':
							//if(rnd())
							//	retornoAcao = new Chart(document.getElementById("canvas").getContext("2d")).Line(Autores);
							//else
							try{
								retornoAcao = new Chart(document.getElementById("canvas").getContext("2d")).Bar(Autores);
							}catch(e){}
							break;
					}
				}
			
			$(document).ready(function(e) {				
                alterarGrafico('btAno');
            });
            
        </script>
    </head>

    <body><br />
        <font color="#FFFFFF">
            <font size="+3"><strong>G</strong>ráficos</font><br /><br />
            <div align="center" style="width:80%">
                <span id="btAno" class="btnFlutuante" onclick="javascript: alterarGrafico('btAno');">Por ano</span>
                <span id="btTipo" class="btnFlutuante" onclick="javascript: alterarGrafico('btTipo');">Por tipo</span>
                <span id="btAutores" class="btnFlutuante" onclick="javascript: alterarGrafico('btAutores');">Por autores</span><br />
                <div id="divResultadoInterno" style="height:100%; border:#FFF solid 2px; width:100%; float:left">
                    <canvas id="canvas" height="305" width="480" style="background-color:#FFF; margin-top:5px"></canvas></div>
            </div>
    </body>
</html>