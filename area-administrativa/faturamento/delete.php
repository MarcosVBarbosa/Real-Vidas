<?php

include('../verifyConnection.php');
$pageActive = "Faturamento";
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
    $type = base64_encode("error");
    $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
    header('Location: ./?type='.$type.'&msg='.$msg);
    exit;
}
include('../connectDb.php');

$linkRequest = explode('/', $_SERVER["HTTP_REFERER"]);
$linkRequest = end($linkRequest);

if ($_SERVER["HTTP_HOST"] == $linkUrl) {
    if (!empty($_GET["ref"]) and !empty($_GET["refFaturamento"])) {
        $delete = unlink("arquivos/" . base64_decode($_GET["ref"]));
        mysqli_query($connecta, "UPDATE rv_faturamento SET " . base64_decode($_GET["table"]) . " = '' WHERE pkId = " . base64_decode($_GET["refFaturamento"]));
    }

    if ($delete) {
        $type = base64_encode("success");
        $msg = base64_encode("Arquivo removido com sucesso!");
    } else {
        $type = base64_encode("danger");
        $msg = base64_encode("Falha ao salvar o registro! Por favor tente mais tarde.");
    }
}

header('Location: insert.php?ref=' . $_GET["refFaturamento"] . '&type=' . $type . '&msg=' . $msg);
exit;
