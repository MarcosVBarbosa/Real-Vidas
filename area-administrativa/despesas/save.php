<?php
include('../verifyConnection.php');
$pageActive = "Estoque/Custos";
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
    $type = base64_encode("error");
    $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
    header('Location: https://' . $_SERVER['HTTP_HOST'] . '/area-administrativa-developer/index.php?msg=' . $msg . '&type=' . $type);
    exit;
}

include('../connectDb.php');

$linkRequest = explode('/', $_SERVER["HTTP_REFERER"]);
$linkRequest = end($linkRequest);

if ($_SERVER["HTTP_HOST"] == $linkUrl) {

    if (empty($_POST["pkId"])) {
        $query = "INSERT INTO rv_despesas (nome,qtde,valor) VALUES
        ('" . mysqli_real_escape_string($connecta, trim($_POST["txtNome"])) . "',
        ('" . mysqli_real_escape_string($connecta, trim($_POST["txtQtde"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtValor"])) . "'
        )";
    } else {
        $query = "UPDATE rv_despesas SET
        nome = '" . mysqli_real_escape_string($connecta, trim($_POST["txtNome"])) . "',
        qtde = '" . mysqli_real_escape_string($connecta, trim($_POST["txtQtde"])) . "',
        valor = '" . mysqli_real_escape_string($connecta, trim($_POST["txtValor"])) . "'
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
