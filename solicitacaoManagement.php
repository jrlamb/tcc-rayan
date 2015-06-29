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
            case "solicitacoes":
                
                $resultado = $mysqli->query("SELECT `A`.`sol_id`, `C`.`arq_titulo`, `D`.`usu_primeironome`, `D`.`usu_ultimonome` FROM `tb_solicitacao` `A`, `tb_usuario` `B`, `tb_arquivo` `C`, `tb_usuario` `D` WHERE `B`.`usu_email` = '" . $usuario->email . "' AND `C`.`arq_id` = `A`.`sol_arq_id` AND`B`.`usu_id` = `A`.`sol_usu_id_solicitado` AND `A`.`sol_atendido` = 0 AND `A`.`sol_usu_id_solicitante` = `D`.`usu_id` AND `A`.`sol_permitido` <> NULL");
                print '<!DOCTYPE html>
                <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>Minhas solicitações</title>
                        <link rel="stylesheet" type="text/css" href="libs/style.css">
                        <script type="text/javascript">
                        function positivo(i) {
                            if ($("#divResultadoInterno").is(":hidden"))
                                $("#divResultadoInterno").slideToggle(400);
                            $("#divResultadoInterno").load("solicitacaoManagement.php?action=avaliarSolicitacao&P&sol_id=" + i, function() {
                                setTimeout(function() {
                                    atualizarLista();
                                    $("#divResultadoInterno").slideToggle(400);
                                }, 2000);
                            });
                        }
                        function negativo(i) {
                            if ($("#divResultadoInterno").is(":hidden"))
                                $("#divResultadoInterno").slideToggle(400);
                            $("#divResultadoInterno").load("solicitacaoManagement.php?action=avaliarSolicitacao&N&sol_id=" + i, function() {
                                setTimeout(function() {
                                    atualizarLista();
                                    $("#divResultadoInterno").slideToggle(400);
                                }, 2000);
                            });
                        }
                        function atualizarLista() {
                            $("#divMinhasSolicitacoes").fadeOut(300, function() {
                                $("#divMinhasSolicitacoes").load("solicitacaoManagement.php?action=solicitacoesInterna", function() {
                                    $("#divSolicitacoes").load("solicitacaoManagement.php?action=atualiza", function() {
                                        if ($("#divSolicitacoes").is(":hidden")) {
                                            $("#divSolicitacoes").slideToggle(400);
                                        }
                                    });
                                    $("#divMinhasSolicitacoes").fadeIn(300);
                                });
                            });
                        }
                        </script>
                    </head>

                <body text="#FFFFFF">
                        <div align="center"><br />
                                <font size="+3" color="#FFFFFF"><strong>S</strong>olicitações</font><br /><br />
                                <div id="divResultadoInterno" style="display:none"></div>
                                <br />
                                <div align="justify" style="width:80%">
                                        <font color="#FFFFFF">
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nesta seção, estão listados os artigos e certificados que foram-lhe solicitados para compartilhar. Para o solicitante receber seu arquivo, você deve permitir utilizando o icone <img src="imagens/icon-positivo.png" width="16" />, caso você não deseje compartilhar seu arquivo, basta clicar no icone <img src="imagens/icon-negativo.png" width="16" />.
                                        </font><br /><br />
                                        <div id="divMinhasSolicitacoes">';
                                if($resultado->num_rows > 0){
                                        print "<table width='100%' cellspacing='0'><tr class='topoTabela'><td width='20' class='separador'></td><td width='auto' class='separador'>Arquivo</td><td width='auto' class='separador'>Solicitante</td><td width='20'></td></tr>";
                                        $p = 0;
                                        while ($r = $resultado->fetch_array()) {
                                                if ($p == 0) {
                                                   	print "<tr class='par'><td align='center' class='separador'><a href='javascript: positivo(\"" . $r[0] . "\");' title='Compartilhar com o usuário'><img border='0' src='icon-positivo.png' width='18' /></td><td class='separador'>" . $r[1] . "</td><td>" . utf8_encode($r[2]) . " " . utf8_encode($r[3]) . "</td><td align='center' class='separador'><a href='javascript: negativo(\"" . $r[0] . "\");' title='Não compartilhar com o usuário'><img border='0' src='icon-negativo.png' width='18' /></td></tr>";
                                                  	$p++;
                                                } else {
                                                  	print "<tr class='impar'><td align='center' class='separador'><a href='javascript: positivo(\"" . $r[0] . "\");' title='Compartilhar com o usuário'><img border='0' src='icon-positivo.png' width='18' /></td><td class='separador'>" . $r[1] . "</td><td class='separador'>" . utf8_encode($r[2]) . " " . utf8_encode($r[3]) . "</td><td align='center' class='separador'><a href='javascript: negativo(\"" . $r[0] . "\");' title='Não compartilhar com o usuário'><img border='0' src='icon-negativo.png' width='18' /></td></tr>";
                                                   	$p--;
                                                }
                                        }
                                        print '</table>';
                                }else{
                                        print "<p align='center'><strong>Não há solicitações.</strong></p>";
                                }
                                print '</div></div></div></body></html>';
                break;
				
            case "solicitacoesInterna":
                $resultado = $mysqli->query("SELECT `A`.`sol_id`, `C`.`arq_titulo`, `D`.`usu_primeironome`, `D`.`usu_ultimonome` FROM `tb_solicitacao` `A`, `tb_usuario` `B`, `tb_arquivo` `C`, `tb_usuario` `D` WHERE `B`.`usu_email` = '" . $usuario->email . "' AND `C`.`arq_id` = `A`.`sol_arq_id` AND`B`.`usu_id` = `A`.`sol_usu_id_solicitado` AND `A`.`sol_atendido` = 0 AND `A`.`sol_usu_id_solicitante` = `D`.`usu_id`");
                print "<table width='100%' cellspacing='0'><tr class='topoTabela'><td width='20'></td><td width='auto'>Arquivo</td><td width='auto'>Solicitante</td><td width='20'></td></tr>";
                $p = 0;
                while ($r = $resultado->fetch_array()) {
                    if ($p == 0) {
                        print "<tr class='par'>";
                        $p++;
                    } else {
                        print "<tr class='impar'>";
                        $p--;
                    }
                    print "<td align='center' class='separador'><a href='javascript: positivo(\"" . $r[0] . "\");' title='Compartilhar com o usuário'><img border='0' src='icon-positivo.png' width='18' /></td><td>" . utf8_encode($r[1]) . "</td><td>" . utf8_encode($r[2]) . "</td><td align='center'><a href='javascript: negativo(\"" . $r[0] . "\");' title='Não compartilhar com o usuário'><img border='0' src='icon-negativo.png' width='18' /></td></tr>";
                }
                print "</table>";
            break;
            case "avaliarSolicitacao":
                if (isset($_GET['sol_id'])) {
                    if (isset($_GET['P'])) {
                        if ($mysqli->query("UPDATE `tb_solicitacao` SET `sol_atendido` = '1', `sol_permitido` = '1', `sol_dtatendido` = CURRENT_TIMESTAMP WHERE `sol_id` = '" . $_GET['sol_id'] . "';")) {
                            echo "<div class='sucesso'>Permissão para partilhar arquivo <strong>CONCEDIDA</strong>!</div>";
                            $mysqli->query("UPDATE `tb_solicitacao` SET `sol_atendido` = '1', `sol_permitido` = '0', `sol_dtatendido` = CURRENT_TIMESTAMP WHERE `sol_id` <> '" . $_GET['sol_id'] . "';");
                        } else{
                            echo "<div class='erro'>Erro ao permitir.</div>";
                        }
                        print "<script type=\"text/javascript\">$('#divSolicitacoes').load('mensagemManagement.php?action=atualizar');</script>";
                    } else if (isset($_GET['N'])) {
                        if ($mysqli->query("UPDATE `tb_solicitacao` SET `sol_atendido` = '1', `sol_permitido` = '0', `sol_dtatendido` = CURRENT_TIMESTAMP WHERE `sol_id` = '" . $_GET['sol_id'] . "';")) {
                            echo "<div class='erro'>Permissão para partilhar arquivo <strong>NEGADA</strong>!</div>";
                        } else{
                            echo "<div class='erro'>Erro ao negar.</div>";
                        }
                        print "<script type=\"text/javascript\">$('#divSolicitacoes').load('mensagemManagement.php?action=atualizar');</script>";
                    }
                }
            break;
			
			case "externoEnviar":
				$output_dir = "temp/Temporario/";
                if (isset($_FILES["myfile"])) {
                    $ret = array();

                    $error = $_FILES["myfile"]["error"];
                    //You need to handle  both cases
                    //If Any browser does not support serializing of multiple files using FormData() 
                    if (!is_array($_FILES["myfile"]["name"])) { //single file
                        $fileName = $_FILES["myfile"]["name"];
                        $c = 0;
						$k =$mysqli->query("SELECT * FROM `tb_arquivo` WHERE `arq_nome` LIKE '%" . $fileName . "%'");
                        while ($k->num_rows != 0) {
                            $fileName = $c++ . $fileName;
							$k = $mysqli->query("SELECT * FROM `tb_arquivo` WHERE `arq_nome` LIKE '%" . $fileName . "%'");
                        }
                        move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir . utf8_decode($fileName));
                        $ret[] = utf8_decode($fileName);
                    } else {  //Multiple files, file[]
                        $fileCount = count($_FILES["myfile"]["name"]);
                        for ($i = 0; $i < $fileCount; $i++) {
                            $fileName = $_FILES["myfile"]["name"][$i];
                            move_uploaded_file($_FILES["myfile"]["tmp_name"][$i], $output_dir . utf8_decode($fileName));
                            $ret[] = $fileName;
                        }
                    }
                    echo json_encode($ret);
				}
			break;
			case "externo":
				$e = $token = $titulo = $tipo = $evento = $eventoID = $tipoID = "";
				$valido = true;
				if (isset($_GET['token'])) {
					//$e = mysql_real_escape_string($_GET['e']);
					$token = $mysqli->real_escape_string($_GET['token']);
				
					$queryVerificacao = $mysqli->query("SELECT `arqtemp_id`, `arqtemp_email_convidado`, `arqtemp_titulo`, `tparq_descricao`, `eve_nome`, `eve_id`, `tparq_id` FROM `tb_arquivo_temporario`, `tb_tipoarquivo`, `tb_evento` WHERE `eve_id` = `arqtemp_eve_id` AND `arqtemp_tparq_id` = `tparq_id` AND `arqtemp_token` = '" . $token . "' AND `arqtemp_realizado` = 0");
				
					if ($queryVerificacao->num_rows > 0) {
						$queryVerificacao = $queryVerificacao->fetch_array();
						$e = $queryVerificacao[1];
						$queryEmail = $mysqli->query("SELECT * FROM `tb_usuario` WHERE `usu_email` = '" . $e . "'");
						$valido = true;
						$titulo = $queryVerificacao[2];
						$tipo = $queryVerificacao[3];
						$evento = $queryVerificacao[4];
						$eventoID = $queryVerificacao[5];
						$tipoID = $queryVerificacao[6];
						if ($queryEmail->num_rows == 0) {
							$mysqli->query("INSERT INTO `tb_usuario` (`usu_id` ,`usu_primeironome` ,`usu_ultimonome` ,`usu_email` ,`usu_ultimologin` ,`usu_cod_sinc`) VALUES (NULL , '', '', '" . $e . "', CURRENT_TIMESTAMP , NULL);");
						}
					}else{
						header('Location: index.php');}
				}
				print '

				<!DOCTYPE html>
				
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<title>Sistema de Compartilhamento de Artigos acadêmicos</title>
					<link rel="icon" type="image/ico" href="imagens/favicon.ico">
					<script src="libs/jquery.js" type="text/javascript"></script>
					<link rel="stylesheet" type="text/css" href="libs/style.css">
					<link rel="stylesheet" type="text/css" href="libs/uploadfilesolit.css" />
					<script src="libs/jquery.uploadfile_solicitacao.js" type="text/javascript"></script>
					<script type="text/javascript">
				
					$(document).ready(function() {
                                            var uploadObj = $("#fileuploader").uploadFile({
                                                url: "solicitacaoManagement.php?action=externoEnviar",
                                                multiple: false,
                                                autoSubmit: true,
                                                fileName: "myfile",
                                                maxFileSize: 1024 * 10000,
                                                showStatusAfterSuccess: false,
                                                dragDropStr: "<div align=\'center\'><font size=\'+1\'><strong>Arraste aqui o arquivo</strong></font><br /><br /></div>",
                                                abortStr: "Abortar",
                                                cancelStr: "Remover",
                                                doneStr: "Concluído!",
                                                extErrorStr: " não pode ser carregado. <br />São permitidos apenas arquivos com a extensão: ",
                                                sizeErrorStr: "O arquivo inserido é muito grande! O máximo permitido é: ",
                                                uploadErrorStr: "Erro ao carregar arquivo."
                                            });
                                            $("#formEnviar").submit(function() {
                                                jQuery.ajax({
                                                   data: $(this).serialize(),
                                                   url: this.action,
                                                   type: this.method,
                                                   success: function(results) {
                                                        $("#conteudoInicial").html(results)
                                                   }
                                                })
                                                return false;
                                            });
                                            $("#ajax-upload-dragdrop").css("width", "80%");
                                        });
				
					</script>
				</head>
				
				<body onLoad="javascript: $(\'#conteudoCentral\').animate({width: \'500px\'}, 500);" text="#FFFFFF">
					<font color="#FFFFFF">
					<div align="center">
                                            <div class="divTitulo">
                                                <div align="center" style="float:left; width:20%"><img class="logo" src="imagens/logo.png" width="100" height="100" /></div>
                                                <div style="float:left; width:80%"><font size="+2"><br />
                                                        Sistema de compartilhamento de artigos acadêmicos</font></div>
                                            </div>
						<div class="conteudoCentral" id="conteudoCentral">
							<div class="acesso" id="divAcesso"><strong>Solicitação de arquivo</strong></div>
							';
							if ($valido) {
								print '
								<form method="post" action="solicitacaoManagement.php?action=externoAcao" id="formEnviar">
									<input type="hidden" name="token" id="token" value="'.$token.'" />
									<input type="hidden" name="arq_nome" id="arq_nome" value="" />
									<input type="hidden" name="arq_titulo" id="arq_titulo" value="'.$titulo.'" />
									<input type="hidden" name="arq_tparq_id" id="arq_tparq_id" value="'. $tipoID.'" />
									<input type="hidden" name="arq_eve_id" id="arq_eve_id" value="'.$eventoID.'" />
				
								   <div id="divResultado" style="display:none"></div>
								   <div class="conteudoInicial" id="conteudoInicial" align="center"><br />
										<br />
										<div id="divForm">
											<div id="fileuploader" style="height:60px; width:100%" align="center">Upload</div>
											<div align="justify" style="width:80%"><br />
												<font size="+1"><strong>* Os dados podem ser posteriormente alterados, com exceção do nome físico do arquivo. *</strong></font> <br />
												<br />
												<table>
													<tr>
														<td align="right" width="50%"><font size="+1" color="#CCCCCC"><strong>Título:</strong></font></td>
														<td align="left" width="50%"><font size="+1" color="#FFFFFF">'.utf8_encode($titulo).'</font></td>
													</tr>
													<tr>
														<td align="right" width="50%"><font size="+1" color="#CCCCCC"><strong>Tipo de publicação:</strong></font></td>
														<td align="left" width="50%"><font size="+1" color="#FFFFFF">'.utf8_encode($tipo).'</font></td>
													</tr>
													<tr>
														<td align="right" width="50%"><font size="+1" color="#CCCCCC"><strong>Evento:</strong></font></td>
														<td align="left" width="50%"><font size="+1" color="#FFFFFF">'.utf8_encode($evento).'</font></td>
													</tr>
												</table>
											</div>
										</div>   
									</div>
							</div>
							<div align="center" id="divRodape">
								<div id="divRodapeDentro" align="center"><font size="2"><a href="mailto:chemin.rayan@gmail.com?Subject=Contato pessoal" target="_blank"><strong>C</strong>ontato</a> <font color="#CCC" size="2"><strong>|</strong></font> <a href="mailto:chemin.rayan@gmail.com?Subject=Problema no sistema" target="_blank"><strong>P</strong>roblema</a> <font color="#CCC" size="2"><strong>|</strong></font> <a href="mailto:chemin.rayan@gmail.com?Subject=Sugestão para o sistema" target="_blank"><strong>S</strong>ugestão </font> </a> <font color="#CCC" size="2"> <strong> | Todos os direitos reservados &reg;</strong> </font> </div>
							</div>
						</div>
					</form>
					';
				} else {
					print '<br /><br /><div class="erro">Solicitação já realizada ou inválida!</div><script type="text/javascript">alert("Solicitação já realizada ou inválida!"); window.location="index.php";</script>';
				}
				print '</div></font></body></html>';
			break;
			
			case "externoAcao":			
				$token = $_POST['token'];
				$arq_nome = $mysqli->real_escape_string($_POST['arq_nome']);
				$arq_titulo = $mysqli->real_escape_string($_POST['arq_titulo']);
				$arq_tipo = $mysqli->real_escape_string($_POST['arq_tparq_id']);
				$arq_eve_id = $mysqli->real_escape_string($_POST['arq_eve_id']);
				
				$id = $mysqli->query("SELECT `usu_id` FROM `tb_usuario`, `tb_arquivo_temporario` WHERE `usu_email` = `arqtemp_email_convidado` AND `arqtemp_token` = '" . $token . "'");
				$id = $id->fetch_array();
				$id = $id[0];
				$idSolicitante = $mysqli->query("SELECT `arqtemp_convidante` FROM `tb_arquivo_temporario` WHERE `arqtemp_token` = '" . $token . "'");
				$idSolicitante = $idSolicitante->fetch_array();
                $idSolicitante = $idSolicitante[0];
				print '<font color="#FFFFFF"><div align="center">
						<br />
						<font size="+3" color="#FFFFFF"><strong>S</strong>ucesso!</font><br />
						<br /><div align="justify" style="width:80%">';
				
				if ($mysqli->query("INSERT INTO `tb_arquivo`( `arq_id` , `arq_nome` , `arq_titulo` , `arq_tparq_id` , `arq_necessitaatualizar` , `arq_dono` , `arq_eve_id` ) VALUES ( NULL, '" . $arq_nome . "', '" . $arq_titulo . "', " . $arq_tipo . ", '0', '" . $id . "', '" . $arq_eve_id . "' );")) {
					$idArq = $mysqli->query("SELECT `arq_id` FROM `tb_arquivo` WHERE `arq_nome` = '" . $arq_nome . "'");
					$idArq = $idArq->fetch_array();
					$idArq = $idArq[0];

					$mysqli->query("INSERT INTO `tb_sincronizado` (`sinc_usu_id` , `sinc_arq_id` , `sinc_remover` , `sinc_data` ) VALUES ( '" . $id . "', '" . $idArq . "', '0', NOW());");
					$mysqli->query("INSERT INTO `tb_arquivo_autores` ( `arqaut_id` , `arqaut_usu_id` , `arqaut_arq_id` ) VALUES ( NULL , '" . $id . "', '" . $idArq . "' )");
					$mysqli->query("UPDATE `tb_arquivo_temporario` SET `arqtemp_realizado` = '1' WHERE `arqtemp_token` = '" . $token . "';");
					if (file_exists("temp/Conta/" . $arq_nome) && copy("temp/Conta/" . $arq_nome, "temp/Temporario/" . $arq_nome)) {
							$mysqli->query("INSERT INTO `tb_solicitacao` ( `sol_id` , `sol_usu_id_solicitante` , `sol_usu_id_solicitado` , `sol_arq_id` , `sol_dtpedido` , `sol_dtatendido` , `sol_atendido` , `sol_permitido` ) VALUES ( NULL , '" . $idSolicitante . "', '" . $id . "', '" . $idArq . "', CURRENT_TIMESTAMP , CURRENT_TIMESTAMP , '1', 1 )");
					} else {
							$mysqli->query("INSERT INTO `tb_solicitacao` ( `sol_id` , `sol_usu_id_solicitante` , `sol_usu_id_solicitado` , `sol_arq_id` , `sol_dtpedido` , `sol_dtatendido` , `sol_atendido` , `sol_permitido` ) VALUES ( NULL , '" . $idSolicitante . "', '" . $id . "', '" . $idArq . "', CURRENT_TIMESTAMP , CURRENT_TIMESTAMP , '0', NULL )");
					}
					print 'O arquivo foi enviado com sucesso, e ficou nomeado como: <em>' . utf8_encode($arq_nome) . '</em>.</strong><br /><p align="center"><img src="imagens/icon-ok.png" width="80" /></p>';
} else {
					print '<font color="red">Erro ao enviar o arquivo.</font><br /><p align="center"><img src="imagens/icon-faltando.png" width="80" /></p>';
					if (file_exists($arq_nome)) {
						unlink($arq_nome);
					}
				}
				print '</div></div></font><script type="text/javascript"> setTimeout(function(){window.location = "index.php";}, 3000);</script>';
			break;
			case "atualiza":
				$query = $mysqli->query("SELECT COUNT(*) FROM `tb_solicitacao` `A`, `tb_usuario` `B`, `tb_arquivo` `C`, `tb_usuario` `D` WHERE `B`.`usu_email` = '" . $usuario->email . "' AND `C`.`arq_id` = `A`.`sol_arq_id` AND`B`.`usu_id` = `A`.`sol_usu_id_solicitado` AND `A`.`sol_atendido` = 0 AND `A`.`sol_usu_id_solicitante` = `D`.`usu_id` AND `A`.`sol_permitido` <> NULL");
				$r = $query->fetch_array();
				print "<a href='javascript: menuInterno(\"sol\");'>Você possui " . $r[0];
				print ($r[0] <= 1) ? " solicitação" : " solicitações";
				print "</a>";
			break;
			case "solicitarAcao":
			include("debug.php");
			if (isset($_GET['nome'])) {
				$qq = $mysqli->query("SELECT `usu_id` FROM `tb_usuario` WHERE `usu_email` = '" . $usuario->email . "'");
				$qq = $qq->fetch_array();
				$mID = $qq[0];
				$queryVerifica = $mysqli->query("SELECT COUNT(*) FROM `tb_solicitacao`, `tb_arquivo`, `tb_usuario` WHERE `arq_id` = `sol_arq_id` AND `arq_titulo` LIKE '%" . $_GET['nome'] . "%' AND `sol_usu_id_solicitante` = `usu_id` AND `usu_email` = '" . $usuario->email . "'");
				$r = $queryVerifica->fetch_array();
				if ($r[0] == 0) {
					$queryListar = $mysqli->query("SELECT DISTINCT `usu_id`, `arq_id` FROM `tb_usuario`, `tb_sincronizado`, `tb_arquivo` WHERE `arq_id` = `sinc_arq_id` AND `sinc_usu_id` = `usu_id` AND `usu_email` <> '" . $usuario->email . "' AND `arq_titulo` LIKE '%" . $_GET['nome'] . "%'");
					$count = 0;
					while ($ri = $queryListar->fetch_array()) {
						if($mysqli->query("INSERT INTO `tb_solicitacao` ( `sol_id` , `sol_usu_id_solicitante` , `sol_usu_id_solicitado` , `sol_arq_id` , `sol_dtpedido` , `sol_dtatendido` , `sol_atendido` , `sol_permitido` ) VALUES ( NULL , '" . $mID . "', '" . $ri[0] . "', '" . $ri[1] . "', CURRENT_TIMESTAMP , NULL , '0', NULL )")){
							$count++;
						}
					}
					print utf8_decode("<div class='sucesso'>Sua solicitação foi enviada para " . $count . " usuários.</div>");
				} else
					print  "<div class='erro'>Sua solicitação já foi registrada. Favor aguardar.</div>";
			}
			break;
			
			default:
			
			print '<!DOCTYPE html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Solicitar arquivo</title>
        <link rel="stylesheet" type="text/css" href="libs/style.css">
            <link rel="stylesheet" href="libs/jquery-ui.min.css" type="text/css" /> 
            <script type="text/javascript" src="libs/jquery.js"></script>
            <script type="text/javascript" src="libs/jquery-ui.min.js"></script>	
            <script type="text/javascript">
                    $("#nome").autocomplete({
                        source: "procuraManagement.php?action=arquivo",
                        minLength: 2,
                        select: function(event, ui) {
                            var label = ui.item.label;
                            var value = ui.item.value;
                            $("#divResultadoInterno").load("solicitacaoManagement.php?action=solicitarAcao&nome=" + value, function( response, status, xhr ) {			
								$("#divResultadoInterno").fadeIn(400);
								$("#divResultadoInterno").html(response);
								setTimeout(function(){
									$("#divResultadoInterno").slideToggle(400);
								}, 3000);
							});
                        }
                    });
                
                function naoEncontrei() {
                    abrirInterno("convidarManagement.php?action=op");
                }
            </script>
    </head>

    <body>
        <div align="center"><br />
            <font color="#FFFFFF">
                <font size="+3"><strong>S</strong>olicitar <strong>a</strong>rquivo</font><br /><br />
                <div id="divResultadoInterno" style="display:none"></div>
                Informe o título do arquivo:<br />
                <input type="text" name="nome" id="nome" required="required" style="height:30px; font-size:20px" /><br /><br /><br />
                <a href="javascript: naoEncontrei();" style="color: lightblue!important">Não encontrou o arquivo que deseja, mas conhece alguém que tenha? <br />Clique aqui e solicite o arquivo através do site, e ajude-nos a crescer!</a>
            </font>
        </div>
    </body>
</html>';
        break;
        }
    }
}
?>

