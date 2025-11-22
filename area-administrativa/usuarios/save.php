<?php

include('../verifyConnection.php');
$pageActive = "Usuários";

$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
    $type = base64_encode("error");
    $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
    header('Location: ../?msg=' . $msg . '&type=' . $type);
    exit;
}
include('../connectDb.php');

$linkRequest = explode('/', $_SERVER["HTTP_REFERER"]);
$linkRequest = end($linkRequest);

if ($_SERVER["HTTP_HOST"] == $linkUrl) {

    if (empty($_POST["pkId"])) {
        $query = "
        INSERT INTO rv_administradores (nome,fkPerfil,email,senha) VALUES
        (
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtNome"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtPerfil"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtEmail"])) . "',
        '" . mysqli_real_escape_string($connecta, sha1(trim($_POST["txtSenha"]))) . "'
        )";
    } else {
        $query = "
        UPDATE rv_administradores SET
        nome = '" . mysqli_real_escape_string($connecta, trim($_POST["txtNome"])) . "',
        fkPerfil = '" . mysqli_real_escape_string($connecta, trim($_POST["txtPerfil"])) . "',
        email = '" . mysqli_real_escape_string($connecta, trim($_POST["txtEmail"])) . "',
        senha = IF(LENGTH('" . trim($_POST["txtSenha"]) . "') > 0, '" . mysqli_real_escape_string($connecta, sha1(trim($_POST["txtSenha"]))) . "', senha)
        WHERE pkId = " . base64_decode($_POST["pkId"]);
    }
    // echo $query;exit;
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
