<?php

require('class/classes.class.php');
require('class/conexao.class.php');

$security = Security::getInstance();
$db = Database::getInstance();
$mysqli = $db->getConnection();
$return_arr = array();

if (!$security->isLogged()) {
	header('Location: index.php');
}else{
    $varAction = $_GET['action'];
    $usuario = $security->getUsuario();

    if (isset($varAction)) {
		
        switch ($varAction) {
                
            case "atualizar":
                $queryMensagem = $mysqli->query("SELECT COUNT(*) FROM `tb_mensagem`, `tb_usuario` WHERE `usu_email` = '" . $usuario->email. "' AND `usu_id` = `men_solicitado` AND `men_removida` = '0' AND `men_lida` = '0'");
                $querySolicitacao = $mysqli->query("SELECT COUNT(*) FROM `tb_solicitacao`, `tb_usuario` WHERE `usu_email` = '" . $usuario->email . "' AND `usu_id` = `sol_usu_id_solicitado` AND `sol_atendido` = '0' AND `sol_permitido` <> NULL");
                $varCountqueryMensagem = $queryMensagem->fetch_array();
                $varCountquerySolicitacao = $querySolicitacao->fetch_array();

                print "<div class='float'>
                            <a href='javascript: abrirInterno(\"solicitacaoManagement\", \"solicitacoes\");'><img src='imagens/icon-solicitacao.png' width='25' />" . $varCountquerySolicitacao[0] . "</a>
                        </div>
                        <div class='float'>
			    <a href='javascript: abrirInterno(\"mensagemManagement\", \"op\");'><img src='imagens/icon-mensagem.png' width='25' />" . $varCountqueryMensagem[0] . "</a>
		       </div>";
                break;
            case "deletar":
                $varValue = $_GET['value'];
                if ($mysqli->query("UPDATE `tb_mensagem` SET `men_removida` = '1' WHERE `men_id` = '" . $varValue . "';")) {
                    echo "<div class='sucesso'>Mensagem removida com sucesso!</div>";
                } else {
                    echo "<div class='erro'>Erro ao remover mensagem.</div>";
                }
                break;
            case "naolida":
                $varValue = $_GET['value'];
                if ($mysqli->query("UPDATE `tb_mensagem` SET `men_lida` = '0' WHERE `men_id` = '" . $varValue . "';")) {
                    echo "<div class='sucesso'>Mensagem definida como <em>não lida</em>!</div>";
                } else {
                    echo "<div class='erro'>Erro ao definir mensagem.</div>";
                }
                break;
            case "visualizar":
                $id = $_GET['men_id'];

                $nomeObj = $men_id = $men_solicitante = $men_tipo = $men_data = $men_lida = $men_removida = $men_obs = '';
                $query = $mysqli->query("SELECT `men_id`, `men_obj_id`, `men_solicitante`, `men_tipo`, DATE_FORMAT(`men_data`, '%d/%m/%Y'), `men_lida`, `men_removida`, `men_obs` FROM `tb_usuario`, `tb_mensagem` WHERE `men_solicitado` = `usu_id` AND `usu_email` = '" . $usuario->email . "' AND `men_id` = '" . $id . "'");
                $rows = $query->num_rows;
                if ($rows > 0) {
                    $arr = $query->fetch_array();
                    $men_id = $arr[0];
                    $men_obj_id = $arr[1];
                    $men_solicitante = $arr[2];
                    $men_tipo = $arr[3];
                    $men_data = $arr[4];;
                    $men_lida = $arr[5];
                    $men_removida = $arr[6];
                    $men_obs = $arr[7];
                    if ($men_tipo == "arquivo") {
                        $men_tipo = "Arquivo";
                        $nomeObjQuery = $mysqli->query("SELECT `arq_nome` FROM `tb_arquivo` WHERE `arq_id` = '" . $men_obj_id . "';");
                        $nomeObjArr = $nomeObjQuery->fetch_array();
                        $nomeObj = $nomeObjArr[0];
                    } else {
                        $men_tipo = "Evento";
                        $nomeObjQuery = $mysqli->query("SELECT `eve_nome` FROM `tb_evento` WHERE `eve_id` = '" . $men_obj_id . "'");
                        $nomeObjArr = $nomeObjQuery->fetch_array();
                        $nomeObj = $nomeObjArr[0];
                    }
                    $men_solicitanteQuery = $mysqli->query("SELECT CONCAT(`usu_primeironome`, ' ' ,`usu_ultimonome`) FROM `tb_usuario` WHERE `usu_id` = '" . $men_solicitante . "'");
                    $men_solicitanteArr = $men_solicitanteQuery->fetch_array();
                    $men_solicitante = $men_solicitanteArr[0];
                    $mysqli->query("UPDATE `tb_mensagem` SET `men_lida` = '1' WHERE `men_id` = '" . $id . "'");
                }
                print '<!DOCTYPE html>
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                    <title>Visualizar mensagem</title>
                    <link rel="stylesheet" type="text/css" href="libs/style.css">
                    <link rel="stylesheet" href="libs/jquery-ui.min.css" />
                    <script src="libs/jquery.js"></script>
                    <script type="text/javascript">

                        function voltar() {
                            abrirInterno("mensagemManagement.php?action=op");
                        }
                        function naoLida() {
                            $(\'#divResultadoInterno\').load(\'mensagemManagement.php?action=naolida&value=\' + $("#id").val(), function() {
                                $(\'#divResultadoInterno\').slideToggle(400, function() {
                                    setTimeout(function() {
                                        $(\'#divResultadoInterno\').slideToggle(400);
                                    }, 2000);
                                });
                            });

                        }
                    </script>
                </head>
                <body text="#FFFFFF">
                    <input type="hidden" id="id" name="id" value="' . $id . '" />
                    <div align="center"><br />
                        <font size="+3" color="#FFFFFF"><strong>V</strong>isualizar <strong>m</strong>ensagem</font><br />
                        <br />
                        <div id="divResultadoInterno" style="display:none"></div>
                        <div align="center"> <font color="#FFFFFF">
                            <div align="justify" style="width:80%">
                                <div>
                                    <div class="float" align="left">
                                        <font size="+1"><strong>Mens. relacionada a:</strong></font><br />
                                        <input type="text" style="width:80%; font-size:16px; height:20px" value="' . $men_tipo . '" />
                                    </div>
                                    <div class="float" align="left">
                                        <font size="+1"><strong>Data:</strong></font><br />
                                        <input type="text" style="width:50%; font-size:16px; height:20px" value="' . $men_data . '" /><br /><br />
                                    </div><br />
                                </div>
                                <font size="+1"><strong>Arquivo:</strong></font><br />
                                <input type="text" style="width:75%;font-size:16px; height:20px" value="' . utf8_encode($nomeObj) . '" /><br /><br />
                                <font size="+1"><strong>Solicitante:</strong></font><br />
                                <input type="text" style="width:65%;font-size:16px; height:20px" value="' . utf8_encode($men_solicitante) . '" /><br />
                                <font size="+1"><strong>Obs:</strong></font><br />
                                <textarea style="max-height:60px; max-width:80%; width:75%">' . utf8_encode($men_obs) . '</textarea>
                                <div align="center" style="clear:both"><br /><br />
                                    <input class="botao2" type="button" value="Voltar" onClick="javascript: voltar();" style="width:35%; height:40px" />	
                                    <input class="botao2" type="button" value="Marcar não lida" onClick="javascript: naoLida();" style="width:35%; height:40px" />
                                </div>
                            </div>
                            </font></div>
                    </div>
                    <script type="text/javascript">$("#divSolicitacoes").load("mensagemManagement.php?action=atualizar");</script>
                </body>
                </html>
';
                break;
            case "lista":
                $nomeObj = "";
                $queryMensagensQuery = $mysqli->query("SELECT `men_id`, `men_obj_id`, `men_solicitante`, `men_tipo`, DATE_FORMAT(`men_data`, '%d/%m/%Y'), `men_lida`, `men_removida` FROM `tb_usuario`, `tb_mensagem` WHERE `men_removida` = 0 AND `men_solicitado` = `usu_id` AND `usu_email` = '" . $usuario->email . "'");

                print "<script type='text/javascript'>
		function apagar(i){
                    if(confirm('Você tem certeza que deseja remover essa mensagem?')){				
                        $('#divResultadoInterno').load('mensagemManagement.php?action=deletar&value=' + i, function(){
                            if($('#divResultadoInterno').is(':hidden')){
                                $('#divResultadoInterno').slideToggle(400, function(){
                                    setTimeout(function(){
                                        $('#divResultadoInterno').slideToggle(800);
                                    }, 3000);  
								});
                            }else{
                                setTimeout(function(){
                                    $('#divResultadoInterno').slideToggle(800);
                                }, 3000); 	
                            }
                            $('#divMeusarquivos').load('mensagemManagement.php?action=lista');
                        });					
                    }
		}
		function visualizar(o){	
                    $('#conteudoInterno').fadeOut(300, function(){
                        $('#conteudoInterno').load('mensagemManagement.php?action=visualizar&men_id=' + o, function(){
                            $('#conteudoInterno').fadeIn(300);
			});	
                    });							
		}
		</script>";
                
				if($queryMensagensQuery->num_rows > 0){
                	$p = 0;
                            print "<table width='100%' cellspacing='0'><tr class='topoTabela'><td class='separador'>Nome</td><td class='separador'>Relacionado a</td><td width='65' class='separador'>Data</td><td width='20'></td></tr>";
                            while ($r = $queryMensagensQuery->fetch_array()) {
                                $nomeSolicitanteQuery = $mysqli->query("SELECT CONCAT(`usu_primeironome`, ' ', `usu_ultimonome`) FROM `tb_usuario` WHERE `usu_id` = '" . $r[2] . "'");
                                $nomeSolicitanteArr = $nomeSolicitanteQuery->fetch_array();
                                $nomeSolicitante = $nomeSolicitanteArr[0];
                                $men_tipo = $nomeObj = "";
                                if ($r[6] == 0) {
                                    if ($r[3] == "arquivo") {
                                        $men_tipo = "Arquivo";
                                        $nomeObj = $mysqli->query("SELECT `arq_titulo` FROM `tb_arquivo` WHERE `arq_id` = " . $r[1]);
                                        $nomeObjArr = $nomeObj->fetch_array();
                                        $nomeObj = $nomeObjArr[0];
                                    } else if ($r[3] == "evento") {
                                        $men_tipo = "Evento";
                                        $nomeObj = $mysqli->query("SELECT `eve_nome` FROM `tb_evento` WHERE `eve_id` = " . $r[1]);
                                        $nomeObjArr = $nomeObj->fetch_array();
                                        $nomeObj = $nomeObjArr[0];
                                    } else {
                                        $men_tipo = "Compartilhamento";
                                        $nomeObj = $mysqli->query("SELECT `arq_nome`, `arq_titulo` FROM `tb_arquivo` WHERE `arq_id` = " . $r[1]);
                                        $nomeObjArr = $nomeObj->fetch_array();
                                        $nomeObj = $nomeObjArr[0];
                                    }
                                    $nomeObj = $nomeSolicitante;
                                    if ($r[5] == 0) {
                                        print "<tr class='nova'>";
                                    } else {
                                        if ($p == 0) {
                                            print "<tr class='par'>";
                                            $p++;
                                        } else {
                                            print "<tr class='impar'>";
                                            $p--;
                                        }
                                    }
                                    if ($r[3] == "arquivo" || $r[3] == "evento") {
                                        print "<td class='separador'><a href='javascript: visualizar(\"" . $r[0] . "\");'>" . utf8_encode($nomeObj) . "</a></td><td class='separador'><a href='javascript: visualizar(\"" . $r[0] . "\");'>" . utf8_encode($men_tipo) . "</a></td><td class='separador'>" . $r[4] . "</td><td align='center'><a href='javascript: apagar(\"" . $r[0] . "\")'><img src='imagens/icon-borracha.png' width='15' /></a></td>";
                                    } else {
                                        print "<td class='separador'><a href='javascript: visualizar1(\"" . $r[0] . "\");'>" . utf8_encode($nomeObj) . "</a></td><td class='separador'><a href='javascript: visualizar1(\"" . $r[0] . "\");'>" . utf8_encode($men_tipo) . "</a></td><td class='separador'>" . $r[4] . "</td><td align='center'><a href='javascript: apagar(\"" . $r[0] . "\")'><img src='imagens/icon-borracha.png' width='15' /></a></td>";
                                    }
                                    print "</tr>";
                                }
                            }
                	print "</table>";
                }else{
                    print "<p align='center'><strong>Não há mensagens.</strong></p>";
                }
                break;
            default:
                print '<!DOCTYPE html>
                        <head>
                            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                            <title>Mensagens</title>
                            <link rel="stylesheet" type="text/css" href="libs/style.css">
                        </head>

                        <body text="#FFFFFF">
                            <div align="center"><br />
                                <font size="+3" color="#FFFFFF"><strong>M</strong>ensagens</font><br /><br />
                                <div id="divResultadoInterno" style="display:none"></div>
                                <br />
                                <div align="justify" style="width:80%">
                                    <font color="#FFFFFF">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nesta seção, estão listadas as mensagens referentes a <em>reports</em> sobre arquivos e/ou eventos cujo qual você é dono, ou informativos sobre arquivos que foram compartilhados com você. Clicando no título serão exibidos mais informações sobre o mesmo.
                                    </font>
                                    <br /><br />
                                    <div id="divMeusarquivos"><script type="text/javascript">$("#divMeusarquivos").load("mensagemManagement.php?action=lista");</script></div>
                                </div>
                            </div>
                        </body>
                        </html>';
                break;
        }
    }
}
?>

