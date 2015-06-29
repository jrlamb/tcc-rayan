<?php

require('class/classes.class.php');
require('class/conexao.class.php');

$security = Security::getInstance();
$db = Database::getInstance();
$mysqli = $db->getConnection();
$return_arr = array();
function formata_data($dt) {
    $dia = substr($dt, 0, 2);
    $mes = substr($dt, 3, 2);
    $ano = substr($dt, 6, 4);
    $data = $ano . "-" . $mes . "-" . $dia;
    return $data;
}
if (!$security->isLogged()) {
    header('Location: index.php');
}else{
    $varAction = $_GET['action'];
    $usuario = $security->getUsuario();

    if (isset($varAction)) {
		
        switch ($varAction) {
            case "arquivosEvento":
                $query = $mysqli->query("SELECT `eve_nome` FROM `tb_evento` WHERE `eve_id` = " . $_GET['eve_id']);
                $query = $query->fetch_array();
                print '<!DOCTYPE html>
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                    <title>Eventos</title>
                    <link rel="stylesheet" type="text/css" href="libs/style.css">
                </head>

                <body text="#FFFFFF">
                    <div align="center"><br />
                        <font size="+3" color="#FFFFFF"><strong>A</strong>rquivos do <strong>E</strong>vento</font><br /><font color="#333333">'. utf8_encode($query[0]).'</font><br />
                        <div id="divResultadoInterno" style="display:none"></div>
                        <br />
                        <div align="justify" style="width:80%">
                            <br /><br />
                            <div id="divMeusEventos"><script type="text/javascript">$("#divMeusEventos").load(\'eventoManagement.php?action=listaArquivosEvento&eve_id=' . $_GET['eve_id'].'\');</script></div>
                        </div>
                    </div>
                </body>
                </html>';
            break;
            case "lista":
               $resultado = $mysqli->query("SELECT DISTINCT `eve_id`, `eve_nome`, `eve_dono`, `usu_id` FROM `tb_evento`, `tb_usuario`, `tb_arquivo`, `tb_sincronizado` WHERE `usu_email` = '" . $usuario->email . "' AND `usu_id` = `sinc_usu_id` AND `sinc_arq_id` = `arq_id` AND `sinc_remover` = 0 AND `eve_id` = `arq_eve_id`
UNION
SELECT DISTINCT `eve_id`, `eve_nome`, `eve_dono`, `usu_id` FROM `tb_evento`, `tb_usuario` WHERE `usu_email` = '" . $usuario->email . "' AND `eve_dono` = `usu_id`");
                print "<script type='text/javascript'>
                    function mostrarArquivos(o){	
                        $('#conteudoInterno').fadeOut(300, function(){
                            $('#carregandoI').show();
                            $('#conteudoInterno').load(('eventoManagement.php?action=arquivosEvento&eve_id=' + o), function(){
                                $('#carregandoI').fadeOut(100, function(){
                                    $('#conteudoInterno').fadeIn(300);										
                                });	
                            });	
                        });							
                    }
                    function editar(o){	
                        $('#conteudoInterno').fadeOut(300, function(){
                            $('#carregandoI').show();
                            $('#conteudoInterno').load(('eventoManagement.php?action=editarEvento&eve_id=' + o), function(){
                                $('#carregandoI').fadeOut(100, function(){
                                    $('#conteudoInterno').fadeIn(300);										
                                });	
                            });	
                        });							
                    }
                    function visualizar(o){	
                    $('#conteudoInterno').fadeOut(300, function(){				
                        $('#carregandoI').show();
                        $('#conteudoInterno').load(('eventoManagement.php?action=visualizar&eve_id=' + o), function(){
                            $('#carregandoI').fadeOut(100, function(){
                                $('#conteudoInterno').fadeIn(300);										
                            });
                        });	
                    });							
                    }</script>";
                    print "<table width='100%' cellspacing='0'><tr class='topoTabela'><td>Nome do evento</td><td width='20'></td><td width='20'></td></tr>";
                    $p = 0;
                    while ($r = $resultado->fetch_array()) {
                        if ($p == 0) {
                            print "<tr class='par'>";
                            $p++;
                        } else {
                            print "<tr class='impar'>";
                            $p--;
                        }
                        print "<td class='separador'><a href='javascript: visualizar(\"" . $r[0] . "\");'>" . utf8_encode($r[1]) . "</a></td><td align='center'><a href='javascript: mostrarArquivos(\"" . $r[0] . "\");' title='Mostrar Arquivos'><img border='0' src='imagens/icon-glass.png' width='15' /></td>";
                        if ($r[2] == $r[3]) {
                            print "<td align='center'><a href='javascript: editar(\"" . $r[0] . "\");' title='Editar'><img border='0' src='imagens/icon-lapis.png' width='15' /></td>";
                        } else {
                            print "<td width='20'></td>";
                        }
                        print "</tr>";
                    }
                    print "</table>";
                break;
            case "listaArquivosEvento":
                $resultado = $mysqli->query("SELECT `arq_id`, `arq_nome` FROM `tb_sincronizado`, `tb_usuario`, `tb_arquivo` WHERE `usu_email` = '" . $usuario->email . "' AND `usu_id` = `sinc_usu_id` AND `sinc_arq_id` = `arq_id` AND `sinc_remover` = 0 AND `arq_eve_id` = " . $_GET['eve_id']);
                print "<script type='text/javascript'>
                    function compartilhar(b, o){	
                        $('#conteudoInterno').fadeOut(300, function(){
                            $('#carregandoI').show();
                            $('#conteudoInterno').load(('arquivoManagement.php?action=compartilhar&arq_id=' + o + '&eve_id=' + b), function(){
                                $('#carregandoI').fadeOut(100, function(){
                                    $('#conteudoInterno').fadeIn(300);										
                                });
                            });	
                        });						
                    }</script>";
                print "<table width='100%' cellspacing='0'><tr class='topoTabela'><td>Nome do arquivo</td><td width='20'></td></tr>";
                $p = 0;
                while ($r = $resultado->fetch_array()) {
                    if ($p == 0) {
                        print "<tr class='par'>";
                        $p++;
                    } else {
                        print "<tr class='impar'>";
                        $p--;
                    }
                    print "<td class='separador'>" . utf8_encode($r[1]) . "</td><td align='center'><a href='javascript: compartilhar(\"" . $_GET['eve_id'] . "\", \"" . $r[0] . "\");' title='Mostrar Arquivos'><img border='0' src='imagens/icon-compartilhar.png' width='15' /></a></td>";
                    print "</tr>";
                }
                print "</table><br /><p align='center'><button class='botao2' onclick='javascript: menuInterno(\"ev\");' style='width:60%; height:35px'><- Voltar</button></p>";
                    break;
                case "editarEvento":
                    $id = $_GET['eve_id'];

                    $nome = $local = $dtini = $dtfin = $cidid = $estid = '';
                    $query = $mysqli->query("SELECT `eve_nome`, `eve_local`, DATE_FORMAT(`eve_dtinicial`, '%Y-%m-%d'), DATE_FORMAT(`eve_dtfinal`, '%Y-%m-%d'), `eve_cid_id`, `est_id` FROM `tb_evento`, `tb_cidade`, `tb_estado` WHERE `eve_cid_id` = `cid_id` AND `cid_est_id` = `est_id` AND `eve_id` = '" . $id . "'");

                    if ($query->num_rows > 0) {
                        $query = $query->fetch_array();
                        $nome = $query[0];
                        $local = $query[1];
                        $dtini = $query[2];
                        $dtfin = $query[3];
                        $cidid = $query[4];
                        $estid = $query[5];
                    }
                    print '<!DOCTYPE html>
                            <head>
                                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                                <title>Criar evento</title>
                                <link rel="stylesheet" type="text/css" href="libs/style.css">
                                <link rel="stylesheet" href="libs/jquery-ui.min.css" />
                                <script src="libs/jquery.js"></script>
                                <script src="jquery.mask.js"></script>
                                <script type="text/javascript">

                                $(document).ready(function(e) {
                                    $("#estado").change(function() {
                                        if ($(this).val()) {
                                            $.getJSON("procuraManagement.php?action=cidades&term=" + $(this).val(), {ajax: "true"}, function(j) {
                                                var options = "";
                                                for (var i = 0; i < j.length; i++) {
                                                    if (j[i].cid_nome != null)
                                                        options += \'<option value="\' + j[i].cid_id + \'">\' + j[i].cid_nome + \'</option>\';
                                                }
                                                $(\'#cidade\').html(options);
                                            });
                                        } else {
                                            $(\'#cidade\').html(\'<option value="">-- Escolha um estado --</option>\');
                                        }
                                    });
                                });

                                function cancelar(o) {
                                    if (o == null)
                                            menuInterno("ev");
                                }

                                jQuery(function($) {
                                    $("#dtinicio").val("'.$dtini.'");
                                    $("#dtfinal").val("'.$dtfin.'");
                                });
                                $("#formEditarEvento").submit(function() {
                                    var thistarget = "#divResultadoInterno";
                                    jQuery.ajax({
                                        data: $(this).serialize(),
                                        url: this.action,
                                        type: this.method,
                                        success: function(results) {
                                            $(thistarget).html(results)
                                            if ($(thistarget).is(":hidden")) {
                                                $(thistarget).slideToggle(400,function(){
                                                    setTimeout(function() {
                                                        $(thistarget).slideToggle(800);
                                                    }, 3000);
                                                });
                                            }
                                        }
                                    })
                                    return false;
                                });
                                </script>
                            </head>
				
				<body text="#FFFFFF">
                                    <div align="center"><br />
                                        <font size="+3" color="#FFFFFF"><strong>E</strong>ditar <strong>E</strong>vento</font><br />
                                        <br />
                                        <div id="divResultadoInterno" style="display:none"></div>
                                        <div align="justify" style="width:80%"> <font color="#FFFFFF">
                                            <div align="center">
                                                <form id="formEditarEvento" method="post" action="eventoManagement.php?action=editarEventoAcao">
                                                    <input type="hidden" name="eve_id" value="'.$_GET["eve_id"].'" />
                                                    <font size="+1"><strong>Nome do evento:</strong></font><br />
                                                    <input type="text" required name="nome" style="width:100%; font-size:16px; height:20px" value="'.utf8_encode($nome).'" />
                                                    <br />
                                                    <br />
                                                    <font size="+1"><strong>Local:</strong></font><br />
                                                    <input type="text" required name="local" style="width:70%;font-size:16px; height:20px" value="'.utf8_encode($local).'" />
                                                    <br />
                                                    <br />
                                                    <div style="width:90%">
                                                        <div class="float" align="left"><font size="+1"><strong>Data inicio:</strong></font><br />
                                                            <input type="date" pattern="\d{1,2}/\d{1,2}/\d{4}" name="dtinicio" id="dtinicio" style="font-size:16px; height:20px" required value="'.$dtini.'" />
                                                        </div>
                                                        <div class="float" align="left"><font size="+1"><strong>Data encerramento:</strong></font><br />
                                                            <input type="date" pattern="dd/MM/yyyy" name="dtfinal" id="dtfinal" style="font-size:16px; height:20px" required value="'.$dtfin.'" />
                                                        </div>
                                                    </div>
                                                    <br />
                                                    <div style="width:100%"><br />
                                                        <br />
                                                        <div align="left" style="float:left; width:30%"><font size="+1"><strong>Estado:</strong></font><br />
                                                            <select name="estado" id="estado" style="height:25px; font-size:16px" required>
                                                                <option selected disabled value=" "> </option>';

                                                                $query = $mysqli->query("SELECT `est_nome`, `est_id` FROM `tb_estado`");
                                                                while ($q = $query->fetch_array()) {
                                                                    if ($q[1] == $estid){
                                                                        print '<option value="' . $q[1] . '" selected>' . utf8_encode($q[0]) . '</option>';
                                                                    }else{
                                                                        print '<option value="' . $q[1] . '">' . utf8_encode($q[0]) . '</option>';
                                                                    }
                                                                }
                                                                print '
                                                            </select>
                                                        </div>
                                                        <div style="float:right; width:55%" align="left"><font size="+1"><strong>Cidade:</strong></font><br />
                                                                <select name="cidade" id="cidade" style="height:25px; font-size:16px" required>';
                                                                        $queryCidade = $mysqli->query("SELECT `cid_id`, `cid_nome` FROM `tb_cidade` WHERE `cid_est_id` = " . $estid);
                                                                        while ($cid = $queryCidade->fetch_array()) {
                                                                            if ($cid[0] == $cidid) {
                                                                                print '<option value="' . $cid[0] . '" selected>' . utf8_encode($cid[1]) . '</option>';
                                                                            } else {
                                                                                print '<option value="' . $cid[0] . '">' . utf8_encode($cid[1]) . '</option>';
                                                                            }
                                                                        }
                                                                        print '
                                                                </select>
                                                        </div>
                                                    </div>
                                                    <br />
                                                    <br />
                                                    <br />
                                                    <div align="center">
                                                        <input type="submit" class="botao2" value="Editar evento" style="width:45%; height:40px" />
                                                        <input class="botao2" type="button" value="Cancelar" onClick="javascript: cancelar();" style="width:35%; height:40px" />
                                                    </div>
                                                </form>
                                            </div>
                                            </font>
                                        </div>
                                    </div>
				</body>
				</html>';
                    break;
                case "editarEventoAcao":
                    if (isset($_POST['eve_id']) && isset($_POST['nome']) && isset($_POST['local']) 
                            && isset($_POST['dtinicio']) && isset($_POST['dtfinal']) && isset($_POST['cidade'])) {
                        $nome = $mysqli->real_escape_string($_POST['nome']);
                        $local = $mysqli->real_escape_string($_POST['local']);
                        $dtinicio = $mysqli->real_escape_string($_POST['dtinicio']);
                        $dtfinal = $mysqli->real_escape_string($_POST['dtfinal']);
                        $cidade = $mysqli->real_escape_string($_POST['cidade']);
                        if (new DateTime($dtfinal) >= new DateTime($dtinicio)) {
                            $q = $mysqli->query("SELECT * FROM `tb_evento` WHERE `eve_nome` LIKE '%" . utf8_decode($nome) . "%' AND `eve_id` <> '" . $_POST['eve_id'] . "'");
                            if ($q->num_rows == 0) {
                                if ($mysqli->query("UPDATE `tb_evento` SET `eve_nome` = '" . utf8_decode($nome) . "', `eve_local` = '" . utf8_decode($local) . "', `eve_dtinicial` = '" . $dtinicio . "', `eve_dtfinal` = '" . $dtfinal . "', `eve_cid_id` = '" . $cidade . "' WHERE `eve_id` = '" . $_POST['eve_id'] . "'")) {
                                    echo "<div class='sucesso'>Evento alterado com sucesso! <script>setTimeout(function(){cancelar()}, 3000);</script></div>";
                                } else {
                                    echo "<div class='erro'>Erro ao alterar evento.</div>";
                                }
                            } else {
                                echo "<div class='erro'>Erro, o nome que você escolheu já existe.</div>";
                            }
                        } else {
                            echo "<div class='erro'>A data final não pode terminar antes da inicial.</div>";
                        }
                    }	
                    break;
                case "criarEvento":
                    print '<!DOCTYPE html>
                                <head>
                                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                                        <title>Criar evento</title>
                                        <link rel="stylesheet" type="text/css" href="libs/style.css">
                                        <link rel="stylesheet" href="libs/jquery-ui.min.css" />
                                        <script src="libs/jquery.js" type="text/javascript"></script>
                                        <script src="jquery.mask.js"></script>
                                        <script type="text/javascript">

                                                $(document).ready(function(e) {
                                                        $("#estado").change(function() {
                                                                if ($(this).val()) {
                                                                        $.getJSON("procuraManagement.php?action=cidades&term=" + $(this).val(), {ajax: "true"}, function(j) {
                                                                                var options = "";
                                                                                for (var i = 0; i < j.length; i++) {
                                                                                        if (j[i].cid_nome != null)
                                                                                                options += \'<option value="\' + j[i].cid_id + \'">\' + j[i].cid_nome + \'</option>\';
                                                                                }
                                                                                $(\'#cidade\').html(options);
                                                                        });
                                                                } else {
                                                                        $(\'#cidade\').html(\'<option value="">-- Escolha um estado --</option>\');
                                                                }
                                                        });
                                                });
                                                function cancelar() {
                                                        menuInterno(\'ev\');
                                                }

                                                /*$(document).ready(function(e) {
                                                        $(\"#dtinicio\").mask(\"99/99/9999");
                                                        $(\"#dtfinal\").mask(\"99/99/9999");
                                                });*/

                                                $("#formCriarEvento").submit(function() {
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

                                        </script>
                                </head>


                                <body text="#FFFFFF">
                                        <div align="center"><br />
                                                <font size="+3" color="#FFFFFF"><strong>C</strong>riar <strong>E</strong>vento</font><br />
                                                <br />
                                                <div id="divResultadoInterno" style="display:none"></div>
                                                <div align="justify" style="width:80%"> <font color="#FFFFFF">
                                                        <div align="center">
                                                                <form id="formCriarEvento" method="post" action="eventoManagement.php?action=criarEventoAcao">
                                                                        <font size="+1"><strong>Nome do evento:</strong></font><br />
                                                                        <input type="text" required name="nome" style="width:100%; font-size:16px; height:20px" />
                                                                        <br />
                                                                        <br />
                                                                        <font size="+1"><strong>Local:</strong></font><br />
                                                                        <input type="text" required name="local" style="width:70%;font-size:16px; height:20px" />
                                                                        <br />
                                                                        <br />
                                                                        <div style="width:90%">
                                                                                <div class="float" align="left"><font size="+1"><strong>Data inicio:</strong></font><br />
                                                                                        <input type="date" name="dtinicio" id="dtinicio" style="font-size:16px; height:20px"  required />
                                                                                </div>
                                                                                <div class="float" align="left"><font size="+1"><strong>Data encerramento:</strong></font><br />
                                                                                        <input type="date" name="dtfinal" id="dtfinal" style="font-size:16px; height:20px" required />
                                                                                </div>
                                                                        </div>
                                                                        <br />
                                                                        <div style="width:100%"><br />
                                                                                <br />
                                                                                <div align="left" style="float:left; width:30%"><font size="+1"><strong>Estado:</strong></font><br />
                                                                                        <select name="estado" id="estado" style="height:25px; font-size:16px" required>
                                                                                                <option selected disabled value=" "> </option>
                                                                                           ';

                                                                                                $query = $mysqli->query("SELECT `est_nome`, `est_id` FROM `tb_estado`");
                                                                                                while ($q = $query->fetch_array()) {
                                                                                                        print '<option value="' . $q[1] . '">' . utf8_encode($q[0]) . '</option>';
                                                                                                }
                                                                                                print '
                                                                                        </select>
                                                                                </div>
                                                                                <div style="float:right; width:55%" align="left"><font size="+1"><strong>Cidade:</strong></font><br />
                                                                                        <select name="cidade" id="cidade" style="height:25px; font-size:16px" required>
                                                                                        </select>
                                                                                </div>
                                                                        </div>
                                                                        <br />
                                                                        <br />
                                                                        <br />
                                                                        <div align="center">
                                                                                <input type="submit" class="botao2" value="Criar evento!" />
                                                                                <input class="botao2" type="button" value="Cancelar" onClick="javascript: cancelar();" />
                                                                        </div>
                                                                </form>
                                                        </div>
                                                        </font> </div>
                                        </div>
                                </body>
                                </html>';
                        break;
                    case "criarEventoAcao":
                        if (isset($_POST['nome']) && isset($_POST['local']) && isset($_POST['dtinicio']) && isset($_POST['dtfinal']) && isset($_POST['cidade'])) {
                            $nome = $mysqli->real_escape_string($_POST['nome']);
                            $local = $mysqli->real_escape_string($_POST['local']);
                            $dtinicio = $mysqli->real_escape_string($_POST['dtinicio']);
                            $dtfinal = $mysqli->real_escape_string($_POST['dtfinal']);
                            $cidade = $mysqli->real_escape_string($_POST['cidade']);
                            if (new DateTime($dtfinal) >= new DateTime($dtinicio)) {
                                 $verifica = $mysqli->query("SELECT COUNT(*) FROM `tb_evento` WHERE `eve_nome` LIKE '" . $nome . "'");
                                 $verifica = $verifica->fetch_array();
                                 $id = $mysqli->query("SELECT `usu_id` FROM `tb_usuario` WHERE `usu_email` = '" . $usuario->email . "'");
                                 $id = $id->fetch_array();
                                 $id = $id[0];
                                 if ($verifica[0] == 0) {
                                      if ($mysqli->query("INSERT INTO `tb_evento` ( `eve_id` , `eve_nome` , `eve_local` , `eve_cid_id` , `eve_dtinicial` , `eve_dtfinal` , `eve_dono` ) VALUES ( NULL , '" . utf8_decode($nome) . "', '" . utf8_decode($local) . "', '" . $cidade . "', '" . $dtinicio . "', '" . $dtfinal . "', '" . $id . "' )")) {
                                           echo "<div class='sucesso'>Evento criado com sucesso!<script type='text/javascript'>document.getElementById('formCriarEvento').reset();	$('#cidade').empty();</script></div>";
                                      } else {
                                           echo "<div class='erro'>Erro ao inserir evento.</div>";
                                      }
                                 } else {
                                      echo "<div class='erro'>Este evento já existe!</div>";
                                 }
                            } else {
                                 echo "<div class='erro'>A data final não pode terminar antes da inicial.</div>";
                            }
                       }
                        break;
                    case "info":
                        $query = $mysqli->query("SELECT `cid_nome`, `eve_dtinicial`, `eve_dtfinal`, `eve_local` FROM `tb_evento`, `tb_cidade` WHERE `eve_cid_id` = `cid_id` AND `eve_nome` LIKE '" . utf8_decode($_GET['nome']) . "'");
                        if ($query->num_rows > 0) {
                            $query = $query->fetch_array();
                            print "<sup>Cidade: <strong>" . utf8_encode($query[0]) . "</strong><br />";
                            print "Local: <strong>" . utf8_encode($query[3]) . "</strong><br />";
                            if ($query[1] == $query[2]){
                                print "Data: <strong>" . $query[1] . "</strong><br />";
                            }else {
                                print "Dt. Início: <strong>" . date("d/m/Y", strtotime($query[1])) . "</strong><br />";
                                print "Dt. Encer.: <strong>" . date("d/m/Y", strtotime($query[2])) . "</strong></sup>";
                            }
                        }
                        break;
                    case "visualizar":
                        $id = $_GET['eve_id'];
                        $nome = $local = $dtini = $dtfin = $cidid = $estid = '';
                        $query = $mysqli->query("SELECT `eve_nome`, `eve_local`, `eve_dtinicial`, `eve_dtfinal`, `eve_cid_id`, `est_id` FROM `tb_evento`, `tb_cidade`, `tb_estado` WHERE `eve_cid_id` = `cid_id` AND `cid_est_id` = `est_id` AND `eve_id` = '" . $id . "'");
                        if ($query->num_rows > 0) {
                            $query = $query->fetch_array();
                            $nome = $query[0];
                            $local = $query[1];
                            $dtini = date($query[2]);
                            $dtfin = date($query[3]);
                            $cidid = $query[4];
                            $estid = $query[5];
                        }
                        print '<!DOCTYPE html>
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title>Criar evento</title>
				<link rel="stylesheet" type="text/css" href="libs/style.css">
				<script type="text/javascript">
					function reportar() {
						$("#formVisualizacao").slideToggle(300);
						$("#formReport").slideToggle(300);
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
			
			<body text="#FFFFFF">
				<div align="center"><br />
					<font size="+3" color="#FFFFFF"><strong>V</strong>isualizar <strong>E</strong>vento</font><br />
					<br />
					<div id="divResultadoInterno" style="display:none"></div>
					<div align="justify" style="width:80%"> <font color="#FFFFFF">
						<div align="center">
							<div id="formVisualizacao">
								<font size="+1"><strong>Nome do evento:</strong></font><br />
								<input type="hidden" name="arq_id" id="arq_id" value="'.$id.'" readonly />
								<input type="text" required name="nome" style="width:100%; font-size:16px; height:20px" value="'.utf8_encode($nome).'" readonly />
								<br />
								<br />
								<font size="+1"><strong>Local:</strong></font><br />
								<input type="text" required name="local" style="width:70%;font-size:16px; height:20px" value="'.utf8_encode($local).'" readonly />
								<br />
								<br />
								<div style="width:90%">
									<div class="float" align="left"><font size="+1"><strong>Data inicio:</strong></font><br />
										<input type="date" style="font-size:16px; height:20px" required value="'.$dtini.'" readonly />
									</div>
									<div class="float" align="left"><font size="+1"><strong>Data encerramento:</strong></font><br />
										<input type="date" style="font-size:16px; height:20px" required value="'.$dtfin.'" readonly />
									</div>
								</div>
								<br />
								<div style="width:100%"><br />
									<br />
									<div align="left" style="float:left; width:30%"><font size="+1"><strong>Estado:</strong></font><br />
										<select name="estado" id="estado" style="height:25px; font-size:16px" required disabled>
											';
											$query = $mysqli->query("SELECT `est_nome`, `est_id` FROM `tb_estado`");
											while ($q = $query->fetch_array()) {
												if ($q[1] == $estid)
													print '<option value="' . $q[1] . '" selected>' . utf8_encode($q[0]) . '</option>';
												else
													print '<option value="' . $q[1] . '">' . utf8_encode($q[0]) . '</option>';
											}
											print '
										</select>
									</div>
									<div style="float:right; width:55%" align="left"><font size="+1"><strong>Cidade:</strong></font><br />
										<select name="cidade" id="cidade" style="height:25px; font-size:16px" required disabled>
											';
											$queryCidade = $mysqli->query("SELECT `cid_id`, `cid_nome` FROM `tb_cidade` WHERE `cid_est_id` = " . $estid);
											while ($cid = $queryCidade->fetch_array()) {
												if ($cid[0] == $cidid) {
													print '<option value="' . $cid[0] . '" selected>' . utf8_encode($cid[1]) . '</option>';
												} else {
													print '<option value="' . $cid[0] . '">' . utf8_encode($cid[1]) . '</option>';
												}
											}
											print '
										</select>
									</div>
								</div>
								<br />
								<br />
								<br />
								<div align="center">
									<input class="botao2" type="button" value="<- Voltar" onClick="javascript: menuInterno(\'ev\');" style="width:35%; height:40px" />
									<input type="button" class="botao2" value="Reportar" style="width:45%; height:40px" onClick="reportar();" />        
								</div>
							</div>
							<div id="formReport" style="display:none">
								<input type="hidden" value="evento" name="tipoReport" id="tipoReport" />
								<font color="#FFFFFF" size="+1">Obs.:</font><br />
								<textarea id="obsReport" style="max-height:100px; max-width:80%; height:60px; width:50%"></textarea>
								<br />
								<br />
								<input class="botao2" type="button" value="Cancelar" onClick="javascript: reportar();"  style="width:35%; height:40px" /> <input class="botao2" type="button" value="Enviar report" onClick="javascript: enviarReport();" style="width:45%; height:40px" />
			
							</div>
						</div>
						</font> </div>
				</div>
			</body>
			</html>';
                        break;
            default:
                print '<!DOCTYPE html>
                        <head>
                            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                            <title>Eventos</title>
                            <link rel="stylesheet" type="text/css" href="libs/style.css">
                        </head>						
                        <body text="#FFFFFF">
                            <div align="center"><br />
                                <font size="+3" color="#FFFFFF"><strong>E</strong>ventos</font><br /><br />
                                <div id="divResultadoInterno" style="display:none"></div>
                                <br />
                                <div align="justify" style="width:80%">
                                    <font color="#FFFFFF">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nesta seção, estão listados os eventos cujo qual seus arquivos estão associados ou que você seja o dono. É possivel compartilhar o arquivo, após listar os arquivos do evento.
                                    </font>
                                    <br /><br />
                                    <div align="right">
                                        <span class="botao"><a href="javascript: abrirInterno(\'eventoManagement\', \'criarEvento\');"><img src="imagens/icon-adicionar.png" width="20" /> Criar evento</a></span>
                                    </div>
                                    <br />
                                    <div id="divMeusEventos"><script type="text/javascript">$("#divMeusEventos").load(\'eventoManagement.php?action=lista\');</script></div>
                                </div>
                            </div>
                        </body>
                        </html>';
                break;
        }
    }
}
?>

