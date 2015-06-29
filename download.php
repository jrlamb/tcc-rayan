<?php
require('class/classes.class.php');
require("class/conexao.class.php");

$db = Database::getInstance();
$security = Security::getInstance();
$mysqli = $db->getConnection();
$usuario = $security->getUsuario();

if (!$security->isLogged()) {
    header('Location: index.php');
}

$resultado = $mysqli->query("SELECT `usu_cod_sinc` FROM `tb_usuario` WHERE `usu_email` = '" . $usuario->email . "'");
$a = $resultado->fetch_array();
?><!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="libs/style.css">
    <title>Obter o software</title>
    <script type="text/javascript">
        function download(o) {
            o.disabled = true;
            o.value = "O download irá iniciar em instantes.";
            //$("#divResultadoInterno").load('setup.exe');
            $("#downloadWindow").attr('src', 'setup.exe');
        }
        function novo(o) {
            if (confirm("Você tem certeza que quer gerar um novo código?")) {
                o.disabled = true;
                o.value = "Novo código gerado!";
                $("#divResultadoInterno").load('gerarCodigo.php?A&Atualizar', function() {
                    if (!$("#divResultadoInterno").is(":visible"))
                        $("#divResultadoInterno").slideToggle(400);
                });
            }
        }
    </script>
</head>


<body text="#FFFFFF">
    <div align="center">
        <font size="+3" color="#FFFFFF"><strong>O</strong>bter o <strong>S</strong>oftware</font><br /><br />
        <div align="justify" style="width:80%">
            <font color="#FFFFFF">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Para o sistema funcionar adequadamente, é necessário a instalação do software de serviço. O software permite o gerenciamento dos arquivos por parte do sistema, sem necessidade de alterações manuais.
            </font> 
        </div><br />
        <div align="justify" style="width:80%">
            <font color="#FFFFFF">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Após a instalação, execute o software, e em seguida insira o código gerado após clicar no botão abaixo. Dessa forma, o sistema e o software entram em sincronismo, deixando sempre atualizado os arquivos.    
            </font>        
        </div>
        <div align="center"><br /><font color="#FFFFFF" size="3"> <strong>Ao momento o software está disponível apenas para<br /> plataforma Microsoft&reg; Windows na versão XP ou superior</strong></font></div>
        <br /><div align="center"><font color="#FFFFFF" size="2"> Obs.: O <a href="http://www.java.com" target="_blank"><strong>Java</strong></a> deverá estar instalado, caso contrario o software não funcionará.</font></div><br /><br />
        <div align="center">
            <input class="botao2" type="button" style="width:50%; height:60px" onClick="javascript: download(this);" value="Clique aqui para realizar o download" />
            <input class="botao2" type="button" style="width:30%; height:60px" onclick="novo(this);" value="Solicitar novo código" <?php echo is_null($a[0]) ? "disabled" : "" ?> />
            <div id="divResultadoInterno" style="display:none"></div>
            <iframe name="downloadWindow" id="downloadWindow" allowtransparency="1" frameborder="0" width="1" height="1" style="display:none"></iframe>
        </div>
        <script>
            $("#divResultadoInterno").load('gerarCodigo.php?A', function() {
                if (!$("#divResultadoInterno").is(":visible"))
                    $("#divResultadoInterno").slideToggle(400);
            });
        </script>
</body>

</html>