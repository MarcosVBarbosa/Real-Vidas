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

    if (empty($_POST["pkId"])) {
        $query = "
        INSERT INTO rv_manutencoes (data,nome,fkVTR,kmAtual,valor,valorPeca,kmLimite,nfServico,nfPeca) VALUES
        (
            '" . mysqli_real_escape_string($connecta, trim($_POST["txtData"])) . "',
            '" . mysqli_real_escape_string($connecta, trim($_POST["txtNome"])) . "',
            '" . mysqli_real_escape_string($connecta, trim($_POST["txtVTR"])) . "',
            '" . mysqli_real_escape_string($connecta, trim($_POST["txtKmAtual"])) . "',
            '" . mysqli_real_escape_string($connecta, trim($_POST["txtValor"])) . "',
            '" . mysqli_real_escape_string($connecta, trim($_POST["txtValorPeca"])) . "',
            '" . mysqli_real_escape_string($connecta, trim($_POST["txtKmLimite"])) . "',
            '" . mysqli_real_escape_string($connecta, trim($_POST["txtNFServico"])) . "',
            '" . mysqli_real_escape_string($connecta, trim($_POST["txtNFPeca"])) . "'
        )";
    } else {
        $query = "
        UPDATE rv_manutencoes SET
        data = '" . mysqli_real_escape_string($connecta, trim($_POST["txtData"])) . "',
        nome = '" . mysqli_real_escape_string($connecta, trim($_POST["txtNome"])) . "',
        fkVTR = '" . mysqli_real_escape_string($connecta, trim($_POST["txtVTR"])) . "',
        kmAtual = '" . mysqli_real_escape_string($connecta, trim($_POST["txtKmAtual"])) . "',
        kmLimite = '" . mysqli_real_escape_string($connecta, trim($_POST["txtKmLimite"])) . "',
        valor = '" . mysqli_real_escape_string($connecta, trim($_POST["txtValor"])) . "',
        nfServico = '" . mysqli_real_escape_string($connecta, trim($_POST["txtNFServico"])) . "',
        valorPeca = '" . mysqli_real_escape_string($connecta, trim($_POST["txtValorPeca"])) . "',
        nfPeca = '" . mysqli_real_escape_string($connecta, trim($_POST["txtNFPeca"])) . "'
        WHERE pkId = " . base64_decode($_POST["pkId"]);
    }
    //echo $query;exit;
    $result = mysqli_query($connecta, $query);
    if ($result) {
        $type = base64_encode("success");
        $msg = base64_encode("Registro salvo com sucesso!");
    } else {
        $type = base64_encode("danger");
        $msg = base64_encode("Falha ao salvar o registro! Por favor tente mais tarde.");
    }
}

header('Location: ./?type=' . $type . '&msg=' . $msg);
exit;
