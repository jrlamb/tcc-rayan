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

$numSolicitacoes = 0;
$numMensagens = 0;
$queryMensagens = $mysqli->query("SELECT COUNT(*) FROM `tb_mensagem`, `tb_usuario` WHERE `usu_email` = '" . $usuario->email . "' AND `usu_id` = `men_solicitado` AND `men_removida` = '0' AND `men_lida` = '0'");
$querySolicitacoes = $mysqli->query("SELECT COUNT(*) FROM `tb_solicitacao`, `tb_usuario` WHERE `usu_email` = '" . $usuario->email . "' AND `usu_id` = `sol_usu_id_solicitado` AND `sol_atendido` = '0' AND `sol_permitido` <> NULL");
$queryVerificacao = $mysqli->query("SELECT `usu_cod_sinc` FROM `tb_usuario` WHERE `usu_email` = '" . $usuario->email . "'");
if ($querySolicitacoes->num_rows > 0) {
    $numSolicitacoes = $querySolicitacoes->current_field;
}
if ($queryMensagens->num_rows > 0) {
    $numMensagens = $queryMensagens->current_field;
}
$v = $msg = "";
if (is_null($queryVerificacao->current_field)) {
    $v = '$(".divMenu").hide(); $(".divMenuPontaBaixo").hide();';
    $msg = "<p align='justify'>
					<font size='+1' color='#FFFFFF'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Por ser seu primeiro login, necessitamos que você realize o download do software de serviço, para assim gerar o código de sincronismo e tornar sua conta ativa!</font></p>";
}
echo "
				<script type='text/javascript'>
					function abrirInterno(t){
						$('#conteudoInterno').fadeOut(300, function(){
							$('#carregandoI').show();
							$('#conteudoInterno').load(t + '.php?A', function(){
								$('#carregandoI').fadeOut(100, function(){
									$('#conteudoInterno').fadeIn(300);										
								});								
							});	
						});
						
						$('#divSolicitacoes').load('mensagemManagement.php?action=atualizar');										
					}
					function abrirInterno(t, y){
						$('#conteudoInterno').fadeOut(300, function(){
							$('#carregandoI').show();
							$('#conteudoInterno').load(t + '.php?action=' + y, function(){
								$('#carregandoI').fadeOut(100, function(){
									$('#conteudoInterno').fadeIn(300);										
								});								
							});	
						});
						
						$('#divSolicitacoes').load('mensagemManagement.php?action=atualizar');										
					}
					function menuInterno(m){
						switch(m){
							case 'o': 
								abrirInterno('download');
							break;
							case 's': 
								abrirInterno('solicitacaoManagement');
							break;
							case 'e': 
								abrirInterno('arquivoManagement', 'enviar');
							break;
							case 'm': 
								abrirInterno('arquivoManagement');
							break;
							case 'c': 
								abrirInterno('compararManagement');
							break;
							case 'sol': 
								abrirInterno('solicitacaoManagement');
							break;
							case 'g': 
								abrirInterno('graficos');
							break;
							case 'ev': 
								abrirInterno('eventoManagement');
							break;
							default:
								alert('Erro');
							break;
						}
					}
				</script>
				<div class='floatMenor1' align='center'>
				<br />
					<div class='divMenuPontaCima'>
						<a href='javascript: menuInterno(\"o\");'>
							<div class='float' style='width:50px; height:100%'><img src='imagens/icon-download.png' width='32' /></div>
							<div class='float' style='width:110px; padding-top:8px; height:100%'>Obter Software</div>
						</a>
					</div>
					<div class='divMenu'>
						<a href='javascript: menuInterno(\"s\");'>
							<div class='float' style='width:50px; height:100%'><img src='imagens/icon-adicionar.png' width='32' /></div>
							<div class='float' style='width:110px; padding-top:8px; height:100%'>Solicitar</div>
						</a>					
					</div>
					<div class='divMenu'>
						<a href='javascript: menuInterno(\"e\");'>
							<div class='float' style='width:50px; height:100%'><img src='imagens/icon-enviar.png' width='32' /></div>
							<div class='float' style='width:110px; padding-top:8px; height:100%'>Enviar</div>
						</a>
					</div>
					<div class='divMenu'>
						<a href='javascript: menuInterno(\"m\");'>
							<div class='float' style='width:50px; height:100%'><img src='imagens/icon-pasta.png' width='32' /></div>
							<div class='float' style='width:110px; padding-top:8px; height:100%'>Meus arquivos</div>
						</a>
					</div>
					<div class='divMenu'>
						<a href='javascript: menuInterno(\"ev\");'>
							<div class='float' style='width:50px; height:100%'><img src='imagens/icon-evento.png' width='32' /></div>
							<div class='float' style='width:60%; height:100%'>Eventos</em></div>
						</a>
					</div>
					<div class='divMenu'>
						<a href='javascript: menuInterno(\"c\");'>
							<div class='float' style='width:50px; height:100%'><img src='imagens/icon-comparar.png' width='32' /></div>
							<div class='float' style='width:60%; height:100%'>Comparar com <em>lattes</em></div>
						</a>
				</div>
				<div class='divMenuPontaBaixo'>
					<a href='javascript: menuInterno(\"g\");'>
							<div class='float' style='width:50px; height:100%'><img src='imagens/icon-grafico.png' width='32' /></div>
							<div class='float' style='width:60%; height:100%'>Gráficos</div>
						</a>
					</div>
				</div>
				<div align='right'>
					<div id='divSolicitacoes' style='display:none; width:100%'>
						<div class='float'>
							<a href='javascript: menuInterno(\"solicitacaoManagement\", \"solicitacoes\");'><img src='imagens/icon-solicitacao.png' width='25' />" . $numSolicitacoes . "</a>
						</div>
						<div class='float'>
							<a href='javascript: abrirInterno(\"mensagemManagement\", \"op\");'><img src='imagens/icon-mensagem.png' width='25' />" . $numMensagens . "</a>
						</div>
					</div>
				</div>
				<div class='floatMaior1' align='center' id='conteudoInterno'>
					<div style='width:80%'>
					<br />
					<font size='+4' color='#FFFFFF'><strong>P</strong></font><font size='+3' color='#FFFFFF'>ainel de</font> <font size='+4' color='#FFFFFF'><strong>C</strong></font><font size='+3' color='#FFFFFF'>ontrole</font>
					<br /><br /><br />" . $msg . "					
					<p align='justify'>
					<font size='+1' color='#FFFFFF'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Este é o seu painel de controle, nele é possivel enviar artigos (contendo ou não os arquivos), listar seus artigos disponibilizados e também compartilhados com você, solicitar artigos que outros usuários possuam e comparar com sua base de dados com a plataforma <em>lattes</em>!</font></p>
					
					</div>
					</div>
				</div>
				<div id='carregandoI' style='display:none'><br /><br /><br /><br /><br /><br />
					<img src='imagens/carregando.gif' width='60' /><br /><font color='#FFFFFF' size='2'>Carregando...</font>
				</div>";
print "<script type='text/javascript'> " . $v . "
				if($(\"#divSolicitacoes\").is(':hidden')){
					$(\"#divSolicitacoes\").slideToggle(400);
					$('#carregandoI').hide();
				}</script>";
?>