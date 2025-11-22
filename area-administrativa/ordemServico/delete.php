<?php
include('../verifyConnection.php');
$pageActive = "Ordem Serviço";
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
    $type = base64_encode("error");
    $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
    header('Location: ./?type=' . $type . '&msg=' . $msg);
    exit;
}

include('../connectDb.php');

// (opcional) Verificação de origem da requisição
$linkRequest = explode('/', $_SERVER["HTTP_REFERER"]);
$linkRequest = end($linkRequest);

// $linkUrl precisa ser definido. Aqui um exemplo:
$linkUrl = $_SERVER["HTTP_HOST"]; // ajuste se necessário

if ($_SERVER["HTTP_HOST"] == $linkUrl) {
    if (!empty($_GET["ref"]) && !empty($_GET["refOrdemServico"])) {

        $arquivo = base64_decode($_GET["ref"]);
        $ordemId = base64_decode($_GET["refOrdemServico"]);

        if (is_file("arquivos/" . $arquivo)) {
            unlink("arquivos/" . $arquivo);
        }

        $sql = "
            UPDATE rv_ordemServico SET 
                caminhoFicha = IF(caminhoFicha = '$arquivo', '', caminhoFicha),
                caminhoGuia = IF(caminhoGuia = '$arquivo', '', caminhoGuia)
            WHERE pkId = $ordemId
        ";

        if (mysqli_query($connecta, $sql)) {
            $type = base64_encode("success");
            $msg = base64_encode("Arquivo removido com sucesso!");
        } else {
            $type = base64_encode("danger");
            $msg = base64_encode("Falha ao salvar o registro! Por favor tente mais tarde.");
        }
    } else {
        $type = base64_encode("danger");
        $msg = base64_encode("Parâmetros ausentes ou inválidos.");
    }
} else {
    $type = base64_encode("danger");
    $msg = base64_encode("Requisição inválida.");
}

header('Location: insert.php?ref=' . $_GET["refOrdemServico"] . '&type=' . $type . '&msg=' . $msg);
exit;
