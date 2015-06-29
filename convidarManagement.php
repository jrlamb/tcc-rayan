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
		$varCod;
		 if(isset($_GET['cod'])){
			 $varCod = $_GET['cod'];
		 }
        switch ($varAction) {
            	
          
            case "convidar2":
                    $id = "";
                    if (isset($_GET['arq_id'])) {
                            $id = $_GET['arq_id'];
                    }
                     print '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                            <html xmlns="http://www.w3.org/1999/xhtml">
                                    <head>
                                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                                    <title>Solicitar por email</title>
                                    <link rel="stylesheet" type="text/css" href="libs/style.css">
                                        <script src="libs/jquery.js"></script>
                                        <script type="text/javascript">
                                            $("#email").keyup(function() {
                                                var valor = $("#email").val().replace(/[^a-z_.0-9]+/g, "");
                                                $("#email").val(valor);
                                            });

                                            $().ready(function(e) {
                                                $("#formConvidar").submit(function() {
                                                    $("#btnSubmit").attr("disabled", "disabled");
                                                    var thistarget = "#divResultadoInterno";
                                                    jQuery.ajax({
                                                        data: $(this).serialize(),
                                                        url: this.action,
                                                        type: this.method,
                                                        success: function(results) {
                                                            $(thistarget).html(results)
                                                            if ($(thistarget).is(":hidden")) {
                                                                $(thistarget).slideToggle(400, function() {
                                                                    setTimeout(function() {
                                                                        if ($(thistarget).is(":visible")) {
                                                                            $(thistarget).slideToggle(800);
                                                                            menuInterno("m");
                                                                        }
                                                                    }, 3000);
                                                                });
                                                            }
                                                        }
                                                    })
                                                    return false;
                                                });
                                            });
                                        </script>
                                    </head>

                                    <body onload="javascript: document.getElementById(\'email\').focus();">
                                        <div align="center" style="width:80%">
                                            <font color="#FFFFFF">
                                                <form id="formConvidar" method="post" action="convidarManagement.php?action=convidar2Acao">
                                                    <input type="hidden" name="arq_id" value="'.$id.'" />
                                                    <font size="+3"><strong>C</strong>onvidar por <strong>e</strong>mail</font>
                                                    <div id="divResultadoInterno" style="display:none"></div><br />
                                                    <br />
                                                    <br />
                                                    <div id="etapaEmail">
                                                        <font size="+1"><strong>Email: </strong></font><br />
                                                        <input type="text" name="email" id="email" style="width:35%" autocomplete="off" autofocus="autofocus" required="required" />		
                                                        <font size="2"><em>@gmail.com</em></font>
                                                        <br /><br />
                                                        <input class="botao2" type="submit" id="btnSubmit" value="Enviar convite!" style="width:35%; height:40px" />
                                                    </div>    
                                                    </div>
                                                </form>
                                            </font>
                                        </div>
                                    </body>
                            </html>';
            break;
            case "convidar2Acao":
            function anti($sql) {
                $sql = preg_replace(sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/"), "", $sql);
                $sql = trim($sql);
                $sql = strip_tags($sql);
                $sql = addslashes($sql);
                $sql = str_replace("<script", "", $sql);
                $sql = str_replace("<iframe", "", $sql);
                $sql = str_replace("<form", "", $sql);
                $sql = str_replace("<input", "", $sql);
                $sql = str_replace("</script>", "", $sql);
                $sql = str_replace("</iframe>", "", $sql);
                $sql = str_replace("</form>", "", $sql);
                $sql = str_replace("</input>", "", $sql);
                return $sql;
            }


                $arq_nome = $arq_titulo = $arq_tipo = $eve_nome = "";
                if (isset($_POST['email']) && isset($_POST['arq_id'])) {

                    $verifica = $mysqli->query("SELECT * FROM `tb_usuario` WHERE `usu_email` = '" . $_POST['email'] . "@gmail.com'");
                    if ($verifica->num_rows == 0) {
                        $mysqli->query("INSERT INTO `tb_usuario` ( `usu_id` , `usu_primeironome` , `usu_ultimonome` , `usu_email` , `usu_ultimologin` , `usu_cod_sinc` ) VALUES ( NULL , '', '', '" . $_POST['email'] . "', '', NULL );");
                        $query = $mysqli->query("SELECT DISTINCT `arq_nome`, `arq_titulo`, `tparq_descricao`, `eve_nome` FROM `tb_usuario`, `tb_arquivo`, `tb_evento`, `tb_tipoarquivo` WHERE `eve_id` = `arq_eve_id` AND `arq_tparq_id` = `tparq_id` AND `arq_id` = '" . $_POST['arq_id'] . "'");

                        $r = $query->fetch_array();
                        $arq_nome = $r[0];
                        $arq_titulo = $r[1];
                        $arq_tipo = $r[2];
                        $eve_nome = $r[3];
                        $pessoa = $usuario->primeiroNome . ' ' . $usuario->ultimoNome;
                        $headers = "MIME-Version: 1.0\n";
                        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
                        $headers .= "From: \"Sistema\" <curriculum@curriculum.info.md.utfpr.edu.br>\r\n";
                        $corpo = "<html><body>
                        <h3>Olá!</h3><br />
                        <div align='justify' style='width:80%'><font size='+1'>Informamos que através do sistema de compartilhamento de arquivos acadêmicos:  " . $pessoa . " <em>(" . $usuario->email . ")</em> compartilhou um arquivo com você!</font>
                        <br /><br />
                        <div align='center'>
                        <font size='2'>
                        <table>
                            <tr>
                                <td align='right'><strong>Arquivo:</strong> </td>
                                <td>" . $arq_nome . "</td>
                            </tr>
                            <tr>
                                <td align='right'><strong>Título:</strong> </td>
                                <td>" . $arq_titulo . "</td>
                            </tr>
                            <tr>
                                <td align='right'><strong>Tipo:</strong> </td>
                                <td>" . $arq_tipo . "</td>
                            </tr>
                            <tr>
                                <td align='right'><strong>Postado no evento:</strong> </td>
                                <td>" . $eve_nome . "</td>
                            </tr>
                        </table>
                        </font>
                        </div><br /><br />
                        </div></body></html>\r\n";

                        if (mail($_POST['email'], utf8_decode("Há um arquivo compartilhado com você!"), utf8_decode($corpo), $headers)) {
                            if ($mysqli->query("INSERT INTO `tb_solicitacao` ( `sol_id` , `sol_usu_id_solicitante` , `sol_usu_id_solicitado` , `sol_arq_id` , `sol_dtpedido` , `sol_dtatendido` , `sol_atendido` , `sol_permitido` ) VALUES ( NULL , '" . $uId . "', '" . $id . "', '" . $arq_id . "', CURRENT_TIMESTAMP , CURRENT_TIMESTAMP , '0', '1');")) {
                                $mysqli->query("INSERT INTO `tb_mensagem` (`men_solicitante` , `men_solicitado` , `men_obj_id` , `men_tipo` , `men_obs` , `men_data` , `men_lida` , `men_removida` ) VALUES ('" . $id . "', '" . $uId . "', '" . $arq_id . "', 'compartilhar', '', NOW(), '0', '0' );");
                                    echo "<div class='sucesso'>Convite enviado com sucesso!</div>";
                            } else {
                                echo "<div class='erro'>Erro ao enviar o convite.</div>";
                            }
                        } else {
                            echo "<div class='erro'>Erro ao enviar o convite.</div>";
                        }
                    }else {
                        echo "<div class='erro'>Esta pessoa já está no sistema.</div>";
                    }				
            }
            break;
            case "convidarAcao":
                    if (isset($_POST['email'])) {
                            $email = $mysqli->real_escape_string($_POST['email']);
                            $arq_titulo = $mysqli->real_escape_string($_POST['titulo']);
                            $arq_tipo = $mysqli->real_escape_string($_POST['tipo']);
                            $eve_nome = $mysqli->real_escape_string($_POST['eve_nome']);
                            $verifica = $mysqli->query("SELECT * FROM `tb_usuario` WHERE `usu_email` = '" . $_POST['email'] . "@gmail.com'");
                            $verificaArquivo = $mysqli->query("SELECT * FROM `tb_arquivo` WHERE `arq_titulo` LIKE '%" . $arq_titulo . "%'");

                            if ($verifica->num_rows == 0 && $verificaArquivo->num_rows == 0) {
                                $pessoa = $usuario->primeiroNome . ' ' . $usuario->ultimoNome;
                                $id = $mysqli->query("SELECT `usu_id` FROM `tb_usuario` WHERE `usu_email` = '" . $usuario->email . "'");
                                $id = $id->fetch_array();
                                $id = $id[0];
                                $evId = $mysqli->query("SELECT `eve_id` FROM `tb_evento` WHERE `eve_nome` = '" . $eve_nome . "'");
                                $evId = $evId->fetch_array();
                                $evId = $evId[0];
                                $arq_tipo_nome = $mysqli->query("SELECT `tparq_descricao` FROM `tb_tipoarquivo` WHERE `tparq_id` = '" . $arq_tipo . "'");
                                $arq_tipo_nome = $arq_tipo_nome->fetch_array();
                                $arq_tipo_nome = $arq_tipo_nome[0];

                                $headers = "MIME-Version: 1.0\n";
                                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
                                $headers .= "From: \"Sistema\" <curriculum@curriculum.info.md.utfpr.edu.br>\r\n";
                                $gen = md5('rayfox' . time() . $email);
                                while (($t = $mysqli->query("SELECT * FROM `tb_arquivo_temporario` WHERE `arqtemp_token` = '" . $gen . "'")) && $t->num_rows != 0) {
                                    $gen = md5('rayfox0' . time() . $email . rand());
                                }
                                $corpo = "<html><body>
                                        <h3>Olá!</h3><br />
                                        <div align='justify' style='width:80%'><font size='+1'>Informamos que: " . $pessoa . " está solicitando um arquivo seu.<br />Para responder a esta solicitação, ao final da mensagem há um link para carrega-lo através do sistema de compartilhamento de arquivos acadêmicos.</font>
                                        <br /><br />
                                        <div align='center'>
                                        <font size='2'>
                                        <table>
                                            <tr>
                                                <td align='right'><strong>Título:</strong> </td>
                                                <td>" . $arq_titulo . "</td>
                                            </tr>
                                            <tr>
                                                <td align='right'><strong>Tipo:</strong> </td>
                                                <td>" . $arq_tipo_nome . "</td>
                                            </tr>
                                            <tr>
                                                <td align='right'><strong>Postado no evento:</strong> </td>
                                                <td>" . $eve_nome . "</td>
                                            </tr>
                                        </table>
                                        </font>
                                        </div><br /><br /><a href='http://www.odanilo.com/rayantcc/solicitacaoManagement.php?action=externo&token=" . $gen . "' target='_blank'><font color='#000'><strong>Clique aqui para acessar!</strong></font></a>

                                        </div></body></html>\r\n";
                                    if ($mysqli->query("INSERT INTO `tb_usuario` ( `usu_id` , `usu_primeironome` , `usu_ultimonome` , `usu_email` , `usu_ultimologin` , `usu_cod_sinc` ) VALUES ( NULL , '', '', '" . $email . "@gmail.com', '', NULL );")) {
                                        $em = mail($email . '@gmail.com', utf8_decode("Há uma solicitação de arquivo para você!"), utf8_decode($corpo), $headers);
                                        $uId = $mysqli->query("SELECT `usu_id` FROM `tb_usuario` WHERE `usu_email` = '" . $email . "@gmail.com'");
                                        $uId = $uId->fetch_array();
                                        $uId = $uId[0];
                                        $qq = $mysqli->query("INSERT INTO `tb_arquivo_temporario` ( `arqtemp_id` , `arqtemp_email_convidado` , `arqtemp_convidante` , `arqtemp_dtconvite` , `arqtemp_titulo` , `arqtemp_tparq_id` , `arqtemp_eve_id`, `arqtemp_token`, `arqtemp_realizado` ) VALUES ( NULL , '" . $email . "@gmail.com', " . $id . ", CURRENT_TIMESTAMP , '" . $arq_titulo . "', " . $arq_tipo . ", " . $evId . ", '" . $gen . "', 0 )");
                                        if ($em && $qq) {
                                            echo "<div class='sucesso'>Solicitação enviada com sucesso!</div>";
                                        } else {
                                            echo "<div class='erro'>Erro ao enviar a solicitação.</div>";
                                        }
                                    } else {
                                        echo "<div class='sucesso'>Erro ao enviar a solicitação.</div>";
                                    }
                            } else {
                                echo "<div class='sucesso'>Esta pessoa/arquivo já está no sistema, então foi enviada uma mensagem através do sistema solicitando o arquivo.</div>";
                                $idUsuarioEncontrado = $mysqli->query("SELECT `usu_id` FROM `tb_usuario` WHERE `usu_email` = '".$email."@gmail.com'");
                                $idUsuarioEncontrado = $idUsuarioEncontrado->fetch_array();
                                $idUsuarioEncontrado = $idUsuarioEncontrado[0];
                                $mysqli->query("INSERT INTO `tb_mensagem` (`men_id`, `men_solicitante`, `men_solicitado`, `men_tipo`, `men_data`, `men_lida`, `men_removida`) VALUES (NULL, ".$id.", ".$$idUsuarioEncontrado.", 'solicitacao', CURRENT_TIMESTAMP, 0 ,0");
                            }
                    } else {
                        echo "<div class='erro'>Erro.</div>";
                    }
            break;
            default:
                print '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Solicitar por email</title>
        <link rel="stylesheet" href="libs/jquery-ui.min.css" type="text/css" />
        <link rel="stylesheet" type="text/css" href="libs/style.css">
            <script src="libs/jquery.js" type="text/javascript"></script>
            <script type="text/javascript" src="libs/jquery-ui.min.js"></script>
            <script type="text/javascript">
                $("#email").keyup(function() {
                    var valor = $("#email").val().replace(/[^a-z_.0-9]+/g, "");
                    $("#email").val(valor);
                });

                function alertar(msg) {
                    $("#divResultadoInternoConv").html(msg);
                    if ($("#divResultadoInternoConv").is(":hidden")) {
                        $("#divResultadoInternoConv").slideToggle(300, function() {
                            setTimeout(function() {
                                if (!$("#divResultadoInternoConv").is(":hidden"))
                                    $("#divResultadoInternoConv").slideToggle(300);
                            }, 2000)
                        });
                    } else {
                        $("#divResultadoInternoConv").fadeOut(300);
                    }
                }
                function menu(o) {
                    if (o == "e") {
                        $("#etapaTitulo").fadeOut(300, function() {
                            $("#etapaEmail").fadeIn(300);
                        });
                    } else if (o == "t") {
                        if ($("#email").val().length < 4) {
                            alertar("<div class=\'erro\'>Informe um email válido.</div>");
                        } else {
                            $("#etapaEmail").fadeOut(300, function() {
                                $("#etapaEvento").fadeOut(300, function() {
                                    $("#etapaTitulo").fadeIn(300);
                                });
                            });
                        }
                    } else if (o == "ev") {
                        if ($("#titulo").val().length < 5) {
                            alertar("<div class=\'erro\'>O Título deve conter ao menos 5 caracteres.</div>");
                        } else {
                            $("#etapaTitulo").fadeOut(300, function() {
                                $("#etapaEvento").fadeIn(300);
                            });
                        }
                    }
                }
                function aoClicar(value) {
                    $("#divInfoEvento").load("eventoManagement.php?action=info&nome=" + value.split(" ").join("+"));
                    $("#nome").attr("readonly", "readonly");
                    $("#nome").css("background-color", "#FFC");
                    $("#ev").val(value);
                    $("#btnRetirar").fadeIn(300);
                    if ($("#divInfoEvento").is(":hidden"))
                        $("#divInfoEvento").fadeIn(400);
                }
                function retirar() {
                    $("#btnRetirar").fadeOut(300);
                    $("#nome").removeAttr("readonly");
                    $("#nome").css("background-color", "");
                    $("#nome").val("");
                    $("#divInfoEvento").fadeOut(300);
                    $("#ev").val("");
                }
                $().ready(function(e) {
                    $("#nome").autocomplete({
                        source: "procuraManagement.php?action=evento",
                        minLength: 2,
                        select: function(event, ui) {
                            var label = ui.item.label;
                            var value = ui.item.value;
                            aoClicar(value);
                        }
                    });
                    $("#formConvidar1").submit(function() {
                        jQuery.ajax({
                            data: $(this).serialize(),
                            url: this.action,
                            type: this.method,
                            success: function(results) {
                                $("#divResultadoInternoConv").html(results)
                                if ($("#divResultadoInternoConv").is(":hidden")) {
                                    $("#divResultadoInternoConv").slideToggle(400, function() {
                                        setTimeout(function() {
                                            if ($("#divResultadoInternoConv").is(":visible")) {
                                                $("#divResultadoInternoConv").slideToggle(400);
                                            }
                                			$("#conteudoInterno").load("convidarManagement.php?action=op");

                                        }, 3000);
                                    });
                                } else {
                                    if ($("#divResultadoInternoConv").is(":visible")) {
                                        $("#divResultadoInternoConv").slideToggle(400);
                                    }
                                }
                            }
                        })
                        return false;
                    }
                    );
                });
                function m(o) {
                    $("#divResultadoInternoConv").html(o)
                    if ($("#divResultadoInternoConv").is(":hidden")) {
                        $("#divResultadoInternoConv").slideToggle(400, function() {
                            setTimeout(function() {
                                if ($("#divResultadoInternoConv").is(":visible")) {
                                    $("#divResultadoInternoConv").slideToggle(400);
                                }
                                $("#conteudoInterno").load("convidarManagement.php?action=op");
                            }, 3000);
                        });
                    } else {
                        if ($("#divResultadoInternoConv").is(":visible")) {
                            $("#divResultadoInternoConv").slideToggle(400);
                        }
                    }
                }
            </script>
    </head>
    <body onload="javascript: document.getElementById(\'email\').focus();">
        <div align="center" style="width:80%"> <font color="#FFFFFF">
                <form id="formConvidar1" method="post" action="convidarManagement.php?action=convidarAcao">
                    <font size="+3"><strong>S</strong>olicitar por <strong>e</strong>mail</font>
                    <div id="divResultadoInternoConv" style="display:none"></div>
                    <br />
                    <br />
                    <br />
                    <div id="etapaEmail"> <font size="+1"><strong>Email: </strong></font><br />
                        <input type="text" name="email" id="email" style="width:35%" autocomplete="off" autofocus="autofocus" required="required" />
                        <font size="2"><em>@gmail.com</em></font> <br />
                        <br />
                        <input class="botao2" type="button" value="Avançar ->" onClick="javascript: menu(\'t\');" style="width:35%; height:40px" />
                    </div>
                    <div id="etapaTitulo" style="display:none"> <br />
                        <strong> <font color="#FFFFFF" size="+1">Título:</font></strong> <br />
                        <input type="text" name="titulo" id="titulo" style="width:80%; height:25px; font-size:16px" />
                        <br />
                        <br />
                        <strong> <font color="#FFFFFF" size="+1">Tipo de publicação:</font></strong> <br />
                        <select name="tipo" id="tipo">
                            <option value="1" selected>Artigos completos publicados em periódicos</option>
                            <option value="2">Artigos aceitos para publicação</option>
                            <option value="3">Livros e capítulos</option>
                            <option value="4">Texto em jornal ou revista (magazine)</option>
                            <option value="5">Trabalhos publicados em anais de eventos</option>
                            <option value="6">Apresentação de trabalho e palestra</option>
                            <option value="7">Tradução</option>
                            <option value="8">Prefácio, posfácio</option>
                            <option value="9">Outra produção bibliográfica</option>
                        </select>
                        <br />
                        <br />
                        <input type="button" class="botao2" value="<- Voltar" onClick="javascript: menu(\'e\');"  style="width:35%; height:40px" />
                        <input class="botao2" type="button" value="Avançar ->" onClick="javascript: menu(\'ev\');" style="width:35%; height:40px" />
                    </div>
                    <div id="etapaEvento" style="display:none">
                        <div class="floatMaior3" align="left"> <font color="#FFFFFF" size="+1"><strong>Vinculado ao evento:</strong></font><font color="#FFFFFF" size="1">(Sem acentos)</font> <br />
                            <input type="text" name="eve_nome" id="nome" required style="height:30px; font-size:18px" />
                            <input type="hidden" id="ev" value="" />
                            <span id="btnRetirar" style="display:none"><a href="javascript: retirar();"><img src="imagens/icon-atualizar.png" width="25" height="25" /></a></span> </div>
                        <font color="#FFFFFF">
                            <div class="floatMenor3" id="divInfoEvento"></div>
                        </font>
                        <div style="clear:both"><br />
                            <br />
                            <input type="button" class="botao2" value="<- Voltar" id="btnAutores" onClick="javascript: menu(\'t\');" style="width:35%; height:40px" />
                            <input type="submit" class="botao2" value="Enviar solicitação!" style="width:35%; height:40px" />
                        </div>
                    </div>
                </form>
            </font> </div>
    </body>
</html>
';
            break;
        }
    }
}
?>

