<?php include('class/constants.php'); ?>
<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title>Sistema de Compartilhamento de Artigos acadêmicos</title>
    <link rel="icon" type="image/ico" href="imagens/favicon.ico">
    <link rel="stylesheet" type="text/css" href="libs/style.css">
    <script src="libs/jquery.js" type="text/javascript" language="javascript"></script>
    <script type="text/javascript" language="javascript">
        function abrir(s, p) {
            $("#conteudoInicial").load(s + ".php" + p);
        }

        function mostrarResultado(t) {
            if (t == "S") {
                $("#divResultado").html("Usuário saiu com sucesso.");
                $("#divResultado").css("background-color", "#06C");
                $("#divResultado").css("color", "#FFFFFF");
                $("#divResultado").slideToggle(400, function() {
                    setTimeout(function() {
                        $("#divResultado").slideToggle(400);
                        $('#conteudoCentral').animate({width: '500px', height: '420px'}, 500);
                        $('#divAcesso').load('menu.php');
                        $('#conteudoInicial').load('conteudoInicial.php');
                    }, 2000);
                });
            }
        }
    </script>

</head>

<body onLoad="<?php
if (!isset($_GET['A'])) {
    print "javascript: $('#conteudoCentral').animate({width:'500px'}, 500);";
}
?>">
    <div align="center">
        <div class="divTitulo">    
            <div align="center" style="float:left; width:20%"><img class="logo" src="imagens/logo.png" width="100" height="100" /></div>
            <div style="float:left; width:80%"><font size="+2"><br />Sistema de compartilhamento de artigos acadêmicos</font></div>
        </div>
        <div class="conteudoCentral" id="conteudoCentral">
            <div class="acesso" id="divAcesso"><a href="#" onClick="javascript: window.open('acessar.php?l', 'acessar', 'width=500,height=400, resizable=no, toolbar=no, fullscreen=no, location=no, top=200, left=350');">Acessar o sistema</a></div>
            <div id="divResultado" style="display:none"></div>        	
            <div class="conteudoInicial" id="conteudoInicial" align="center"><br />
                <font size="+3" color="#FFFFFF"><strong>Bem-vindo</strong></font><br /><br />
                <div align="justify" class="conteudo"><font color="#FFFFFF" size="+1">&nbsp;&nbsp;&nbsp;&nbsp;Este é um novo sistema de compartilhamento de arquivos do meio acadêmico, podendo também ser chamado de rede social de arquivos acadêmicos.</font></div><br />
                <div align="justify" class="conteudo"><font color="#FFFFFF" size="+1">&nbsp;&nbsp;&nbsp;&nbsp;Tendo como ideia inicial, o sistema tem por objetivo unir em um único ponto, a realização e organização da troca dos arquivos, tornando fácil o gerenciamento e compartilhamento dos mesmos.</font></div>     <br /><br />          
                <div align="justify" class="conteudo"><font color="#FFFFFF" size="+1"><strong>O uso do sistema é inteiramente gratuito, caso encontre alguém ou alguma página vendendo: acesso, arquivo ou qualquer coisa relacionada ao sistema, denuncie contatando-nos!</strong></font></div>
            </div>
        </div>
        <div align="center" id="divRodape">
            <div id="divRodapeDentro" align="center"><font size="2"><a href="mailto:chemin.rayan@gmail.com?Subject=Contato pessoal" target="_blank"><strong>C</strong>ontato</a> <font color="#CCC" size="2"><strong>|</strong></font> <a href="mailto:chemin.rayan@gmail.com?Subject=Problema no sistema" target="_blank"><strong>P</strong>roblema</a> <font color="#CCC" size="2"><strong>|</strong></font> <a href="mailto:chemin.rayan@gmail.com?Subject=Sugestão para o sistema" target="_blank"><strong>S</strong>ugestão </font> </a> <font color="#CCC" size="2"> <strong> | Todos os direitos reservados &reg;</strong> </font> </div>
        </div>
    </div>
<?php

require("class/classes.class.php");

$security = Security::getInstance();

    if (isset($_GET['C'])) {
        echo '<script type="text/javascript">
		$("#divResultado").html("Usuário cancelou o acesso.");
		$("#divResultado").css("background-color", "#F60");
		$("#divResultado").css("color", "#FFFFFF");
		$("#divResultado").slideToggle(400, function(){
                    setTimeout(function(){
                        $("#divResultado").slideToggle(400);
                    }, 2000);
		});
		</script>';
    } elseif (isset($_GET['A']) && $security->isLogged()) {
        echo '<script type="text/javascript">
		var altura = 520;
		if(screen.height <= 768){
                    altura = 500;
		}
		$("#divResultado").html("Usuário autenticado com sucesso.");
		$("#divResultado").css("background-color", "#390");
		$("#divResultado").css("color", "#FFFFFF");
		$("#divResultado").slideToggle(400, function(){
                    setTimeout(function(){
                        $("#divResultado").slideToggle(400);
                        $("#conteudoCentral").animate({width:"800px", height:altura + "px"}, 500);
                        $("#divAcesso").load("menu.php?A");
                    }, 2000);
                });
                </script>';
    } elseif (isset($_GET['S']) && $security->isLogged()) {
        $security->setUnlogged();
    }
    ?>
</body>
</html>