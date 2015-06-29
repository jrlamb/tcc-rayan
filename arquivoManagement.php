<?php

require('class/classes.class.php');
require('class/conexao.class.php');

$security = Security::getInstance();
$db = Database::getInstance();
$mysqli = $db->getConnection();
$return_arr = array();

if (!$security->isLogged()) {
    header('Location: index.php');
} else {
    $varAction = $_GET['action'];
    $usuario = $security->getUsuario();

    if (isset($varAction)) {
        $varCod;
        if (isset($_GET['cod'])) {
            $varCod = $_GET['cod'];
        }
        switch ($varAction) {

            case "lista":
                $query = $mysqli->query("SELECT `sinc_id`, `arq_nome`, `arq_titulo`, `arq_id`, `arq_dono` FROM `tb_sincronizado`, `tb_usuario`, `tb_arquivo` WHERE `usu_email` = '" . $usuario->email . "' AND `usu_id` = `sinc_usu_id` AND `sinc_arq_id` = `arq_id` AND `sinc_remover` = 0");
                print "<script type='text/javascript'>
                        function deletar(o, i){
                            if(confirm('Você tem certeza que deseja remover da SUA lista de arquivos? (Outros usuários que contenham o arquivo não serão afetados)')){				
                                $('#divResultadoInterno').load('arquivoManagement.php?action=deletar&sinc_id=' + o + '&arq_id=' + i, function(){
                                    $('#divResultadoInterno').slideToggle(400, function(){
                                        $('#divMeusarquivos').load('arquivoManagement.php?action=lista');
                                        setTimeout(function(){
                                                $('#divResultadoInterno').slideToggle(400);
                                        }, 2000);
                                    });
                                });					
                            }
                        }
                        function compartilhar(i){	
                            $('#conteudoInterno').fadeOut(300, function(){
                                $('#carregandoI').show();
                                $('#conteudoInterno').load(('arquivoManagement.php?action=compartilhar&arq_id=' + i), function(){
                                    $('#carregandoI').fadeOut(100, function(){
                                        $('#conteudoInterno').fadeIn(300);										
                                    });	
                                });	
                            });							
                        }
                        function editar(o, i){	
                            $('#conteudoInterno').fadeOut(300, function(){
                                $('#carregandoI').show();
                                $('#conteudoInterno').load(('arquivoManagement.php?action=editar&sinc_id=' + o + '&arq_id=' + i), function(){
                                    $('#carregandoI').fadeOut(100, function(){
                                        $('#conteudoInterno').fadeIn(300);										
                                    });	
                                });	
                            });							
                        }
                        function visualizar(o){	
                            $('#conteudoInterno').fadeOut(300, function(){
                                $('#carregandoI').show();
                                $('#conteudoInterno').load(('arquivoManagement.php?action=visualizar&arq_id=' + o), function(){
                                    $('#carregandoI').fadeOut(100, function(){
                                        $('#conteudoInterno').fadeIn(300);										
                                    });	
                                });	
                            });							
                        }</script>";
                print "<table width='100%' cellspacing='0'><tr class='topoTabela'><td>Título</td><td width='20'></td><td width='20'></td><td width='20'></td></tr>";
                $p = 0;
                while ($r = $query->fetch_array()) {
                    if ($p == 0) {
                        print "<tr class='par'>";
                        $p++;
                    } else {
                        print "<tr class='impar'>";
                        $p--;
                    }
                    print "<td class='separador'><a href='javascript: visualizar(\"" . $r[3] . "\");' title='Visualizar'>" . utf8_encode($r[2]) . "</a></td><td align='center'><a href='javascript: deletar(\"" . $r[0] . "\", \"" . $r[3] . "\");' title='Remover'><img border='0' src='imagens/icon-borracha.png' width='15' /></td><td align='center'><a href='javascript: compartilhar(\"" . $r[3] . "\");' title='Compartilhar com outro(s) usuários'><img border='0' src='imagens/icon-compartilhar.png' width='15' /></td>";
                    $verificaDono = $mysqli->query("SELECT COUNT(*) FROM `tb_usuario`, `tb_arquivo`, `tb_sincronizado` WHERE `arq_id` = `sinc_arq_id` AND `sinc_usu_id` = `usu_id` AND `usu_email` = '" . $usuario->email . "' AND `arq_dono` = `usu_id` AND `arq_id` = '" . $r[3] . "'");
                    $verificaDono = $verificaDono->fetch_array();
                    if ($verificaDono[0] > 0) {
                        print "<td align='center'><a href='javascript: editar(\"" . $r[0] . "\", \"" . $r[3] . "\");' title='Editar'><img border='0' src='imagens/icon-lapis.png' width='15' /></td>";
                    } else {
                        print "<td align='center'></td>";
                    }
                    print "</tr>";
                }
                print "</table>";
                break;
            case "listaXML":
                $query = $mysqli->query("SELECT `arq_nome` FROM `tb_sincronizado`, `tb_usuario`, `tb_arquivo` WHERE `usu_cod_sinc` = '" . $varCod . "' AND `usu_id` = `sinc_usu_id` AND `sinc_arq_id` = `arq_id`");
                header('Content-Type: application/xml; charset=utf-8');
                print '<usuario cod-sinc="' . $varCod . '">';
                while ($r = $query->fetch_array()) {
                    print '<arquivo nome="' . $r[0] . '" />';
                }
                print "</usuario>";
                break;
            case "visualizar":
                $id = $fisico = $titulo = $evento = $tipo = "";
                if (isset($_GET['arq_id'])) {
                    $id = $_GET['arq_id'];
                    $query = $mysqli->query("SELECT `arq_nome`, `arq_titulo`, `eve_nome`, `tparq_descricao` FROM `tb_arquivo`, `tb_evento`, `tb_tipoarquivo` WHERE `arq_eve_id` = `eve_id` AND `arq_tparq_id` = `tparq_id` AND `arq_id` = '" . $id . "';");
                    if ($query->num_rows > 0) {
                        $query = $query->fetch_array();
                        $fisico = $query[0];
                        $titulo = $query[1];
                        $evento = $query[2];
                        $tipo = $query[3];
                    }
                }
                print '<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Visualizar arquivo</title>
        <link rel="stylesheet" href="libs/style.css" type="text/css" />
		<script src="libs/jquery.js"></script>
        <script type="text/javascript">
            function aoClicar(value) {
                $("#divInfoEvento").load("eventoManagement.php?action=info&nome=" + value.split(" ").join("+"));
                $("#nome").attr("readonly", "readonly");
                //$("#btnRetirar").fadeIn(300);
                if ($("#divInfoEvento").is(":hidden"))
                    $("#divInfoEvento").fadeIn(400);
            }
            function reportar() {
                $("#formVisualizacao").slideToggle(300);
                $("#formReport").slideToggle(300);
            }
            function navegar(o) {
                $("#btA").css("background-color", "#06C");
                $("#btE").css("background-color", "#06C");
                $("#btT").css("background-color", "#06C");
                if (o == "t") {
                    $("#divEvento").fadeOut(300, function() {
                        $("#divAutores").fadeOut(300, function() {
                            $("#divTitulo").fadeIn(300);
                        });
                    });
                    $("#btT").css("background-color", "#03C");
                } else if (o == "e") {
                    $("#divTitulo").fadeOut(300, function() {
                        $("#divAutores").fadeOut(300, function() {
                            $("#divEvento").fadeIn(300);
                        });
                    });
                    $("#btE").css("background-color", "#03C");
                } else if (o == "a") {
                    $("#divTitulo").fadeOut(300, function() {
                        $("#divEvento").fadeOut(300, function() {
                            $("#divAutores").fadeIn(300);
                        });
                    });
                    $("#btA").css("background-color", "#03C");
                }
                if ($("#formVisualizacao").is(":hidden")) {
                    $("#formVisualizacao").slideToggle(300);
                    $("#formReport").hide();
                }
            }
            function enviarReport() {
                reportar();
                var obs = $("#obsReport").val();
                var tipo = $("#tipoReport").val();
                var arq_id = $("#arq_id").val();
                $("#divResultadoInterno").load("reportar.php?A&arq_id=" + arq_id + "&tipo=" + tipo + "&obs=" + obs.split(" ").join("+"));
                if ($("#divResultadoInterno").is(":hidden"))
                    $("#divResultadoInterno").slideToggle(400);
                setTimeout(function() {
                    $("#divResultadoInterno").slideToggle(400);
                }, 3000);
                $("#obsReport").val("");
            }
        </script>
    </head>
    <body>
        <br />
        <div align="center"> <font color="#FFFFFF" size="+3"><strong>V</strong>isualizar <strong>A</strong>rquivo</font><br />
            <span class="botaoMenor" id="btT"><a href="javascript: navegar(\'t\');">Título / Tipo</a></span><span id="btE" class="botaoMenor"><a href="javascript: navegar(\'e\');">Evento</a></span><span class="botaoMenor" id="btA"><a href="javascript: navegar(\'a\');">Autores</a></span><br />
            <div id="divResultadoInterno" style="display:none"></div>
            <br />
            <div align="justify" style="width:90%">
                <div align="center">
                    <div id="formVisualizacao">
                        <div id="divTitulo">
                            <input type="hidden" name="arq_id" id="arq_id" value="' . $id . '" readonly />
                            <font color="#FFFFFF" size="+1">Nome físico:</font> <br />
                            <input type="text" readonly name="arq_nome" value="' . utf8_encode($fisico) . '" readonly style="width:50%; font-size:20px; height:30px" />
                            <br />
                            <br />
                            <font color="#FFFFFF" size="+1">Título:</font> <br />
                            <input type="text" name="arq_titulo" required value="' . utf8_encode($titulo) . '" style="width:80%; font-size:18px; height:30px;"readonly />
                            <br />
                            <br />
                            <font color="#FFFFFF" size="+1">Tipo de publicação:</font> <br />
                            <input type="text" name="arq_tipo" required value="' . utf8_encode($tipo) . '" style="width:70%; font-size:18px; height:30px;"readonly />
                        </div>
                        <div id="divEvento" style="display:none">
                            <div class="floatMaior3"> <font color="#FFFFFF" size="+1">Vinculado ao evento:</font> <br />
                                <input type="text" readonly name="eve_nome" id="nome" required style="height:30px; font-size:18px" value="' . utf8_encode($evento) . '" />
                            </div>
                            <font color="#FFFFFF">
                                <div class="floatMenor3" id="divInfoEvento"></div>
                            </font> <br />
                        </div>
                        <div id="divAutores" style="display:none; width:80%">
                            <font color="#FFFFFF" size="+1">Autores:</font>
                            <div id="divAutores"><script type="text/javascript">$("#divAutores").load("arquivoManagement.php?action=listaAutores&id=' . $id . '");</script></div>
                        </div>
                        <br />
                        <br />
                        <br />
                        <input type="button" class="botao2" value="<- Voltar" onclick="javascript: menuInterno(\'m\');" style="width:30%; height:40px; font-size:15px;" />
                        <input type="button" class="botao2" value="Reportar" onclick="javascript: reportar();" style="width:30%; height:40px; font-size:15px;" />
                    </div>
                    <div id="formReport" style="display:none">
                        <input type="hidden" value="arquivo" name="tipoReport" id="tipoReport" />
                        <font color="#FFFFFF" size="+1">Obs.:</font><br />
                        <textarea id="obsReport" style="max-height:100px; max-width:80%; height:60px; width:50%"></textarea>
                        <br />
                        <br />
                        <input class="botao2" type="button" value="Cancelar" onClick="javascript: reportar();"  style="width:35%; height:40px" /><input class="botao2" type="button" value="Enviar report" onClick="javascript: enviarReport();" style="width:45%; height:40px" />

                    </div>
                </div>

            </div>
        </div>
<script type="text/javascript">aoClicar("' . utf8_encode($evento) . '");</script>
        
    </body>
</html>';
                break;
            case "editar":
                $id = "";
                $fisico = "";
                $titulo = "";
                $evento = "";
                if (isset($_GET['arq_id'])) {
                    $id = $_GET['arq_id'];
                    $query = $mysqli->query("SELECT `arq_nome`, `arq_titulo`, `eve_nome` FROM `tb_arquivo`, `tb_evento` WHERE `arq_eve_id` = `eve_id` AND `arq_id` = '" . $id . "';");
                    if ($query->num_rows > 0) {
                        $query = $query->fetch_array();
                        $fisico = $query[0];
                        $titulo = $query[1];
                        $evento = $query[2];
                    }
                }
                print '<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Editar arquivo</title>
        <link rel="stylesheet" href="libs/style.css" type="text/css" /> 
        <link rel="stylesheet" href="libs/jquery-ui.min.css" type="text/css" /> 
        <script type="text/javascript" src="libs/jquery.js"></script>
        <script type="text/javascript" src="libs/jquery-ui.min.js"></script>
        <script type="text/javascript">
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
                var thistarget = "#divResultadoInterno";
				
                $("#autor").autocomplete({
                    source: "procuraManagement.php?action=autores",
                    minLength: 2,
                    select: function(event, ui) {
                        var label = ui.item.label;
                        var value = ui.item.value;
                        var arqid = $("#arq_id").val();
                        var q = value.split(" - ");
                        $("#divResultadoInterno").load("arquivoManagement.php?action=inserirAutor&arq_id=" + arqid + "&nome=" + q[1], function() {
                            $("#divAutores").load("arquivoManagement.php?action=listaAutores&dl&id=" + arqid);
                        });
                        if ($(thistarget).is(":hidden")) {
                            $(thistarget).slideToggle(400, function() {
                                setTimeout(function() {
                                    $(thistarget).slideToggle(800);
                                }, 3000);
                            });
                        } else {
                            setTimeout(function() {
                                $(thistarget).slideToggle(800);
                            }, 3000);
                        }
                    }
                });
                $("#form").submit(function() {
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
                                        $(thistarget).slideToggle(800);
                                    }, 3000);
                                });
                            } else {
                                setTimeout(function() {
                                    $(thistarget).slideToggle(800);
                                }, 3000);
                            }
                        }
                    })
                    return false;
                }
                );
            });

            function aoClicar(value) {
                $("#divInfoEvento").load("eventoManagement.php?action=info&nome=" + value.split(" ").join("+"));
                $("#nome").attr("readonly", "readonly");
                $("#nome").css("background-color", "#FFC");
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
            }

            function navegar(o) {
                $("#btA").css("background-color", "#06C");
                $("#btE").css("background-color", "#06C");
                if (o == "t") {
                    $("#etapa2").fadeOut(300, function() {
                        $("#etapa1").fadeIn(300);
                    });
                    $("#btT").css("background-color", "#03C");
                } else if (o == "a") {
                    $("#etapa1").fadeOut(300, function() {
                        $("#etapa2").fadeIn(300);
                    });
                    $("#btA").css("background-color", "#03C");
                }
            }
        </script>
    </head>

    <body>
        <br />
        <div align="center">
            <font color="#FFFFFF" size="+3"><strong>E</strong>ditar <strong>a</strong>rquivo</font><br />
            <span class="botaoMenor" id="btT"><a href="javascript: navegar(\'t\');">Título / Tipo / Evento</a></span><span class="botaoMenor" id="btA"><a href="javascript: navegar(\'a\');">Autores</a></span><br />
            <div id="divResultadoInterno" style="display:none"></div><br />
            <div align="justify" style="width:90%">
                <form id="form" method="post" action="arquivoManagement.php?action=editarAcao">
                    <input type="hidden" name="arq_id" id="arq_id" value="' . $id . '" />
                    <div id="etapa1">
                        <font color="#FFFFFF" size="+1">Nome físico:</font> <br /><input type="text" readonly="readonly" name="arq_nome" value="' . utf8_encode($fisico) . '"  style="width:50%; font-size:20px; height:30px; background-color: #FFC" /><br /><font color="#FFCC00"><sup>O nome físico não pode ser alterado</sup></font><br />
                        <font color="#FFFFFF" size="+1">Título:</font> <br /><input type="text" name="arq_titulo" required="required" value="' . utf8_encode($titulo) . '" style="width:80%; font-size:18px; height:30px;" /><br /><br />
                        <div class="floatMaior3">
                            <font color="#FFFFFF" size="+1">Vinculado ao evento:</font> <br />
                            <input type="text" name="eve_nome" id="nome" required="required" style="height:30px; font-size:18px" value="' . utf8_encode($evento) . '" /> <span id="btnRetirar" style="display:none"><a href="javascript: retirar();"><img src="imagens/icon-atualizar.png" width="30" height="30" /></a></span>
                        </div>
                        <font color="#FFFFFF"><div class="floatMenor3" id="divInfoEvento"></div></font>      
                    </div>
                    <div id="etapa2" style="display:none">
                        <font color="#FFFFFF" size="+1">Inserir autor :</font><br /><input type="text" name="autor" id="autor" style="height:30px; font-size:18px" /><br /><br />
                        <div id="divAutores" style="width:90%"><script type="text/javascript">$("#divAutores").load("arquivoManagement.php?action=listaAutores&dl&id=' . $id . '");</script></div>
                    </div>
                    <br /><br /><br /><br />
                    <input type="button" class="botao2" value="<- Voltar" onclick="javascript: menuInterno(\'m\');" style="width:30%; height:40px; font-size:15px;" /><input class="botao2" type="submit" value="Editar!" name="submit" style="width:45%; height:40px; font-size:15px;" />
                </form>
            </div>
        </div>
		<script type="text/javascript">aoClicar("' . utf8_encode($evento) . '");</script>
    </body>
</html>';
                break;
            case "listaAutores":
                $query = $mysqli->query("SELECT `arqaut_id`, `usu_primeironome`, `usu_ultimonome`, `usu_email` FROM `tb_arquivo`, `tb_usuario`, `tb_arquivo_autores` WHERE `usu_id` = `arqaut_usu_id` AND `arq_id` = `arqaut_arq_id` AND `arq_id` = '" . $_GET['id'] . "' ORDER BY `usu_primeironome`");
                print "<script type='text/javascript'>
                        function deletar(o){
                            if(confirm('Você tem certeza que deseja remover este autor?')){				
                                $('#divResultadoInterno').load('arquivoManagement.php?action=deletarAutor&arqaut_id=' + o, function(){
                                    $('#divResultadoInterno').slideToggle(400, function(){
                                        $('#divAutores').load('arquivoManagement.php?action=listaAutores&dl&id=" . $_GET['id'] . "');
                                        setTimeout(function(){
                                            $('#divResultadoInterno').slideToggle(400);
                                        }, 2000);
                                    });
                                });					
                            }
                        }</script>";
                print "<table width='100%' cellspacing='0'><tr class='topoTabela'><td>Nome</td><td>E-mail</td><td width='20'></td></tr>";
                $p = 0;
                while ($r = $query->fetch_array()) {
                    if ($p == 0) {
                        print "<tr class='par'>";
                        $p++;
                    } else {
                        print "<tr class='impar'>";
                        $p--;
                    }
                    print "<td class='separador'>" . utf8_encode($r[1]) . " " . utf8_encode($r[2]) . "</td>";
                    print "<td class='separador'>" . utf8_encode($r[3]) . "</td>";
                    if (isset($_GET['dl'])) {
                        print "<td align='center'><a href='javascript: deletar(\"" . $r[0] . "\");' title='Remover'><img border='0' src='imagens/icon-borracha.png' width='15' /></td>";
                    } else {
                        print "<td></td>";
                    }
                    print "</tr>";
                }
                print "</table>";
                break;
            case "deletar":
                if (isset($_GET['sinc_id']) && isset($_GET['arq_id'])) {
                    $usuarioPossuiArq = $mysqli->query("SELECT * FROM `tb_sincronizado, `tb_usuario` WHERE `usu_email` = '" . $usuario->email . "' AND `sinc_usu_id` = `usu_id` AND `sinc_id` = '" . $_GET['sinc_id'] . "';");
                    if ($usuarioPossuiArq->num_rows > 0 && $mysqli->query("UPDATE `tb_sincronizado` SET `sinc_remover` = '1' WHERE `sinc_id` = '" . $_GET['sinc_id'] . "';")) {
                        echo "<div class='sucesso'>Arquivo desvinculado com sucesso!</div>";
                        $q = $mysqli->query("SELECT `sinc_usu_id` FROM `tb_sincronizado` WHERE `sinc_arq_id` = '" . $_GET['arq_id'] . "';");
                        if ($q->num_rows == 0) {
                            $mysqli->query("DELETE FROM `tb_arquivo` WHERE `arq_id` = '" . $_GET['arq_id'] . "';");
                        }
                    } else {
                        echo "<div class='erro'>Erro ao desvincular arquivo.</div>";
                    }
                }
                break;

            case "deletarAutor":
                if (isset($_GET['arqaut_id'])) {
                    if ($mysqli->query("DELETE FROM `tb_arquivo_autores` WHERE `arqaut_id` = '" . $_GET['arqaut_id'] . "';")) {
                        echo "<div class='sucesso'>Autor desvinculado com sucesso!</div>";
                    } else {
                        echo "<div class='erro'>Erro ao remover autor.</div>";
                    }
                }
                break;

            case "editarAcao":
                if (isset($_POST['arq_id'])) {
                    $queryVerificaDono = $mysqli->query("SELECT COUNT(*) FROM `tb_usuario`, `tb_arquivo`, `tb_sincronizado` WHERE `arq_id` = `sinc_arq_id` AND `sinc_usu_id` = `usu_id` AND `usu_email` = '" . $usuario->email . "' AND `arq_dono` = `usu_id` AND `arq_id` = '" . $_POST['arq_id'] . "'");
                    $queryVerificaDono = $queryVerificaDono->fetch_array();
                    $queryEvento = $mysqli->query("SELECT `eve_id` FROM `tb_evento` WHERE `eve_nome` LIKE '" . utf8_decode($_POST['eve_nome']) . "'");
                    $queryEvento = $queryEvento->fetch_array();
                    if ($queryVerificaDono[0] > 0) {
                        $t = $queryEvento[0];
                        $q = $mysqli->query("SELECT * FROM `tb_arquivo` WHERE `arq_titulo` LIKE '%" . utf8_decode($_POST['arq_titulo']) . "%' AND `arq_id` <> '" . $_POST['arq_id'] . "'");
                        if ($q->num_rows == 0) {
                            if ($mysqli->query("UPDATE `tb_arquivo` SET `arq_titulo` = '" . utf8_decode($_POST['arq_titulo']) . "', `arq_eve_id` = '" . $t . "' WHERE `arq_id` = '" . $_POST['arq_id'] . "';")) {
                                echo "<div class='sucesso'>Arquivo alterado com sucesso!</div>";
                            } else {
                                echo "<div class='erro'>Erro ao alterar arquivo.</div>";
                            }
                        } else {
                            echo "<div class='erro'>Erro, este título já existe!</div>";
                        }
                    } else {
                        echo "<div class='erro'>Erro, voce precisa ser o dono do arquivo.</div>";
                    }
                }

                break;
            case "inserirAutor":
                if (isset($_GET['arq_id']) && isset($_GET['nome'])) {
                    $id = $mysqli->query("SELECT `usu_id` FROM `tb_usuario` WHERE `usu_email` = '" . $_GET['nome'] . "'");
                    $id = $id->fetch_array();
                    $id = $id[0];
                    $q = $mysqli->query("SELECT * FROM `tb_arquivo_autores` WHERE `arqaut_arq_id` = '" . $_GET['arq_id'] . "' AND `arqaut_usu_id` = '" . $id . "'");
                    if ($q->num_rows == 0) {
                        if ($mysqli->query("INSERT INTO `tb_arquivo_autores` ( `arqaut_id` , `arqaut_usu_id` , `arqaut_arq_id` ) VALUES ( NULL , '" . $id . "', '" . $_GET['arq_id'] . "' )")) {
                            echo "<div class='sucesso'>Autor inserido com sucesso!<script>$('#autor').val('');</script></div>";
                        } else {
                            echo "<div class='erro'>Erro ao inserir autor.</div>";
                        }
                    } else {
                        echo "<div class='erro'>O autor já consta na lista.</div>";
                    }
                }
                break;
            case "compartilhar":
                $arquivo = "";
                $id = "";
                if (isset($_GET['arq_id'])) {
                    $query = $mysqli->query("SELECT `arq_titulo`, `arq_nome` FROM `tb_arquivo` WHERE `arq_id` = '" . $_GET['arq_id'] . "';");
                    $id = $_GET['arq_id'];
                    $r = $query->fetch_array();
                    $arquivo = $r[0] . ' - ' . $r[1];
                }
                print '<!DOCTYPE html>
			
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title>Compartilhar arquivos</title>
				<link rel="stylesheet" type="text/css" href="libs/style.css">
				<link rel="stylesheet" href="libs/jquery-ui.min.css" type="text/css" />
				<script type="text/javascript" src="libs/jquery.js"></script>
				<script type="text/javascript" src="libs/jquery-ui.min.js"></script>
				<script type="text/javascript">
				
					$(document).ready(function(e) {
			
                                            $("#nome").autocomplete({
                                                source: "procuraManagement.php?action=compartilhar&arq_id=' . $id . '",						
                                                minLength: 2,
                                                select: function(event, ui) {
                                                    var label = ui.item.label;
                                                    var value = ui.item.value;
                                                    //var separa = value.split("-");
                                                    var exists = 0 != $("#emails option[value=\'"+value+"\']").length;
                                                    if(!exists){
                                                        $("#emails").append("<option value=\'"+value+"\'>"+value+"</option>");
                                                    } else {
                                                        if ($("#divResultadoInterno").is(":hidden")) {
                                                            $("#divResultadoInterno").slideToggle(300, function() {
                                                                $("#divResultadoInterno").html("<div class=\'erro\'>Esta pessoa já foi inserida!</div>");
                                                                setTimeout(function() {
                                                                    $("#divResultadoInterno").slideToggle(300);
                                                                }, 2000);
                                                            });
                                                        }
                                                    }
                                                }
                                            });
                                            $("#nome").mouseover(function(e) {
                                                if ($("#nome").val().indexOf("@") > 0) {
                                                    $("#nome").val("");
                                                }
                                            });
					});
					
					$("#formCompartilhar").submit(function() {
						var thistarget = "#divResultadoInterno";						
						$( "#emails option" ).each(function() {
						  $(this).prop("selected",true);
						});
						if($("#emails option").size() < 1){
                                                    $(thistarget).fadeIn(500, function(){$(thistarget).html(\'<div class="erro">Ao menos uma pessoa deve ser selecionada.</div>\');});
                                                    setTimeout(function() {
                                                        $(thistarget).slideToggle(800);
						   }, 3000);
                                                    return false;
						}
						jQuery.ajax({
						   data: $(this).serialize(),
						   url: this.action,
						   type: this.method,
						   success: function(results) {
								$(thistarget).html(results)
								if ($(thistarget).is(":hidden")) {
									$(thistarget).slideToggle(400, function() {
										setTimeout(function() {
										   $(thistarget).slideToggle(800);
									   }, 3000);
									});
								} else {
									setTimeout(function() {
									   $(thistarget).slideToggle(800);
								   }, 3000);
								}
							}
						});
						return false;
					}
					);
					function convidar(o) {
						$("#conteudoInterno").fadeOut(300, function() {
							$("#conteudoInterno").load("convidarManagement.php?action=convidar2&arq_id=" + o, function() {
								$("#conteudoInterno").fadeIn(300);
							});
						});
					}
					function removerItem(){
						$("#emails option:selected").remove();
					}
				</script>
			</head>
			
			<body text="#FFFFFF">
				<div align="center"><br />
					<font size="+3" color="#FFFFFF"><strong>C</strong>ompartilhar <strong>A</strong>rquivo</font><br />
					<br />
					<div id="divResultadoInterno" style="display:none"></div>
					<div align="center" style="width:80%"> <font color="#FFFFFF"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Informe o email do usuário cujo qual você deseja compartilhar o arquivo:<br />
						<font color="#333333" size="2">' . utf8_encode($arquivo) . '</font><br />
						<sub>(Caso a pessoa que você quer solicitar não possua registro no sistema, <strong><a href="javascript: convidar(' . $id . ');">clique aqui</a></strong>)</sub><br />
						<br />
						<br />
						<form action="arquivoManagement.php?action=compartilharAcao" method="post" id="formCompartilhar">
							<input type="hidden" name="arq_id" value="' . $id . '" />
							Nome Completo:<br />
							<input type="text" name="email" id="nome" style="width:65%">
							<br /><br />
							<input class="botao2" type="button" value="Remover selecionados" name="remover" style="width:45%; height:26px; font-size:12px;" onclick="javascript: removerItem();" /><br />
							<select name="emails" id="emails" style="width:70%; height:80px; max-width:100%; max-height:100px; min-height:80px; min-width:70%" multiple readonly></select>
							<br />
							<br />
							<input class="botao2" type="submit" value="Compartilhar!" name="submit" style="width:45%; height:40px; font-size:15px;" />
							<input class="botao2" type="button" value="Cancelar" onclick="' . ((!isset($_GET['eve_id'])) ? 'javascript: menuInterno(\'m\');' : 'javascript: $(\'#conteudoInterno\').load(\'mostrararquivosevento.php?A&eve_id=' . $_GET['eve_id'] . '\');'
                        ) . '" style="width:30%; height:40px; font-size:15px;" />
											<input class="botao2" type="button" value="Limpar!" onclick="javascript: document.getElementById(\'emails\').value = \'\';" style="width:15%; height:40px; font-size:15px;" />
										</form>
										</font> </div>
								</div>
							</body>
							</html>';
                break;
            case "compartilharAcao":
                $id = $mysqli->query("SELECT `usu_id` FROM `tb_usuario` WHERE `usu_email` = '" . $usuario->email . "'");
                $id = $id->fetch_array();
                $id = $id[0];

                $emails = $_POST['emails'];
                $arq_id = utf8_decode($_POST['arq_id']);
                $q = $mysqli->query("SELECT `arq_nome`, `arq_titulo`, `tparq_descricao`, `eve_nome` FROM `tb_arquivo`, `tb_evento`, `tb_tipoarquivo` WHERE `eve_id` = `arq_eve_id` AND `arq_tparq_id` = `tparq_id` AND `arq_id` = '" . $arq_id . "'");
                $q = $q->fetch_array();
                $arq_nome = $q[0];
                $arq_titulo = $q[1];
                $arq_tipo = $q[2];
                $eve_nome = $q[3];
                $qEmails = explode(',', $emails);
                $sucess = true;
                $erros = 0;
                for ($z = 0; $z < count($qEmails); $z++) {
                    $linha = explode(' - ', $qEmails[$z]);
                    if (count($linha) > 0) {
                        $email = $linha[1];
                        $uId = $mysqli->query("SELECT `usu_id` FROM `tb_usuario` WHERE `usu_email` = '" . $email . "'");
                        $uId = $uId->fetch_array();
                        $uId = $uId[0];
                        $tm = $mysqli->query("SELECT * FROM `tb_solicitacao` WHERE `sol_arq_id` = '" . $arq_id . "' AND `sol_usu_id_solicitante` = '" . $uId . "'");
                        if ($tm->num_rows == 0) {

                            if ($mysqli->query("INSERT INTO `tb_solicitacao` ( `sol_id` , `sol_usu_id_solicitante` , `sol_usu_id_solicitado` , `sol_arq_id` , `sol_dtpedido` , `sol_dtatendido` , `sol_atendido` , `sol_permitido` ) VALUES ( NULL , '" . $uId . "', '" . $id . "', '" . $arq_id . "', CURRENT_TIMESTAMP , CURRENT_TIMESTAMP , '0', '1');")) {
                                $mysqli->query("INSERT INTO `tb_mensagem` (`men_solicitante` , `men_solicitado` , `men_obj_id` , `men_tipo` , `men_obs` , `men_data` , `men_lida` , `men_removida` ) VALUES ('" . $id . "', '" . $uId . "', '" . $arq_id . "', 'compartilhar', '', NOW(), '0', '0' );");
                                $headers = "MIME-Version: 1.0\n";
                                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
                                $headers .= "From: \"Sistema de compartilhamento de Arquivos\" <curriculum@curriculum.info.md.utfpr.edu.br>\r\n";

                                $corpo = "<html><body>
                                        <h3>Ol&aacute;,</h3><br />
                                        <div align='justify' style='width:80%'><font size='+1'>através do sistema de arquivos acadêmicos, informamos que : " . $usuario->primeiroNome . " " . $usuario->ultimoNome . " <em>(" . $usuario->email . ")</em> compartilhou um arquivo com você!
                                        <br /><br />
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
                                        </table><br /><br />
                                        </font></div></body></html>\r\n";
                                if (!mail($email, utf8_decode("Há um arquivo compartilhado com você!"), utf8_decode($corpo), $headers)) {
                                    $sucess = false;
                                }
                            }
                        } else {
                            $erros++;
                        }
                    } else {
                        $sucess = false;
                    }
                }
                if ($sucess) {
                    if ($erros == 0) {
                        print '<font color="#FFFFFF"><div class="sucesso">O arquivo foi compartilhado com sucesso!</div></font><script>setTimout(function(){menuInterno("m");}, 2000);</script>';
                    } else {
                        print '<font color="#FFFFFF"><div class="erro">Erro ao compartilhar arquivo. Esta pessoa já possui o arquivo.</div></font>';
                    }
                } else {
                    print '<font color="#FFFFFF"><div class="erro">Erro ao compartilhar arquivo.</div></font>';
                }
                break;

            case "enviar":
                print '<!DOCTYPE html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Enviar arquivo</title>
    <link rel="stylesheet" type="text/css" href="libs/style.css">
    <link rel="stylesheet" type="text/css" href="libs/uploadfileenviar.css" />
    <script type="text/javascript" src="libs/jquery.min.fup.js"></script>
	<script type="text/javascript" src="libs/jquery.uploadfile_enviar.js"></script>
    <script type="text/javascript" src="libs/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="libs/jquery-ui.min.css" type="text/css" /> 
    <script type="text/javascript">
      
	  	
        function enviar() {
            uploadObj.startUpload();
            $("#divForm").fadeOut(200);
            $("#carregando").fadeIn(200);
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
        function alertar(msg) {
            $("#divResultadoInterno").html(msg);
            if ($("#divResultadoInterno").is(":hidden")) {
                $("#divResultadoInterno").slideToggle(300, function() {
                    setTimeout(function() {
                        if (!$("#divResultadoInterno").is(":hidden"))
                            $("#divResultadoInterno").slideToggle(300);
                    }, 2000)
                });
            } else {
                $("#divResultadoInterno").fadeOut(300);
            }
        }
        function menu(o) {
            if (o == "e") {
                if ($("#titulo").val().length < 5) {
                    alertar("<div class=\'erro\'>O Título deve conter ao menos 5 caracteres.</div>");
                } else if($("#tituloArq").val().length <= 4){
                    alertar("<div class=\'erro\'>Um arquivo deve ser carregado.</div>");					
				} else {
                    $("#etapaTitulo").fadeOut(300, function() {
                        $("#etapaAutores").fadeOut(300, function() {
                            $("#etapaEvento").fadeIn(300);
                        });
                    });
                }
            } else if (o == "t") {
                $("#etapaEvento").fadeOut(300, function() {
                    $("#etapaTitulo").fadeIn(300);
                });
            } else if (o == "a") {
                if ($("#ev").val().length > 0) {
                    $("#etapaEvento").fadeOut(300, function() {
                        $("#etapaAutores").fadeIn(300);
                    });
                } else {
                    alertar("<div class=\'erro\'>Um evento deve ser selecionado</div>");
                }
            }
        }
        $(document).ready(function(e) {
            $("#nomeautor").autocomplete({
                source: "procuraManagement.php?action=autores",
                minLength: 2,
                select: function(event, ui) {
                    var label = ui.item.label;
                    var value = ui.item.value;
                    //var separa = value.split("-");
                    var exists = 0 != $("#emails option[value=\'"+value+"\']").length;
                    if(!exists){
                        $("#emails").append("<option value=\'"+value+"\'>"+value+"</option>");
                    } else {
                        if ($("#divResultadoInterno").is(":hidden")) {
                            $("#divResultadoInterno").slideToggle(300, function() {
                                $("#divResultadoInterno").html("<div class=\'erro\'>Esta pessoa já foi inserida!</div>");
                                setTimeout(function() {
                                    $("#divResultadoInterno").slideToggle(300);
                               }, 2000);
                            });
                        }
                    }
                }
            });
            $("#nomeautor").mouseover(function(e) {
                if ($("#nomeautor").val().indexOf("@") > 0) {
                    $("#nomeautor").val("");
                }
            });
			$("#nome").autocomplete({
                    source: "procuraManagement.php?action=evento",
                    minLength: 2,
                    select: function(event, ui) {
                        var label = ui.item.label;
                        var value = ui.item.value;
                        aoClicar(value);
                    }
                });
            $("#formEnviar").submit(function() {
                var thistarget = "#conteudoInterno";
				$( "#emails option" ).each(function() {
				  $(this).prop("selected",true);
				});
				if($("#emails option").size() < 1){
					$(thistarget).fadeIn(500, function(){$(thistarget).html(\'<div class="erro">Ao menos uma pessoa deve ser selecionada.</div>\');});
					setTimeout(function() {
					   $(thistarget).slideToggle(800);
				   }, 3000);
					return false;
				}
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
                                        $(thistarget).slideToggle(400);
                                    }
                                }, 3000);
                            });
                        }
                    }
                })
                return false;
            }
            );
        });

        var uploadObj = $("#fileuploader").uploadFile({
            url: "arquivoManagement.php?action=enviarAcao",
            multiple: false,
            autoSubmit: false,
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

    </script>

</head>



<body text="#FFFFFF">
    <div align="center"><br />
        <font size="+3" color="#FFFFFF"><strong>E</strong>nviar <strong>A</strong>rquivo</font><br />
        <br />
        <div id="divResultadoInterno" style="display:none"></div>
        <div align="justify" style="width:90%">
            <font color="#FFFFFF">
            <div id="divForm" align="center">
                <form method="post" action="arquivoManagement.php?action=enviarResultado" id="formEnviar">
                    <input type="hidden" name="arq_nome" id="arq_nome" value="" />
                    <input type="hidden" name="tituloArq" id="tituloArq" />
                    <div id="etapaTitulo">
                        <div id="fileuploader" style="height:60px" align="center">Upload</div>
                        <br />
                        <strong> <font color="#FFFFFF" size="+1">Título:</font></strong> <br />
                        <input type="text" name="titulo" id="titulo" style="width:100%; height:25px; font-size:16px" />
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
                        <input type="button" class="botao2" value="Avançar ->" id="btnEvento" onClick="javascript: menu(\'e\');" />
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
                            <input type="button" class="botao2" value="<- Voltar" id="btnAutores" onClick="javascript: menu(\'t\');" />
                            <input type="button" class="botao2" value="Avançar ->" id="btnAutores" onClick="javascript: menu(\'a\');" />
                        </div>
                    </div>
                    <div id="etapaAutores" style="display:none">
                        <div align="center" style="width:90%">Nome Completo:<br />
                            <input type="text" name="email" id="nomeautor" style="width:65%">
                            <br />
                            <br />
                            <input class="botao2" type="button" value="Remover selecionados" name="remover" style="width:45%; height:26px; font-size:12px;" onclick="javascript: removerItem();" /><br />
							<select name="emails" id="emails" style="width:70%; height:80px; max-width:100%; max-height:100px; min-height:80px; min-width:70%" multiple readonly></select>
                            <br />
                            <div style="clear:both">
                                <br /><br />
                                <input type="button" class="botao2" value="<- Voltar" id="btnEvento" onClick="javascript: menu(\'e\');" />          
                                <input type="button" class="botao2" value="Enviar!" id="btnEnviar" onClick="javascript: enviar();" />
                            </div>
                            </font> </div>
                    </div>
            </div>
        </div>
        <br />
        <br />
    </form>
</div>
<div id="carregando" style="display:none"><p align="center"><img src="imagens/carregando.gif" width="80" /><br /><font color="#FFFFFF">Processando...</font></p></div>
</font>
</div>
</div>
</body>
</html>';
                break;
            case "enviarAcao":
                $output_dir = "temp/Conta/";
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
            case "enviarResultado":
                $id = $mysqli->query("SELECT `usu_id` FROM `tb_usuario` WHERE `usu_email` = '" . $usuario->email . "'");
				$id = $id->fetch_array();
				$id = $id[0];
                $arq_nome = utf8_decode($_POST['arq_nome']);
                $titulo = utf8_decode($_POST['titulo']);
                $tipo = $_POST['tipo'];
                $eve_nome = utf8_decode($_POST['eve_nome']);
                $emails = utf8_decode($_POST['emails']);
                $qEmails = explode(', ', $emails);
                $eve_id = $mysqli->query("SELECT `eve_id` FROM `tb_evento` WHERE `eve_nome` LIKE '" . $eve_nome . "'");
                $eve_id = $eve_id->fetch_array();
                $eve_id = $eve_id[0];

                print '<font color="#FFFFFF"><div align="center">
                        <br />
                        <font size="+3" color="#FFFFFF"><strong>E</strong>nviar <strong>A</strong>rquivo</font><br />
                        <br /><div align="justify" style="width:80%">';

			
                if ($mysqli->query("INSERT INTO `tb_arquivo`( `arq_id` , `arq_nome` , `arq_titulo` , `arq_tparq_id` , `arq_necessitaatualizar` , `arq_dono` , `arq_eve_id` ) VALUES ( NULL, '" . $arq_nome . "', '" . $titulo . "', " . $tipo . ", '0', '" . $id . "', '" . $eve_id . "' )")) {
                    $idArq = $mysqli->query("SELECT `arq_id` FROM `tb_arquivo` WHERE `arq_nome` = '" . $arq_nome . "'");
                    $idArq = $idArq->fetch_array();
                    $idArq = $idArq[0];
                    $mysqli->query("INSERT INTO `tb_sincronizado` (`sinc_usu_id` , `sinc_arq_id` , `sinc_remover` , `sinc_data` ) VALUES ( '" . $id . "', '" . $idArq . "', '0', NOW());");
                    $mysqli->query("INSERT INTO `tb_arquivo_autores` ( `arqaut_id` , `arqaut_usu_id` , `arqaut_arq_id` ) VALUES ( NULL , '" . $id . "', '" . $idArq . "' )");
                    if ($emails != "") {
                        for ($z = 0; $z < count($qEmails); $z++) {
                            $linha = explode(' - ', $qEmails[$z]);
                            $email = $linha[1];
                            $uId = $mysqli->query("SELECT `usu_id` FROM `tb_usuario` WHERE `usu_email` = '" . $email . "'");
                            $uId = $uId->fetch_array();
                            $uId = $uId[0];
                            $mysqli->query("INSERT INTO `tb_arquivo_autores` ( `arqaut_id` , `arqaut_usu_id` , `arqaut_arq_id` ) VALUES ( NULL , '" . $uId . "', '" . $idArq . "' )");
                        }
                    }
                    print '<p align="center">O arquivo foi enviado com sucesso, e ficou nomeado como: <em>' . utf8_encode($arq_nome) . '</em>, pertencendo ao evento: <strong>' . utf8_encode($eve_nome) . '</strong><br /><br /><img src="imagens/icon-ok.png" width="80" /></p>';
                } else {
                    print '<p align="center"><font color="red" size="+1">Erro ao enviar o arquivo.</font><br /><br /><img src="imagens/icon-faltando.png" width="80" /></p>';
                    if (file_exists($arq_nome)) {
                        unlink($arq_nome);
                    }
                }
                print '</div></div></font>';
                break;
            default:
                print '<!DOCTYPE html>
                        <head>
                            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                            <title>Meus arquivos</title>
                            <link rel="stylesheet" type="text/css" href="libs/style.css">
                        </head>
				
                        <body text="#FFFFFF">
                            <div align="center"><br />
                                <font size="+3" color="#FFFFFF"><strong>M</strong>eus <strong>A</strong>rquivos</font><br /><br />
                                <div id="divResultadoInterno" style="display:none"></div>
                                <br />
                                <div align="justify" style="width:80%">
                                    <font color="#FFFFFF">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nesta seção, estão listados os artigos e certificados sincronizados fisicamente no computador. Através do ícone na coluna ao lado de cada documento, é possível compartilhar com os outros autores <em>(ou realizar o convite, caso o mesmo não possua registro de acesso no sistema)</em>.
                                    </font>
                                    <br /><br />
                                    <div id="divMeusarquivos"><script type="text/javascript">$("#divMeusarquivos").load("arquivoManagement.php?action=lista");</script></div>
                                </div>
                            </div>
                        </body>
                        </html>';
                break;
        }
    }
}
?>
