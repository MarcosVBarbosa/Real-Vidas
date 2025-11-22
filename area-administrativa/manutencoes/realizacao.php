<?php

include('../verifyConnection.php');
$pageActive = 'Manutenções';
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

    $query = "UPDATE rv_manutencoes SET
    realizado = '" . mysqli_real_escape_string($connecta, trim($_GET["realizado"])) . "'
    WHERE pkId = " . base64_decode($_GET["ref"]);

    //echo $query;exit;
    $result = mysqli_query($connecta, $query);
    if ($result) {
        if ($_GET["realizado"] == "S") {
            $type = base64_encode("success");
            $msg = base64_encode("Manutenção realizada!");
        } else {
            $type = base64_encode("info");
            $msg = base64_encode("A manutenção ainda não foi realizada!");
        }
    } else {
        $type = base64_encode("danger");
        $msg = base64_encode("Falha ao salvar o registro! Por favor tente mais tarde.");
    }
}

header('Location: ./?type=' . $type . '&msg=' . $msg);
exit;
