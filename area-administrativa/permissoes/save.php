<?php
include('../verifyConnection.php');
$pageActive = 'Perfil';
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
        INSERT INTO rv_acesso (fkPerfil,acesso,permissoes) VALUES
        (
        '" . mysqli_real_escape_string($connecta, trim(base64_decode($_POST["pkPerfil"])))  . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["acesso"])) . "',
        '" .  mysqli_real_escape_string($connecta, json_encode($_POST["multPermissoes"])) . "'
        )";
    } else {
        $query = "
        UPDATE rv_acesso SET
        fkPerfil = '" . mysqli_real_escape_string($connecta, trim(base64_decode($_POST["pkPerfil"]))) . "',
        acesso = '" . mysqli_real_escape_string($connecta, trim($_POST["acesso"])) . "',
        permissoes = '" .  mysqli_real_escape_string($connecta, json_encode($_POST["multPermissoes"])) . "'
        WHERE pkId = " . base64_decode($_POST["pkId"]);
    }

    $result = mysqli_query($connecta, $query);

    if ($result) {
        $type = base64_encode("success");
        $msg = base64_encode("Registro salvo com sucesso!");
    } else {
        if (mysqli_errno($connecta) == 1062) { // Código de erro para duplicidade
            $type = base64_encode("warning");
            $msg = base64_encode("Registro duplicado! Verifique os dados e tente novamente.");
        } else {
            $type = base64_encode("danger");
            $msg = base64_encode("Falha ao salvar o registro! Por favor tente mais tarde.");
        }
    }
}

header('Location: ./?ref=' . $_POST["pkPerfil"] . '&type=' . $type . '&msg=' . $msg);
exit;
