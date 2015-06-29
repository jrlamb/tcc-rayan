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

        switch ($varAction) {
            case "compararAcao":
                $id = $mysqli->query("SELECT `usu_id` FROM `tb_usuario` WHERE `usu_email` = '" . $usuario->email . "'");
                $id = $id->fetch_array();
                $id = $id[0];

                $output_dir = "tempXML/";
                if (isset($_FILES["myfile"])) {
                    $ret = array();

                    $error = $_FILES["myfile"]["error"];
                    //You need to handle  both cases
                    //If Any browser does not support serializing of multiple files using FormData() 
                    if (!is_array($_FILES["myfile"]["name"])) { //single file
                        $fileName = $_FILES["myfile"]["name"];
                        move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir . $id . '_' . $fileName);
                        $_SESSION['arquivoTemporario'] = $id . '_' . $fileName;
                        $ret[] = $fileName;
                    } else {  //Multiple files, file[]
                        $fileCount = count($_FILES["myfile"]["name"]);
                        for ($i = 0; $i < $fileCount; $i++) {
                            $fileName = $_FILES["myfile"]["name"][$i];
                            move_uploaded_file($_FILES["myfile"]["tmp_name"][$i], $output_dir . $fileName);
                            $ret[] = $fileName;
                        }
                    }
                    echo json_encode($ret);
                }
                break;
            case "resultado":
                print '<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Resultado da comparação</title>
    <link rel="stylesheet" type="text/css" href="libs/style.css" />
</head>

<body>
    <div align="center"><br />
        <font size="+3" color="#FFFFFF"><strong>C</strong>omparar com o <em>lattes</em> - Resultado</font><br /><br /><br />
        <div id="divComparacao">';

                $uploaddir = 'tempXML/';

                $producoes = array();
                $noSistema = array();

                $query = $mysqli->query("SELECT `arq_titulo` FROM `tb_arquivo`, `tb_usuario`, `tb_sincronizado` WHERE `usu_id` = `sinc_usu_id` AND `arq_id` = `sinc_arq_id` AND `usu_email` = '" . $usuario->email . "'");

                try {
                    while ($k = $query->fetch_array()) {
                        $noSistema[] = $k[0];
                    }
                    libxml_use_internal_errors(true);
                    $temp = file_get_contents($uploaddir . $_SESSION['arquivoTemporario']);
                    $XmlObj = simplexml_load_string($temp);
                    if ($XmlObj !== false) {
                        foreach ($XmlObj->{"PRODUCAO-BIBLIOGRAFICA"}->{"TRABALHOS-EM-EVENTOS"}->{"TRABALHO-EM-EVENTOS"} as $txt) {
                            $producoes[] = utf8_decode($txt->{"DADOS-BASICOS-DO-TRABALHO"}["TITULO-DO-TRABALHO"]);
                        }
                        foreach ($XmlObj->{"PRODUCAO-BIBLIOGRAFICA"}->{"ARTIGOS-PUBLICADOS"}->{"ARTIGO-PUBLICADO"} as $txt) {
                            $producoes[] = utf8_decode($txt->{"DADOS-BASICOS-DO-ARTIGO"}["TITULO-DO-ARTIGO"]);
                        }
                        foreach ($XmlObj->{"PRODUCAO-BIBLIOGRAFICA"}->{"LIVROS-E-CAPITULOS"}->{"LIVROS-PUBLICADOS-OU-ORGANIZADOS"}->{"LIVRO-PUBLICADO-OU-ORGANIZADO"} as $txt) {
                            $producoes[] = utf8_decode($txt->{"DADOS-BASICOS-DO-LIVRO"}["TITULO-DO-LIVRO"]);
                        }
                        foreach ($XmlObj->{"PRODUCAO-BIBLIOGRAFICA"}->{"TEXTOS-EM-JORNAIS-OU-REVISTAS"}->{"TEXTO-EM-JORNAL-OU-REVISTA"} as $txt) {
                            $producoes[] = utf8_decode($txt->{"DADOS-BASICOS-DO-TEXTO"}["TITULO-DO-TEXTO"]);
                        }

                        print "<table width='100%' cellspacing='0'><tr class='topoTabela'><td>Título</td><td width='20'></td></tr>";
                        $p = 0;
                        foreach ($producoes as $prod) {
                            if ($p == 0) {
                                print "<tr class='par'>";
                                $p++;
                            } else {
                                print "<tr class='impar'>";
                                $p--;
                            }
                            print "<td class='separador'>" . utf8_encode($mysqli->real_escape_string($prod)) . "</td>";
                            if (in_array($prod, $noSistema)) {
                                print "<td align='center'><img border='0' src='imagens/icon-ok.png' width='15' title='OK' /></td>";
                            } else {
                                print "<td align='center'><img border='0' src='imagens/icon-faltando.png' width='15' title='Não consta no sistema' /></td>";
                            }
                            print "</tr>";
                        }
                        print "</table>";
                    } else {
                        print '<div class="alert"><br /><strong>Erro ao realizar comparação - arquivo inconsistente.</strong><br /> Por favor, tente novamente com um novo arquivo.</div>';
                    }
                } catch (Exception $e) {
                    print '<tr class="par">Erro ao realizar chamada.</tr>';
                    //throw $e;
                }
                print "</table>";
                print '</div></body></html>';
                break;
            default:
                print '
			<!DOCTYPE html>
			<head>
                            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                            <title>Comparar Curriculum</title>
                            <script type="text/javascript" src="libs/jquery.min.fup.js"></script>
                            <script type="text/javascript" src="libs/jquery.uploadfile_comparar.js"></script>
                            <link rel="stylesheet" type="text/css" href="libs/style.css" />
                            <link rel="stylesheet" type="text/css" href="libs/uploadfile.css" />

                            <script type="text/javascript">
                                $(document).ready(function(e) {
                                    $("#formComparar").submit(function() {
                                        var thistarget = "#conteudoInterno";
                                        $(thistarget).fadeOut(300);
                                        jQuery.ajax({
                                            data: $(this).serialize(),
                                            url: this.action,
                                            type: this.method,
                                            success: function(results) {
                                                    $(thistarget).html(results).fadeIn(300);
                                                    //$("#divForm").slideToggle(400, function(){$(thistarget).fadeIn(400);});					 
                                            }
                                        })
                                        return false;
                                    });
                                    var uploadObj = $("#fileuploader").uploadFile({
                                        url: "compararManagement.php?action=compararAcao",
                                        multiple: false,
                                        autoSubmit: false,
                                        fileName: "myfile",
                                        maxFileSize: 1024 * 2000,
                                        showStatusAfterSuccess: false,
                                        dragDropStr: "<div align=\'center\'><br /><br /><font size=\'+1\'><strong>Para selecionar um arquivo clique no botão \'Upload\'<br /> <em>ou</em> <br />arraste o arquivo XML nesta área.</strong></font></div>",
                                        abortStr: "Abortar",
                                        cancelStr: "Remover",
                                        doneStr: "Concluído!",
                                        extErrorStr: " não pode ser carregado. <br />São permitidos apenas arquivos com a extensão: ",
                                        sizeErrorStr: "O arquivo inserido é muito grande!",
                                        uploadErrorStr: "Erro ao carregar arquivo."
                                    });
                                    $("#startUpload").click(function(){
                                        uploadObj.startUpload();
                                    });
                                });
                            </script>
			</head>
			
			<body text="#FFFFFF"><br />
                            <font size="+3" color="#FFFFFF"><strong>C</strong>omparar com o <em>lattes</em></font><br /><br />
                            <div id="divResultadoInterno" style="display:none" align="center"></div>
                            <div id="divForm" align="center">
                                <form method="post" action="compararManagement.php?action=resultado" id="formComparar">
                                    <div id="fileuploader" style="height:80%" align="center">Upload</div><br /><br />
                                    <button id="startUpload" class="botao2" style="display:none">Enviar para comparação!</button>
                                </form>
                            </div>
			</body>
			</html>';
                break;
        }
    }
}
?>